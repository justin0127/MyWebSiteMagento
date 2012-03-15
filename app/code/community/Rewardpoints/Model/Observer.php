<?php
/**
 * J2T RewardsPoint2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@j2t-design.com so we can send you a copy immediately.
 *
 * @category   Magento extension
 * @package    RewardsPoint2
 * @copyright  Copyright (c) 2009 J2T DESIGN. (http://www.j2t-design.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
	class Rewardpoints_Model_Observer extends Mage_Core_Model_Abstract {
		
		public function recordPointsUponRegistration($observer){
                    if (Mage::getStoreConfig('rewardpoints/default/registration_points', Mage::app()->getStore()->getId()) > 0){
                        //check if points already earned
                        $customerId = $observer->getEvent()->getCustomer()->getEntityId();
                        $points = Mage::getStoreConfig('rewardpoints/default/registration_points', Mage::app()->getStore()->getId());
                        //$orderId = -2;
                        $this->recordPoints($points, $customerId, Rewardpoints_Model_Stats::TYPE_POINTS_ADMIN, false);
                    }
                }

                public function recordPointsForOrderEvent($observer) {

                    $orderId = Mage::getSingleton('checkout/type_onepage')
                                    ->getCheckout()->getLastOrderId();
                    $order = Mage::getModel('sales/order')->load($orderId);
                    $items = $order->getAllItems();
                    
                    $customerId = $order->getCustomerId();
                    if (!$customerId){
                            return false;
                    }

                    $orderId = $order->getIncrementId();
                    $rewardPoints = 0;
                    $prodIds = array();
                    foreach ($items as $_item) {
                        $prodIds[] = $_item->getProductId();
                    }
                    
                    $prod = Mage::getResourceModel('catalog/product_collection')
                                                    ->addAttributeToSelect('reward_points')
                                                    ->addIdFilter($prodIds);

                    $rules = Mage::getModel('rewardpoints/rules')->getPointsByRule();
                    $cart_amount = 0;

                    foreach ($items as $_item) {
                        $_product = $prod->getItemById($_item->getProductId());
                        $product_points = $_product->getData('reward_points');

                        if ($product_points > 0){
                            $rewardPoints += $product_points * $_item->getQtyOrdered();
                        } else {
                            $price = $_item->getRowTotal() + $_item->getTaxAmount() - $_item->getDiscountAmount();
                            $rewardPoints += (int)Mage::getStoreConfig('rewardpoints/default/money_points', Mage::app()->getStore()->getId()) * $price;
                        }
                        $cart_amount += $_item->getRowTotal() + $_item->getTaxAmount() - $_item->getDiscountAmount();
                        if ($rules != array()){
                            foreach ($rules as $rule){
                                if ($rule['type'] == Rewardpoints_Model_Rules::TARGET_SKU){
                                    if ($_product->getSku() == $rule['test_value']){
                                        $rewardPoints += (int)$rule['points'] * $_item->getQty();
                                    }
                                }
                            }
                        }
                    }

                    if ($cart_amount > 0){
                        if ($rules != array()){
                            foreach ($rules as $rule){
                                if ($rule['type'] == Rewardpoints_Model_Rules::TARGET_CART){
                                    switch ($rule['operator']){
                                        case Rewardpoints_Model_Rules::OPERATOR_1: // =
                                            if ($cart_amount == $rule['test_value']){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                        case Rewardpoints_Model_Rules::OPERATOR_2: // <
                                            if ($cart_amount < $rule['test_value']){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                        case Rewardpoints_Model_Rules::OPERATOR_3: // <=
                                            if ($cart_amount <= $rule['test_value']){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                        case Rewardpoints_Model_Rules::OPERATOR_4: // >
                                            if ($cart_amount > $rule['test_value']){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                        case Rewardpoints_Model_Rules::OPERATOR_5: // >=
                                            if ($cart_amount >= $rule['test_value']){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                        case Rewardpoints_Model_Rules::OPERATOR_6: // Between
                                            $test_values = explode(";",$rule['test_value']);
                                            if ($cart_amount >= (int)$test_values[0] && $cart_amount <= (int)$test_values[1]){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                    }

                                }
                            }
                        }
                    }



                    if (Mage::getStoreConfig('rewardpoints/default/math_method', Mage::app()->getStore()->getId()) == 1){
                        $rewardPoints = round($rewardPoints);
                    } else {
                        $rewardPoints = floor($rewardPoints);
                    }

                    //record points for item into db
                    if ($rewardPoints > 0){
                        $this->recordPoints($rewardPoints, $customerId, $orderId);
                    }

                    //subtract points for this order
                    $points_apply = (int) Mage::helper('rewardpoints/event')->getCreditPoints();
                    if ($points_apply > 0){
                        $this->useCouponPoints($points_apply, $customerId, $orderId);
                    }

                    $this->sales_order_success_referral($order);
			
		}
		public function useCouponPoints($pointsAmt, $customerId, $orderId) {
		
                    $points = Mage::getModel('rewardpoints/account')
                                            ->load($customerId);
                    $points->subtractPoints($pointsAmt);
                    $points->save($orderId, true);
                    Mage::helper('rewardpoints/event')->setCreditPoints(0);
		}
		
		public function recordPoints($pointsInt, $customerId, $orderId, $no_check = true) {
                    $points = Mage::getModel('rewardpoints/account')
                                       ->load($customerId);
                    $points->addPoints($pointsInt);
                    $points->save($orderId, $no_check);
		}


                public function sales_order_success_referral($order)
                {
                    $rewardPoints = Mage::getStoreConfig('rewardpoints/default/referral_points', Mage::app()->getStore()->getId());
                    $rewardPointsChild = Mage::getStoreConfig('rewardpoints/default/referral_child_points', Mage::app()->getStore()->getId());
                    
                    if ($rewardPoints > 0 || $rewardPointsChild > 0){
                        //$order = $observer->getEvent()->getInvoice()->getOrder();
                        $referralModel = Mage::getModel('rewardpoints/referral');
                        if ($referralModel->isSubscribed($order->getCustomerEmail())) {
                            if (!$referralModel->isConfirmed($order->getCustomerEmail())) {
                                $referralModel->loadByEmail($order->getCustomerEmail());
                                $referralModel->setData('rewardpoints_referral_status', true);
                                $referralModel->setData('rewardpoints_referral_child_id', $order->getCustomerId());
                                $referralModel->save();

                                $parent = Mage::getModel('customer/customer')->load($referralModel->getData('rewardpoints_referral_parent_id'));
                                $child    = Mage::getModel('customer/customer')->load($referralModel->getData('rewardpoints_referral_child_id'));                                

                                try {
                                    if ($rewardPoints > 0){
                                        $reward_points = Mage::getModel('rewardpoints/account');
                                        $reward_points->saveCheckedOrder($order->getIncrementId(), $referralModel->getData('rewardpoints_referral_parent_id'), $order->getStoreId(), $rewardPoints, $referralModel->getData('rewardpoints_referral_id'), true);
                                    }

                                    if ($rewardPointsChild > 0){
                                        $reward_points2 = Mage::getModel('rewardpoints/account');
                                        $reward_points2->saveCheckedOrder($order->getIncrementId(), $referralModel->getData('rewardpoints_referral_child_id'), $order->getStoreId(), $rewardPointsChild, $referralModel->getData('rewardpoints_referral_id'), true);
                                    }

                                } catch (Exception $e) {
                                    //Mage::getSingleton('session')->addError($e->getMessage());
                                }
                                $referralModel->sendConfirmation($parent, $child, $parent->getEmail());
                            }
                        }
                    }
                }

                public function sales_order_invoice_pay($observer)
                {
                    $rewardPoints = Mage::getStoreConfig('rewardpoints/default/referral_points', Mage::app()->getStore()->getId());
                    $rewardPointsChild = Mage::getStoreConfig('rewardpoints/default/referral_child_points', Mage::app()->getStore()->getId());
                    if ($rewardPoints > 0 || $rewardPointsChild > 0){
                        $order = $observer->getEvent()->getInvoice()->getOrder();
                        $referralModel = Mage::getModel('rewardpoints/referral');
                        if ($referralModel->isSubscribed($order->getCustomerEmail())) {
                            if (!$referralModel->isConfirmed($order->getCustomerEmail())) {
                                $referralModel->loadByEmail($order->getCustomerEmail());
                                $referralModel->setData('rewardpoints_referral_status', true);
                                $referralModel->setData('rewardpoints_referral_child_id', $order->getCustomerId());
                                $referralModel->save();

                                $parent = Mage::getModel('customer/customer')->load($referralModel->getData('rewardpoints_referral_parent_id'));
                                $child    = Mage::getModel('customer/customer')->load($referralModel->getData('rewardpoints_referral_child_id'));
                                $referralModel->sendConfirmation($parent, $child, $parent->getEmail());

                                try {
                                    if ($rewardPoints > 0){
                                        $reward_points = Mage::getModel('rewardpoints/account');
                                        $reward_points->saveCheckedOrder($order->getIncrementId(), $referralModel->getData('rewardpoints_referral_parent_id'), $order->getStoreId(), $rewardPoints, $referralModel->getData('rewardpoints_referral_id'), true);
                                    }


                                    if ($rewardPointsChild > 0){
                                        $reward_points2 = Mage::getModel('rewardpoints/account');
                                        $reward_points2->saveCheckedOrder($order->getIncrementId(), $referralModel->getData('rewardpoints_referral_child_id'), $order->getStoreId(), $rewardPointsChild, $referralModel->getData('rewardpoints_referral_id'), true);
                                    }

                                } catch (Exception $e) {
                                    //Mage::getSingleton('session')->addError($e->getMessage());
                                }
                            }
                        }
                    }
                }

                public function applyDiscount($observer)
                {
                    /*try {
                        
                        $customer = Mage::getSingleton('customer/session');
                        if ($customer->isLoggedIn()){
                            return Mage::getModel('rewardpoints/discount')->apply($observer->getEvent()->getItem());
                        } else return null;
                        
                        //return $this->_discount->apply($observer->getEvent()->getItem());
                    } catch (Mage_Core_Exception $e) {
                        Mage::getSingleton('checkout/session')->addError($e->getMessage());
                    } catch (Exception $e) {
                       Mage::getSingleton('checkout/session')->addError($e);
                    }*/
                }

		
	}
