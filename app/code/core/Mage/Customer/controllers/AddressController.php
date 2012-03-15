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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer address controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_AddressController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Customer addresses list
     */
    public function indexAction()
    {
        if (count($this->_getSession()->getCustomer()->getAddresses())) {
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');

            if ($block = $this->getLayout()->getBlock('address_book')) {
                $block->setRefererUrl($this->_getRefererUrl());
            }
            $this->renderLayout();
        }
        else {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/new'));
        }
    }

    public function editAction()
    {
        $this->_forward('form');
    }

    public function newAction()
    {
        $this->_forward('form');
    }

    /**
     * Address book form
     */
    public function formAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('customer/address');
        }
        $this->renderLayout();
    }

    public function formPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        // Save data
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer/address')
                ->setData($this->getRequest()->getPost())
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) {
                $customerAddress = $this->_getSession()->getCustomer()->getAddressById($addressId);
                if ($customerAddress->getId() && $customerAddress->getCustomerId() == $this->_getSession()->getCustomerId()) {
                    $address->setId($addressId);
                }
                else {
                    $address->setId(null);
                }
            }
            else {
                $address->setId(null);
            }
            try {
                $accressValidation = $address->validate();
                if (true === $accressValidation) {
                    $address->save();
					
					$info = $this->getRequest()->getPost();
					//var_dump($info);
					//exit;
					
					$CId = Mage::getSingleton('customer/session')->getCustomerId();
					$customer = Mage::getModel('customer/customer')->load($CId);
					/****  insert online user to CRM  ***/
               
                $OnlineCustomerID = $CId;
				$CustomerName = $customer->getFirstname().$customer->getLastname();
				
				$AddArr = array(
				"320" => "北京",
"321" => "天津",
"322" => "上海",
"323" => "重庆",
"324" => "江苏省",
"325" => "浙江省",
"326" => "安徽省",
"327" => "福建省",
"328" => "海南省",
"329" => "云南省",
"330" => "四川省",
"331" => "贵州省",
"332" => "湖南省",
"333" => "广东省",
"334" => "河南省",
"335" => "湖北省",
"336" => "山东省",
"337" => "河北省",
"338" => "辽宁省",
"339" => "吉林省",
"340" => "黑龙江省",
"341" => "山西省",
"342" => "陕西省",
"343" => "甘肃省",
"344" => "青海省",
"345" => "江西省",
"346" => "新疆维吾尔自治区",
"347" => "西藏自治区",
"348" => "宁夏回族自治区",
"349" => "内蒙古自治区",
"350" => "广西壮族自治区",
"351" => "香港特别行政区",
"352" => "澳门特别行政区",
"353" => "台湾省"
				);
				
				//$CustomerTitle = '0';
				//$CustomerBirthday = '1900-01-01';
				$CustomerProvince = $AddArr[$info['region_id']];
				$CustomerCity = $info['city'];
				$CustomerAddress = $info['street']['0'];
				$CustomerZip = $info['postcode'];
				$CustomerEmail = $customer->getEmail();
				$CustomerAreaCode = '0';
				$CustomerTele = $info['telephone'];
				/*if($customer->getMobile()) {
				$CustomerMobile = $customer->getMobile();
				}else{
				$CustomerMobile = '0';
				}*/
				$ParentID = '0';
				$Userrank = '0';
				
				if($customer->getSuffix()){
				$customerTitle = $customer->getSuffix();
				}else{$customerTitle='0';}
				if($customer->getDob()){
				$CustomerBirthday =$customer->getDob();
				}else{$CustomerBirthday='1900-01-01';}	
				if($customer->getMobile()){
				$CustomerMobile = $customer->getMobile();
				}else{$CustomerMobile='0';}
				if($customer->getMsn()){
				$Msn =$customer->getMsn();
				}else{$Msn='0';}
				if($customer->getQq()){
				$Qq = $customer->getQq();
				}else{$Qq='0';}
				if($customer->getOfficePhone()){
				$Officephone =$customer->getOfficePhone();
				}else{$Officephone='0';}
				
				$Alias = $customer->getAlias();
				$onlineAdd = "/var/www/sisley/shell/addcustomer.py -c \{\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'CustomerName\':\'".$CustomerName."\',\'CustomerTitle\':\'".$CustomerTitle."\',\'CustomerBirthday\':\'".$CustomerBirthday."\',\'CustomerProvince\':\'".$CustomerProvince."\',\'CustomerCity\':\'".$CustomerCity."\',\'CustomerAddress\':\'".$CustomerAddress."\',\'CustomerZip\':\'".$CustomerZip."\',\'CustomerEmail\':\'".$CustomerEmail."\',\'CustomerAreaCode\':\'".$CustomerAreaCode."\',\'CustomerTele\':\'".$CustomerTele."\',\'CustomerMobile\':\'".$CustomerMobile."\',\'ParentID\':\'".$ParentID."\',\'Userrank\':\'".$Userrank."\',\'Msn\':\'".$Msn."\',\'Qq\':\'".$Qq."\',\'Officephone\':\'".$Officephone."\',\'Alias\':\'".$Alias."\'\}";
				//Mage::log($offlineAdd);
				shell_exec($onlineAdd);
               
                /***  end insert  ***/
					
					
                    $this->_getSession()->addSuccess($this->__('The address has been saved.'));
                    $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                    return;
                } else {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                    if (is_array($accressValidation)) {
                        foreach ($accressValidation as $errorMessage) {
                            $this->_getSession()->addError($errorMessage);
                        }
                    } else {
                        $this->_getSession()->addError($this->__('Cannot save the address.'));
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save address.'));
            }
        }
        $this->_redirectError(Mage::getUrl('*/*/edit', array('id'=>$address->getId())));
    }

    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId) {
            $address = Mage::getModel('customer/address')->load($addressId);

            // Validate address_id <=> customer_id
            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
                $this->_getSession()->addError($this->__('The address does not belong to this customer.'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
                return;
            }

            try {
                $address->delete();
                $this->_getSession()->addSuccess($this->__('The address has been deleted.'));
            }
            catch (Exception $e){
                $this->_getSession()->addError($this->__('An error occurred while deleting the address.'));
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
    }
}
