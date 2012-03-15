<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Controller for onepage and multishipping checkouts
 */
abstract class Mage_Checkout_Controller_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * Make sure customer is valid, if logged in
     * By default will add error messages and redirect to customer edit form
     *
     * @param bool $redirect - stop dispatch and redirect?
     * @param bool $addErrors - add error messages?
     * @return bool
     */
    protected function _preDispatchValidateCustomer($redirect = true, $addErrors = true)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer && $customer->getId()) {
            $validationResult = $customer->validate();
			//print_r($customer->getData());
			//exit();
			
            if ( $customer->getFirstname().$customer->getLastname() == "希思黎会员" ||$customer->getFirstName()=="希思黎"  || $customer->getLastname()=="会员"  || !$customer->getDob() || !$customer->getMobile() ) { //!$customer->getAlias() || 
				if($customer->getFirstname().$customer->getLastname() == "希思黎会员"|| $customer->getFirstName()=="希思黎"  || $customer->getLastname()=="会员"  ){
					Mage::getSingleton('customer/session')->addError("请修改您的姓名信息为真实信息才能继续完成下单流程");
				}
				//if(!$customer->getAlias() ){
				//	Mage::getSingleton('customer/session')->addError("请补充您的昵称信息");
				//}
				if( !$customer->getDob() || $customer->getDob()=="1900-01-01 00:00:00"){
					Mage::getSingleton('customer/session')->addError("请补充您的生日信息才能继续完成下单流程");
				}
				if( !$customer->getMobile()){
					Mage::getSingleton('customer/session')->addError("请补充您的手机信息才能继续完成下单流程");
				}
                    $this->_redirect('customer/account/edit');
                    $this->setFlag('', self::FLAG_NO_DISPATCH, true);
		Mage::getSingleton('customer/session')->setData('checkout_success',1);
                return false;
            }
			
            if ((true !== $validationResult) && is_array($validationResult)) {
                if ($addErrors) {
                    foreach ($validationResult as $error) {
                        Mage::getSingleton('customer/session')->addError($error);
                    }
                }
                if ($redirect) {
                    $this->_redirect('customer/account/edit');
                    $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                }
		Mage::getSingleton('customer/session')->setData('checkout_success',1);
                return false;
            }
        }

        return true;
    }
}
