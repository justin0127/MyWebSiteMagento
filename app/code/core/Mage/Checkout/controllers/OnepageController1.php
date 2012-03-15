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
        $session = $this->getOnepage()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }

        $session->clear();
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
		
		$post = $this->getRequest()->getPost();
		if($post['chooseFree']) {
		$ProductList = str_replace(" ","",Mage::getStoreConfig('catalog/freeitem/product_sku'));
		$this->addMoreProduct($ProductList, $post['chooseFree']);
		}
		
            $postData = $this->getRequest()->getPost('billing', array());
            $data = $this->_filterPostData($postData);
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
			
			//var_dump($postData);
			//exit;

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

    /**  add product function  **/
    public function addMoreProduct($ProductList, $id) {
  		$productModel=Mage::getModel('catalog/product');
		$quoteObj=Mage::getSingleton('checkout/session')->getQuote();
	
    		//delete product action
		$ProductArr = explode(",", $ProductList);
		$QuoteProducts = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
		foreach($QuoteProducts as $_product) {
			$QuoteSku = $_product->getSku();
			if (in_array($QuoteSku,$ProductArr)) {
				$quoteObj->removeItem($_product->getId());  //if there is already a product in the cart, remove it first
			}
		}
		
		//add product action
		$productObj = $productModel->load($id);
		$quoteItem = Mage::getModel('sales/quote_item')->setProduct($productObj);
		$quoteItem->setQuote($quoteObj);
		$quoteItem->setQty('1');
		$quoteObj->addItem($quoteItem);
		$quoteObj->save();
		return;
    }
    
    /**  get gift message id  **/
    public function getGiftMessageId() {
  		$quote = Mage::getSingleton('checkout/session')->getQuote()->getData();
        	$gift_message_id = $quote['gift_message_id'];
		return $gift_message_id;
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
        
        /*  add package to quote  */
        $post = $this->getRequest()->getPost();
        if($post['package']) {
		$this->addMoreProduct($post['packageinfo'], $post['package']);
        }
        /*  end add package to quote  */
       
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
        
        /*  add print to gift message  */
        if ($this->getRequest()->getPost('dayindanju')) {
        $gift_message_id = $this->getGiftMessageId();
        if ($this->getRequest()->getPost('dayindanju') == 'on') {
        $IsPrint = 'no';
        }else{
        $IsPrint = 'yes';
        }
        $dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
	$username = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/username');
	$password = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/password');
	$link = mysql_connect('localhost', $username, $password);
				if (!$link) {
					die('Not connected : ' . mysql_error());
				}//end if
				$db_selected = mysql_select_db($dbname, $link);
				if (!$db_selected) {
					die ('Can\'t use foo : ' . mysql_error());
				}else{
				$query = sprintf("UPDATE gift_message SET print = '%s' WHERE gift_message_id = '%s'",
				mysql_real_escape_string($IsPrint),
				mysql_real_escape_string($gift_message_id));
				$result = mysql_query($query);
				}
				mysql_close($link);
     	}
     	/*  end add print to gift message  */ 
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
    * Set PosId Function
    */
    public function setPosId($customer_id) {
  		$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
		$username = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/username');
		$password = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/password');
    		
		Mage::app();
		$link = mysql_connect('localhost', $username, $password);
		if (!$link) {
			die('Not connected : ' . mysql_error());
		}//end if
		$db_selected = mysql_select_db($dbname, $link);
		if (!$db_selected) {
			die ('Can\'t use foo : ' . mysql_error());
		}else{
			mysql_query("INSERT INTO arvato_posid (customerid) VALUES('$customer_id') ") or die(mysql_error());
		}
		
		$PosId = mysql_insert_id();
		
		mysql_close($link);
		return $PosId;
    }
    
    /** 
    * Set Ecard Function 
    */
    public function setECard($customer_id) {
  		$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
		$username = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/username');
		$password = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/password');
    		
		Mage::app();
		$link = mysql_connect('localhost', $username, $password);
		if (!$link) {
			die('Not connected : ' . mysql_error());
		}//end if
		$db_selected = mysql_select_db($dbname, $link);
		if (!$db_selected) {
			die ('Can\'t use foo : ' . mysql_error());
		}else{
			mysql_query("INSERT INTO arvato_ecard (customerid) VALUES('$customer_id') ") or die(mysql_error()); 
		}
		
		$ECardNum = mysql_insert_id();
		
		mysql_close($link);
		return $ECardNum;
    }
    
    /** 
    * get current customer 
    */
    public function getCustomer() {
    		$customer = Mage::getSingleton('customer/session')->getCustomer();
    		return $customer;
    }
    
    /**
    * end notify email
    */
    public function sendEmail($customer, $title, $body) {
	$SoapClient = new SoapClient("http://app.focussend.com/webservice/FocusSendWebService.asmx?WSDL",array('trace' => 1));
	$FocusUser   = new StdClass;
	$FocusUser->Email="arvatoservices@bertelsmann.com.cn";
	$FocusUser->Password=sha1("arvatoli");

	$FocusEmail=new StdClass;
	$FocusEmail->Body= $body; 
	$FocusEmail->IsBodyHtml=true;

	$FocusTask=new StdClass;
	$FocusTask->TaskName="batch send php";
	$FocusTask->Subject="first subject";
	$FocusTask->SenderName="abc";
	$FocusTask->SenderEmail="abc@focussend.com";
	$FocusTask->ReplyName="reply";
	$FocusTask->ReplyEmail="zcz_wn@163.com";
	$FocusTask->SendDate=date("Y-m-d\TH-m-s");
	//$FocusTask->SendDate="2010-06-04T00:00:00";    
	$subject= $title;

	//$FocusReceiver=new StdClass;
	$FocusReceiver->Email=$customer->getEmail();

	//send one email
	$result= $SoapClient->SendOne(array("user"=>$FocusUser,"email"=>$FocusEmail,"subject"=>$subject,"receiver"=>$FocusReceiver));
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
			//var_dump($this->getRequest()->getPost());
			//exit;
			
			//$package_action=$this->getRequest()->getPost('')
			
            $shipping_time = $this->getRequest()->getPost('deliveryTime', false);
            $fapiao = $this->getRequest()->getPost('fapiao', '^^^');
            //Mage::log('in saveOrderAction : '. $shipping_time. $fapiao);

            $shipping_time_chinese = array('all'=>'工作日、双休日与假日均可送货', 'holidays'=>'仅双休日、假日送货(工作日不用送货)','workdays'=>'仅工作日送货(双休日、假日不用送货)','nights'=>'学校地址(该地址白天没人，请尽量安排其他时间送货)');
            
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
        
	/*  set and get PosId & E-Card  */
	//E-Card
	$Customer = $this->getCustomer();
        $CardNum = $Customer->getECard();
        if(!$CardNum) {
        	//set a new E-Card Num when there isn't any one
        	$CustomerId = $Customer->getId();
        	$_ECardNum = $this->setECard($CustomerId);   // do action
        	
        	//save E-Card Num in customer info
        	$_ECardNum = strval($_ECardNum);
        	$ECardNum = 'E'.str_pad($_ECardNum,5, "0",STR_PAD_LEFT);
        	
        	$Customer->setECard($ECardNum);
        	$Customer->save();
        	
        	//send notify email to customer after create E-Card
        	$title = "your e-card num is".$ECardNum;
        	$body = "notify customer e-card info";
        	$this->sendEmail($Customer, $title, $body);
        }
        
        //PosId
        //$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $PosNum = $Customer->getPosId();
        if(!$PosNum) {
        	//set a new Pos Num when there isn't any one
        	$CustomerId = $Customer->getId();
        	$_PosNum = $this->setPosId($CustomerId);   // do action
        	
        	//save Pos Num in customer info
        	$_PosNum = strval($_PosNum);
        	$PosId = '08797'.str_pad($_PosNum,5, "0",STR_PAD_LEFT);
        	
        	$Customer->setPosId($PosId);
        	$Customer->save();
        	
		/* insert customer to crm which has pos id */
		if($Customer->getPosId() != NULL) {
			//insert action
			
			/****  insert online user to CRM  ***/
                
            $OnlineCustomerID = $Customer->getPosId();
			$CustomerName = $Customer->getFirstname().$Customer->getLastname();
			$CustomerTitle = '0';
			$CustomerBirthday = '1900-01-01';
			$CustomerProvince = '0';
			$CustomerCity = '0';
			$CustomerAddress = '0';
			$CustomerZip = '0';
			$CustomerEmail = $Customer->getEmail();
			$CustomerAreaCode = '0';
			$CustomerTele = '0';
			
			if($Customer->getMobile()) {
			$CustomerMobile = $Customer->getMobile();
			}else{
			$CustomerMobile = '0';
			}
			
			$ParentID = '0';
			$Userrank = '0';
			$Msn = '0';
			$Qq = '0';
			$Officephone = '0';
			$Alias = $Customer->getAlias();
			$onlineAdd = "/var/www/sisley/shell/addcustomer.py -c \{\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'CustomerName\':\'".$CustomerName."\',\'CustomerTitle\':\'".$CustomerTitle."\',\'CustomerBirthday\':\'".$CustomerBirthday."\',\'CustomerProvince\':\'".$CustomerProvince."\',\'CustomerCity\':\'".$CustomerCity."\',\'CustomerAddress\':\'".$CustomerAddress."\',\'CustomerZip\':\'".$CustomerZip."\',\'CustomerEmail\':\'".$CustomerEmail."\',\'CustomerAreaCode\':\'".$CustomerAreaCode."\',\'CustomerTele\':\'".$CustomerTele."\',\'CustomerMobile\':\'".$CustomerMobile."\',\'ParentID\':\'".$ParentID."\',\'Userrank\':\'".$Userrank."\',\'Msn\':\'".$Msn."\',\'Qq\':\'".$Qq."\',\'Officephone\':\'".$Officephone."\',\'Alias\':\'".$Alias."\'\}";
			Mage::log($onlineAdd);
			shell_exec($onlineAdd);
                
               		/***  end insert  ***/
			
		}
		/* insert customer to crm which has pos id */
        	
        }
        /*  end set PosId & E-Card  */
        

        
        //if use cod
        //Mage::log(Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance()->getCode());
        if(Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance()->getCode() == 'cashondelivery') {
            ### get order info ###
            $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order = Mage::getModel('sales/order')->load($orderId);
            //Mage::log($orderId);
            //exit;
            $OnlineOrderID = $orderId;
			
	    /*  gift message  */		
            $gift_message_id = $this->getGiftMessageId();
            $giftMessage = Mage::getModel('giftmessage/message')->load((int)$gift_message_id);
            $Print = $giftMessage->getData('print');
            if($Print == 'yes'){
            $IsPrint = '1';
            }else{
            $IsPrint = '0';
            }
            if ($giftMessage->getData('sender')) {
            $GiftCardFrom = $giftMessage->getData('sender');
            }else{
            $GiftCardFrom = '0';
            }
            if ($giftMessage->getData('recipient')) {
            $GiftCardTo = $giftMessage->getData('recipient');
            }else{
            $GiftCardTo = '0';
            }
            if ($giftMessage->getData('message')) {
            $GiftCardContent = $giftMessage->getData('message');
            }else{
            $GiftCardContent = '0';
            }
            //Mage::log($IsPrint);
            /*  end gift message  */
            
            
            $DateOfOrder = date("Y-m-d");
			
            $Customer = $this->getCustomer();
            $OrderSource = '10';   //
            $OnlineCustomerID = $Customer->getPosId();
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
            if ('chinapay_payment' == $paymentcode){
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
            
            /*  get fapiao title  */
            $FapiaoTitle = $order->getFapiaoTitle(); 
            if($FapiaoTitle == '^^^') {
            $IsFapiao = 'No';
            }else{
            $IsFapiao = 'Yes';
            }
            
            $Remark = '0'.'--'.$IsFapiao;
            $UsedItemPoints = '0';
            
            $OrderInformation = "\{\'OnlineOrderID\':\'".$OnlineOrderID."\',\'DateOfOrder\':\'".$DateOfOrder."\',\'OrderSource\':\'".$OrderSource."\',\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'RetailAmountOfOrder\':\'".$RetailAmountOfOrder."\',\'ActualAmountOfOrder\':\'".$ActualAmountOfOrder."\',\'DiscountAmountOfOrder\':\'".$DiscountAmountOfOrder."\',\'Payment\':".$Payment.",\'PaymentChannel\':".$PaymentChannel.",\'PaymentCost\':\'".$PaymentCost."\',\'TotalProductAmount\':\'".$TotalProductAmount."\',\'InvoiceTitle\':\'".$InvoiceTitle."\',\'ShippingCost\':\'".$ShippingCost."\',\'UsedPoints\':".$UsedPoints.",\'Package\':\'".$Package."\',\'PackageCost\':\'".$PackageCost."\',\'PromotionCode\':\'".$PromotionCode."\',\'Remark\':\'".$Remark."\',\'UsedItemPoints\':".$UsedItemPoints.",\'IsPrint\':\'".$IsPrint."\',\'GiftCardFrom\':\'".$GiftCardFrom."\',\'GiftCardTo\':\'".$GiftCardTo."\',\'GiftCardContent\':\'".$GiftCardContent."\'\}";
            //Mage::log($OrderInformation);
            
            ### get order item info ###
            //$products = $order->getAllItems();
            $products = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
            //Mage::log($products);
            
            $items = array();
            $i = 0;
            foreach ($products as $product){
            
            Mage::log($product->getSku());
            
		if ($product->getProductType()=='configurable' ) {
		      	$price = round($product->getRowTotal() / $product->getQty(), 2);
			//continue;	    
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
	    	//'Price' => $price,
	    	'Price' => $product->getBaseRowTotal(),
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
            Mage::log($addorder);
            shell_exec($addorder);
            //passthru('python /var/www/sisley/shell/addorder.py'.$addorder);
            //exit;
        }
        
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
