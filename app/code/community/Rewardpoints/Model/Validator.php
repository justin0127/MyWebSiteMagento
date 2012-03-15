<?php
class Rewardpoints_Model_Validator extends Mage_SalesRule_Model_Validator
{
	
	public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
	{
		parent::process($item);

		try {
                    $customer = Mage::getSingleton('customer/session');
                    if ($customer->isLoggedIn()){
                        
                        /*AJOUT JON*/
                        $customerId = Mage::getModel('customer/session')->getCustomerId();
                        $customerPoints = Mage::getModel('rewardpoints/account')->load($customerId);
                        
                        $auto_use = Mage::getStoreConfig('rewardpoints/default/auto_use', Mage::app()->getStore()->getId());
                        if ($auto_use){
                            $customer_points = $customerPoints->getPointsCurrent();
                            if ($customer_points){
                                $cart_amount = Mage::getModel('rewardpoints/discount')->getCartAmount();
                                if (Mage::getStoreConfig('rewardpoints/default/math_method', Mage::app()->getStore()->getId())){
                                    $cart_amount = round($cart_amount);
                                } else {
                                    $cart_amount = floor($cart_amount);
                                }
                                $points_value = min(Mage::helper('rewardpoints/data')->convertMoneyToPoints($cart_amount), $customer_points);

                                //$points_value = 250;
                                Mage::getSingleton('customer/session')->setProductChecked(0);
                                Mage::helper('rewardpoints/event')->setCreditPoints($points_value);
                            }
                            
                        }
                        /*AJOUT JON*/

                        Mage::getModel('rewardpoints/discount')->apply($item);
                    }
                    //else return null;

                    //return $this->_discount->apply($observer->getEvent()->getItem());
                } catch (Mage_Core_Exception $e) {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                } catch (Exception $e) {
                   Mage::getSingleton('checkout/session')->addError($e);
                }
		return $this;
	}
}
