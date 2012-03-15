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


class Mage_Checkout_OnepageController extends Mage_Checkout_Controller_Action
{
    protected $_sectionUpdateFunctions = array(
        'payment-method'  => '_getPaymentMethodsHtml',
        'shipping-method' => '_getShippingMethodsHtml',
        'review'          => '_getReviewHtml',
    );

    /**
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_preDispatchValidateCustomer();
        return $this;
    }

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getHasError()
            || $this->getOnepage()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    /**
     * Get shipping method step html
     *
     * @return string
     */
    protected function _getShippingMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * Get payment method step html
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getAdditionalHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_additional');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * Get order review step html
     *
     * @return string
     */
    protected function _getReviewHtml()
    {
        return $this->getLayout()->getBlock('root')->toHtml();
    }

    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * Checkout page
     */
    public function indexAction()
    {
        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getOnepage()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        $this->renderLayout();
    }

    /**
     * Checkout status block
     */
    public function progressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function shippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function reviewAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Order success action
     */
    public function successAction()
    {
         $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent('checkout_onepage_controller_success_action');
        $this->renderLayout();
    }

    public function failureAction()
    {
        $lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }


    public function getAdditionalAction()
    {
        $this->getResponse()->setBody($this->_getAdditionalHtml());
    }

    /**
     * Address JSON
     */
    public function getAddressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = $this->getOnepage()->getAddress($addressId);

            if (Mage::getSingleton('customer/session')->getCustomer()->getId() == $address->getCustomerId()) {
                $this->getResponse()->setHeader('Content-type', 'application/x-json');
                $this->getResponse()->setBody($address->toJson());
            } else {
                $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            }
        }
    }

    /**
     * Save checkout method
     */
    public function saveMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('method');
            $result = $this->getOnepage()->saveCheckoutMethod($method);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost('billing', array());
            $data = $this->_filterPostData($postData);
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Save payment ajax action
     *
     * Sets either redirect or a JSON response
     */
    public function savePaymentAction()
    {
 
        Mage::log($this->getRequest()->getPost());
        if ($this->_expireAjax()) {
            return;
        }
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);

            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Create order action
     */
    public function saveOrderAction()
    {
        Mage::log($this->getRequest()->getPost());
        
        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
            if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }

            //yaozer
            $shipping_time = $this->getRequest()->getPost('deliveryTime', false);
            $fapiao = $this->getRequest()->getPost('fapiao', '^^^');
            //Mage::log('in saveOrderAction : '. $shipping_time. $fapiao);

            $shipping_time_chinese = array('all'=>'送货时间：不限制', 'holidays'=>'送货时间：限节假日','workdays'=>'送货时间：限工作日','nights'=>'送货时间：学校地址');
            
            if (array_key_exists($shipping_time, $shipping_time_chinese)) {
    		 $shipping_time = $shipping_time_chinese[$shipping_time];  //  translate into chinese,  or false for no translation.
	   }
	   
	   
            $this->getOnepage()->saveOrder($shipping_time, $fapiao);
            
            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error']   = false;
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        
        
            ### get order info ###
            $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order = Mage::getModel('sales/order')->load($orderId);
            //Mage::log($orderId);
            $OnlineOrderID = $orderId;
            $DateOfOrder = date("Y-m-d");
            $OrderSource = '10';   //
            $OnlineCustomerID = Mage::getSingleton('customer/session')->getCustomerId();
            $RetailAmountOfOrder = strval(Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal());
            $ActualAmountOfOrder = strval(Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal());
            $DiscountAmountOfOrder = '0';
            
            $Payment = '1';
            $paymentcode = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance()->getCode();
            if ('cashondelivery' == $paymentcode){
            $Payment = '0';
            }else{
            $Payment = '1';
            }
            
            $PaymentChannel = '3';
            if ('cashondelivery' == $paymentcode){
            $PaymentChannel = '99';
            $PaymentCost = '0';
            }
            if ('chinapay' == $paymentcode){
            $PaymentChannel = '0';
            $PaymentCost = '0';
            }
            if ('alipay_payment' == $paymentcode){
            $PaymentChannel = '1';
            $PaymentCost = '0';
            }
            if ('cosmo99bill_payment' == $paymentcode){
            $PaymentChannel = '2';
            $PaymentCost = '0';
            }
            if ('checkmo' == $paymentcode){
            $PaymentChannel = '3';
            $PaymentCost = '0';
            }
            
            $ship = number_format(Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getBaseShippingAmount(), 0, '.', '');
            $ShippingCost = strval($ship);
            //$PaymentCost = '0';
            $TotalProductAmount = $RetailAmountOfOrder-$ShippingCost-$PaymentCost;//number_format(Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal(), 2, '.', '');
            $InvoiceTitle = '0';
            
            $UsedPoints = '0';
            $Package = 'Normal';
            $PackageCost = '0';
            $PromotionCode = '0';
            $Remark = '0';
            $UsedItemPoints = '0';
            
            $OrderInformation = "\{\'OnlineOrderID\':\'".$OnlineOrderID."\',\'DateOfOrder\':\'".$DateOfOrder."\',\'OrderSource\':\'".$OrderSource."\',\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'RetailAmountOfOrder\':\'".$RetailAmountOfOrder."\',\'ActualAmountOfOrder\':\'".$ActualAmountOfOrder."\',\'DiscountAmountOfOrder\':\'".$DiscountAmountOfOrder."\',\'Payment\':".$Payment.",\'PaymentChannel\':".$PaymentChannel.",\'PaymentCost\':\'".$PaymentCost."\',\'TotalProductAmount\':\'".$TotalProductAmount."\',\'InvoiceTitle\':\'".$InvoiceTitle."\',\'ShippingCost\':\'".$ShippingCost."\',\'UsedPoints\':".$UsedPoints.",\'Package\':\'".$Package."\',\'PackageCost\':\'".$PackageCost."\',\'PromotionCode\':\'".$PromotionCode."\',\'Remark\':\'".$Remark."\',\'UsedItemPoints\':".$UsedItemPoints."\}";
            //Mage::log($OrderInformation);
            
            ### get order item info ###
            //$products = $order->getAllItems();
            $products = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
            //Mage::log($products);
            
            $items = array();
            $i = 0;
            foreach ($products as $product){
		if ($product->getProductType()=='configurable' ) {
		      	$price = round($product->getRowTotal() / $product->getQty(), 2);
			continue;	    
		    }
	    if (empty($price)){
		$price = round($product->getRowTotal() / $product->getQty(), 2);        
		}
	  	$sku = $product->getSku();
	  	$name = $product->getName();
	  	$qty = round($product->getQty()); 
	    $quote_str = array(
	    	'OnlineOrderID' => $orderId,
	    	'ProductID' => $sku,
	    	'ProductType' => '0',
	    	'Quantity' => $qty,
	    	'BasePrice' => $product->getBaseRowTotal(),
	    	'Price' => $price,
	    	'UsedItemPoints' => '0'
	    );   
	    //$quotes_str .= $quote_str;
	    
	    $item = "\{\'OnlineOrderID\':\'".$quote_str['OnlineOrderID']."\',\'ProductID\':\'".$quote_str['ProductID']."\',\'ProductType\':".$quote_str['ProductType'].",\'Quantity\':".$quote_str['Quantity'].",\'BasePrice\':\'".$quote_str['BasePrice']."\',\'Price\':\'".$quote_str['Price']."\',\'UsedItemPoints\':".$quote_str['UsedItemPoints']."\}";
	    
	    $items[$i] = $item;
	    $i++;
	    //Mage::log($items);
	    }
	    
	    $max = count($items);
	    if ($max == '1') {
	    $OrderItemsInformation = "[".$item."]";
	    }else{
	    $OrderItems = $items['0'];
	    
	    for ($i=1; $i<$max; $i++)
	    {
              $OrderItems = $OrderItems.','.$items[$i];
            }
            
            $OrderItemsInformation = "[".$OrderItems."]";
            }
            //Mage::log($OrderItemsInformation);
            
            ### get delivery info ###
            $ship_addr = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
            $addr_info = $ship_addr->getData();
            //Mage::log($addr_info);
            
            $OnlineOrderID = $orderId;
            $DeliveryType = 'EMS_COD1';
            $ReceiverName = $ship_addr->getLastname().$ship_addr->getFirstname();
            $ReceiverProvince = $ship_addr->getRegion();
            $ReceiverCity = $ship_addr->getCity();
            $ReceiverAddress = $addr_info['street'];
            $ReceiverZip = $ship_addr->getPostcode();
            $ReceiverAreaCode = $ship_addr->getPostcode();
            $ReceiverTelePhone = $ship_addr->getFax();
            $ReceiverMobile = $ship_addr->getTelephone();
            
            $DeliveryInformation = "\{\'OnlineOrderID\':\'".$OnlineOrderID."\',\'DeliveryType\':\'".$DeliveryType."\',\'ReceiverName\':\'".$ReceiverName."\',\'ReceiverProvince\':\'".$ReceiverProvince."\',\'ReceiverCity\':\'".$ReceiverCity."\',\'ReceiverAddress\':\'".$ReceiverAddress."\',\'ReceiverZip\':\'".$ReceiverZip."\',\'ReceiverAreaCode\':\'".$ReceiverAreaCode."\',\'ReceiverTelePhone\':\'".$ReceiverTelePhone."\',\'ReceiverMobile\':\'".$ReceiverMobile."\'\}";
            
            
            // order info to python 
            $addorder = "/var/www/sisley/shell/addorder.py -o ".$OrderInformation." -i ".$OrderItemsInformation." -d ".$DeliveryInformation;
            //Mage::log($addorder);
            shell_exec($addorder);
            //passthru('python /var/www/sisley/shell/addorder.py'.$addorder);
            //exit;
        
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }
}
