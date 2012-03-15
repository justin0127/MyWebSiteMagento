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
 * This file is kept to assure backward compatibility!
 * 
 * @category   Magento extension
 * @package    RewardsPoint2
 * @copyright  Copyright (c) 2009 J2T DESIGN. (http://www.j2t-design.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RewardPoints_Model_Rule extends Mage_SalesRule_Model_Rule
{
	public function validate(Varien_Object $object) {
		if (substr($this->getCouponCode(),0,6) != 'points') {
			return parent::validate($object);
		}
		$customerId = Mage::getModel('customer/session')
						->getCustomerId();
		$points = Mage::getModel('rewardpoints/account')
						->load($customerId);
		$current = $points->getPointsCurrent();

		
		if ($current < $this->getPointsAmt()) {
			Mage::getSingleton('checkout/session')->addError('Not enough points available.');
			return false;
		}
		$step = Mage::getStoreConfig('rewardpoints/default/step_value', Mage::app()->getStore()->getId());
		if ($step > $this->getPointsAmt()){
			Mage::getSingleton('checkout/session')->addError('The minimum required points is not reached.');
			return false;
		}
		
		$step_apply = Mage::getStoreConfig('rewardpoints/default/step_apply', Mage::app()->getStore()->getId());
		if ($step_apply){
			if (($this->getPointsAmt() % $step) != 0){
				Mage::getSingleton('checkout/session')->addError('Amount of points wrongly used.');
				return false;
			}
		}
		
		
		return true;
	}
	
	
	public function getDiscountAmount() {
		if (substr($this->getCouponCode(),0,6) == 'points') {
			$step = Mage::getStoreConfig('rewardpoints/default/points_money', Mage::app()->getStore()->getId());
			return ($this->getPointsAmt() / $step);
		}
		$test = new Mage_SalesRule_Model_Rule();
		if (method_exists($test,'getDiscountAmount'))
			return parent::getDiscountAmount();
		if ($this->discount_amount){
			return $this->discount_amount;
		}
	}

        public function getPointsOnOrder(){
            $cartHelper = Mage::helper('checkout/cart');
            $items = $cartHelper->getCart()->getItems();
            $rewardPoints = 0;

           
            $rules = Mage::getModel('rewardpoints/rules')->getPointsByRule();
            $cart_amount = 0;
            
            foreach ($items as $_item){
                $_product = Mage::getModel('catalog/product')->load($_item->getProductId());                
                $product_points = $_product->getData('reward_points');

                if ($product_points > 0){
                    if ($_item->getQty() > 0){
                        $rewardPoints += (int)$product_points * $_item->getQty();

                    }
                } else {
                    $price = $_item->getRowTotal() + $_item->getTaxAmount() - $_item->getDiscountAmount();
                    $rewardPoints += (int)Mage::getStoreConfig('rewardpoints/default/money_points', Mage::app()->getStore()->getId()) * $price;
                }
                $cart_amount += $_item->getRowTotal() + $_item->getTaxAmount() - $_item->getDiscountAmount();

                if ($rules != array()){
                    foreach ($rules as $rule){
                        if ($rule['type'] == RewardPoints_Model_Rules::TARGET_SKU){
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
                        if ($rule['type'] == RewardPoints_Model_Rules::TARGET_CART){
                            switch ($rule['operator']){
                                case RewardPoints_Model_Rules::OPERATOR_1: // =
                                    if ($cart_amount == $rule['test_value']){
                                        $rewardPoints += (int)$rule['points'];
                                    }
                                    break;
                                case RewardPoints_Model_Rules::OPERATOR_2: // <
                                    if ($cart_amount < $rule['test_value']){
                                        $rewardPoints += (int)$rule['points'];
                                    }
                                    break;
                                case RewardPoints_Model_Rules::OPERATOR_3: // <=
                                    if ($cart_amount <= $rule['test_value']){
                                        $rewardPoints += (int)$rule['points'];
                                    }
                                    break;
                                case RewardPoints_Model_Rules::OPERATOR_4: // >
                                    if ($cart_amount > $rule['test_value']){
                                        $rewardPoints += (int)$rule['points'];
                                    }
                                    break;
                                case RewardPoints_Model_Rules::OPERATOR_5: // >=
                                    if ($cart_amount >= $rule['test_value']){
                                        $rewardPoints += (int)$rule['points'];
                                    }
                                    break;
                                case RewardPoints_Model_Rules::OPERATOR_6: // Between
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


            return $rewardPoints;
        }
	
}
