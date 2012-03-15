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
class Rewardpoints_Helper_Data extends Mage_Core_Helper_Abstract {
    public function getReferalUrl()
    {
        return $this->_getUrl('rewardpoints/');
    }

    public function getProductPoints($product){
        $product_points = $product->getData('reward_points');
        if ($product_points > 0){
            return $product_points;
        } else {
            //get product price include vat
            $_finalPriceInclTax  = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
            $_weeeTaxAmount = Mage::helper('weee')->getAmount($product);
            $price = Mage::helper('core')->currency($_finalPriceInclTax+$_weeeTaxAmount,false,false);
            $money_to_points = Mage::getStoreConfig('rewardpoints/default/money_points', Mage::app()->getStore()->getId());
            if ($money_to_points > 0){
                $price = $price * $money_to_points;
            }
            if (Mage::getStoreConfig('rewardpoints/default/math_method', Mage::app()->getStore()->getId()) == 1){
                return round($price);
            } else {
                return floor($price);
            }
        }
    }

    public function convertMoneyToPoints($money){
        $points_to_get_money = Mage::getStoreConfig('rewardpoints/default/points_money', Mage::app()->getStore()->getId());
        if (Mage::getStoreConfig('rewardpoints/default/math_method', Mage::app()->getStore()->getId()) == 1){
            $money_amount = round($money*$points_to_get_money);
        } else {
            $money_amount = floor($money*$points_to_get_money);
        }
        return $money_amount;
    }

    public function convertPointsToMoney($points_to_be_used){
        $customerId = Mage::getModel('customer/session')
                                        ->getCustomerId();
        $points = Mage::getModel('rewardpoints/account')
                                        ->load($customerId);
        $current = $points->getPointsCurrent();


        if ($current < $points_to_be_used) {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('rewardpoints')->__('Not enough points available.'));
            Mage::helper('rewardpoints/event')->setCreditPoints(0);
            return 0;
        }
        $step = Mage::getStoreConfig('rewardpoints/default/step_value', Mage::app()->getStore()->getId());
        if ($step > $points_to_be_used){
            Mage::getSingleton('checkout/session')->addError(Mage::helper('rewardpoints')->__('The minimum required points is not reached.'));
            Mage::helper('rewardpoints/event')->setCreditPoints(0);
            return 0;
        }

        $step_apply = Mage::getStoreConfig('rewardpoints/default/step_apply', Mage::app()->getStore()->getId());
        if ($step_apply){
            if (($points_to_be_used % $step) != 0){
                Mage::getSingleton('checkout/session')->addError(Mage::helper('rewardpoints')->__('Amount of points wrongly used.'));
                Mage::helper('rewardpoints/event')->setCreditPoints(0);
                return 0;
            }
        }

        $points_to_get_money = Mage::getStoreConfig('rewardpoints/default/points_money', Mage::app()->getStore()->getId());
        if (Mage::getStoreConfig('rewardpoints/default/math_method', Mage::app()->getStore()->getId()) == 1){
            $discount_amount = round($points_to_be_used/$points_to_get_money);
        } else {
            $discount_amount = floor($points_to_be_used/$points_to_get_money);
        }
        return $discount_amount;
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
