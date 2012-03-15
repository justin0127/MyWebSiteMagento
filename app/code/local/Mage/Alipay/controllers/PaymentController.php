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
 * @category   Mage
 * @package    Mage_Alipay
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Alipay Payment Front Controller
 *
 * @category   Mage
 * @package    Mage_Alipay
 * @name       Mage_Alipay_PaymentController
 * @author	   Magento Core Team <core@magentocommerce.com>, Quadra Informatique - Nicolas Fischer <nicolas.fischer@quadra-informatique.fr>
 */

class Mage_Alipay_PaymentController extends Mage_Core_Controller_Front_Action
{
	/**
     * Order instance
     */
	protected $_order;

	/**
     *  Get order  获得定单
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
	 public function testAction(){
	 return false;
try{

$c = array (
  'entity_type_id'  => 5,         // 11 is the id of the entity model "sales/order". This could be different on your system! Look at database-table "eav_entity_type" for the correct ID!
  'attribute_code'  => 'omcstatus',
  'backend_type'    => 'text',     // MySQL-DataType
  'frontend_input'  => 'text', // Type of the HTML-Form-Field
  'is_global'       => '1',
  'is_visible'      => '1',
  'is_required'     => '0',
  'is_user_defined' => '0',
  'frontend_label'  => 'OMC Status',
);
$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($c['entity_type_id'], $c['attribute_code'])
          ->setStoreId(0)
          ->addData($c);
$attribute->save(); 


$cc = array (
  'entity_type_id'  => 5,         // 11 is the id of the entity model "sales/order". This could be different on your system! Look at database-table "eav_entity_type" for the correct ID!
  'attribute_code'  => 'obflag',
  'backend_type'    => 'text',     // MySQL-DataType
  'frontend_input'  => 'text', // Type of the HTML-Form-Field
  'is_global'       => '1',
  'is_visible'      => '1',
  'is_required'     => '0',
  'is_user_defined' => '0',
  'frontend_label'  => 'OB Flag',
);
$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($cc['entity_type_id'], $cc['attribute_code'])
          ->setStoreId(0)
          ->addData($cc);
$attribute->save(); 


}catch(Exception $e)
            {
                print_r($e);
            }
	 echo "F";
	 
	 }
	public function getOrder()
	{
		if ($this->_order == null) {
			$session = Mage::getSingleton('checkout/session');
			$this->_order = Mage::getModel('sales/order');
			$this->_order->loadByIncrementId($session->getLastRealOrderId());
		}
		return $this->_order;
	}

	/**
     * When a customer chooses Alipay on Checkout/Payment page
     *  支付入口
     */
	public function redirectAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$session->setAlipayPaymentQuoteId($session->getQuoteId());

		$order = $this->getOrder();
		if (!$order->getId()) {                       //$order->getId()    get entity_id
			$this->norouteAction();
			return;
		}
		$order->addStatusToHistory(
		$order->getStatus(),
		Mage::helper('alipay')->__('客户被重定向到支付宝支付页面')
		);
		
		
		$returnstatus="-4";
		$order->setOmcstatus($returnstatus);
		  if($order->getStatus()!='processing'){

			$order->setStatus("online_unpaid");
			$order->save();
			
			}		

		//exit(var_dump(sprintf('%.2f',$order->getShipping_amount())));
		$order->save();

		$this->getResponse()
		->setBody($this->getLayout()
		->createBlock('alipay/redirect')
		->setOrder($order)
		->toHtml());

		$session->unsQuoteId();
	}
	
   /*
   *后支付入口
   */   
	 public function payAction()
  	{
      $data=$this->getRequest()->getQuery();
      $order = Mage::getModel('sales/order')->loadByIncrementId($data['orderid']);  
      if (Mage::getStoreConfig('payment/alipay_payment/service_type')=='trade_create_by_buyer'){
			  $redirect='alipay/redirect';
  		}else{
  			$redirect='alipay/redirect';
  		}    	      
  		$this->getResponse()
  			->setBody($this->getLayout()
		      ->createBlock($redirect)
  				->setOrder($order)
  				->toHtml());
    }

	/**
	 *  Alipay response router
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function notifyAction()
	{
		$model = Mage::getModel('alipay/payment');
		if ($this->getRequest()->isPost()) {
			$postData = $this->getRequest()->getPost();
			$method   = 'post';
		
		} else if ($this->getRequest()->isGet()) {
			$postData = $this->getRequest()->getQuery();
			$method   = 'get';

		} else {
			$model->generateErrorResponse();
		};
		
		//$this->logvarRS('notifyAction',$_POST,'pending_paypal',Mage::helper('alipay')->__('Payment accepted by Alipay'));
		
			$order = Mage::getModel('sales/order')
			->loadByIncrementId($postData['body']);
	
			if (!$order->getId()) {
				$model->generateErrorResponse();
			}
		if($postData['trade_status']=='WAIT_SELLER_SEND_GOODS'){
		  if($order->getStatus()!='processing'){
			$order->addStatusToHistory(
			$model->getConfigData('order_status_payment_accepted'),
			Mage::helper('alipay')->__('买家已付款，等待卖家发货')
			);
			$order->sendNewOrderEmail();
			$this->saveInvoice($order);
			$order->save();
			
			}
			$this->_redirect('checkout/onepage/success');
			echo 'success';
		}else if($postData['trade_status']=="WAIT_BUYER_PAY"){
		if($order->getStatus()!='wait_buyer_pay'){
			$order->addStatusToHistory(
			'wait_buyer_pay',
			Mage::helper('alipay')->__('等待买家付款')
			);
	
			$order->save();
			}
			echo 'success';
		}else if($postData['trade_status']=="SEND_GOODS"){
			$order->addStatusToHistory(
			'send_goods',
			Mage::helper('alipay')->__('卖家已发货')
			);

			$order->save();
			echo 'success';
		}else if($postData['trade_status']=="WAIT_BUYER_CONFIRM_GOODS"){
			$order->addStatusToHistory(
			'wait_buyer_confirm_goods',
			Mage::helper('alipay')->__('等待买家确认')
			);

			$order->save();
			echo 'success';
		/*}else if($postData['trade_status']=="TRADE_FINISHED"){
			$order->addStatusToHistory(
			'trade_finishen',
			Mage::helper('alipay')->__('订单完成')
			);

			$order->save();
			echo 'success';
		}*/
		}else if ($postData['trade_status'] == "TRADE_SUCCESS")
        {
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($postData['out_trade_no']);
            $order->sendNewOrderEmail();
            $order->addStatusToHistory(
            $model->getConfigData('order_status_payment_accepted'),
            Mage::helper('alipay')->__('买家已付款， 等待卖家发货。'),
            true
            );
            try
            {
                $order->save();
            } catch(Exception $e)
            {
                ;
            }
            echo 'success';
            
            ### get order info ###
            $OnlineOrderID = $postData['out_trade_no'];
            $DateOfOrder = date("Y-m-d");
            $OrderSource = '10';   //
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            $OnlineCustomerID = $customer->getPosId();
            $RetailAmountOfOrder = strval($order->getBaseGrandTotal());
            $ActualAmountOfOrder = strval($order->getBaseGrandTotal());
            $DiscountAmountOfOrder = '0';
            
		$gift_message_id = $order['gift_message_id'];
		$giftMessage = Mage::getModel('giftmessage/message')->load((int)$gift_message_id);
		$Print = $giftMessage->getData('print');
		if($Print == 'no'){
		$IsPrint = '0';
		}else{
		$IsPrint = '1';
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
            
            $Payment = '1';
            $paymentcode = $order->getPayment()->getMethodInstance()->getCode();
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
            
            $ship = number_format($order->getBaseShippingAmount(), 0, '.', '');
            $ShippingCost = strval($ship);
            //$PaymentCost = '0';
            $TotalProductAmount = $RetailAmountOfOrder-$ShippingCost-$PaymentCost;//number_format(Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal(), 2, '.', '');
            $InvoiceTitle = '0';
            
            $UsedPoints = '0';
            $Package = 'Normal';
            $PackageCost = '0';
            $PromotionCode = '0';
            //$Remark = '0';
            $UsedItemPoints = '0';
            
            $FapiaoTitle = $order->getFapiaoTitle(); 
            $shipping_time = $order->getShippingTime();
            if($FapiaoTitle == '^^^') {
            $IsFapiao = 'No';
            }else{
            $IsFapiao = 'Yes'.'^^^'.$FapiaoTitle;
            }
            $Remark = '0'.'^^^'.$IsFapiao.'^^^'.$shipping_time;
            
            $GiftCardFrom = str_replace(' ','\ ', $GiftCardFrom);
            $GiftCardTo = str_replace(' ','\ ', $GiftCardTo);
            $GiftCardContent = str_replace(' ','\ ', $GiftCardContent);
            $Remark = str_replace(' ','\ ', $Remark);
            
            $OrderInformation = "\{\'OnlineOrderID\':\'".$OnlineOrderID."\',\'DateOfOrder\':\'".$DateOfOrder."\',\'OrderSource\':\'".$OrderSource."\',\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'RetailAmountOfOrder\':\'".$RetailAmountOfOrder."\',\'ActualAmountOfOrder\':\'".$ActualAmountOfOrder."\',\'DiscountAmountOfOrder\':\'".$DiscountAmountOfOrder."\',\'Payment\':".$Payment.",\'PaymentChannel\':".$PaymentChannel.",\'PaymentCost\':\'".$PaymentCost."\',\'TotalProductAmount\':\'".$TotalProductAmount."\',\'InvoiceTitle\':\'".$InvoiceTitle."\',\'ShippingCost\':\'".$ShippingCost."\',\'UsedPoints\':".$UsedPoints.",\'Package\':\'".$Package."\',\'PackageCost\':\'".$PackageCost."\',\'PromotionCode\':\'".$PromotionCode."\',\'Remark\':\'".$Remark."\',\'UsedItemPoints\':".$UsedItemPoints.",\'IsPrint\':\'".$IsPrint."\',\'GiftCardFrom\':\'".$GiftCardFrom."\',\'GiftCardTo\':\'".$GiftCardTo."\',\'GiftCardContent\':\'".$GiftCardContent."\'\}";
            //Mage::log($OrderInformation);
            
            ### get order item info ###
            //$products = $order->getAllItems();
            //$products = $order->getAllItems();
            //Mage::log($products);
            $products = $order->getItemsCollection();
            
            $items = array();
            $i = 0;
            foreach ($products as $product){
		if ($product->getProductType()!='configurable' ) {
		
		$_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product->getSku());
		if($_product->getTypeId() =='configurable') {
        	//var_dump($item);
        	//exit;
        		$price = round($_product->getPrice(), 2); 
        		$sku = $product->getProductOptionByCode('simple_sku');
        	}else{
        		$price = round($_product->getPrice(), 2); 
        		$sku = $product->getSku();
        	}
		
	  	//$sku = $product->getSku();
	  	$name = $product->getName();
	  	//$qty = round($product->getQty()); 
	  	$qty = round($product->getQtyOrdered());
	    $quote_str = array(
	    	'OnlineOrderID' => $OnlineOrderID,
	    	'ProductID' => $sku,
	    	'ProductType' => '0',
	    	'Quantity' => $qty,
	    	'BasePrice' => $price*$qty,
	    	'Price' => $price,
	    	'UsedItemPoints' => '0'
	    );   
	    //$quotes_str .= $quote_str;
	    
	    $item = "\{\'OnlineOrderID\':\'".$quote_str['OnlineOrderID']."\',\'ProductID\':\'".$quote_str['ProductID']."\',\'ProductType\':".$quote_str['ProductType'].",\'Quantity\':".$quote_str['Quantity'].",\'BasePrice\':\'".$quote_str['BasePrice']."\',\'Price\':\'".$quote_str['Price']."\',\'UsedItemPoints\':".$quote_str['UsedItemPoints']."\}";
	    
	    $items[$i] = $item;
	    $i++;
	    //Mage::log($items);
	    }
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
            $ship_addr =  $order->getShippingAddress();
            $addr_info = $ship_addr->getData();
            //Mage::log($addr_info);
            
            $OnlineOrderID = $OnlineOrderID;
            $DeliveryType = 'EMS_COD1';
            //$ReceiverName = $ship_addr->getLastname().$ship_addr->getFirstname();
            $ReceiverName = $ship_addr->getFirstname().$ship_addr->getLastname();
            $ReceiverProvince = $ship_addr->getRegion();
            $ReceiverCity = $ship_addr->getCity();
            $ReceiverAddress = $addr_info['street'];
            $ReceiverZip = $ship_addr->getPostcode();
            $ReceiverAreaCode = $ship_addr->getPostcode();
            $ReceiverTelePhone = $ship_addr->getFax();
            $ReceiverMobile = $ship_addr->getTelephone();
            
            $ReceiverAddress = str_replace(' ','\ ', $ReceiverAddress);
            
            $DeliveryInformation = "\{\'OnlineOrderID\':\'".$OnlineOrderID."\',\'DeliveryType\':\'".$DeliveryType."\',\'ReceiverName\':\'".$ReceiverName."\',\'ReceiverProvince\':\'".$ReceiverProvince."\',\'ReceiverCity\':\'".$ReceiverCity."\',\'ReceiverAddress\':\'".$ReceiverAddress."\',\'ReceiverZip\':\'".$ReceiverZip."\',\'ReceiverAreaCode\':\'".$ReceiverAreaCode."\',\'ReceiverTelePhone\':\'".$ReceiverTelePhone."\',\'ReceiverMobile\':\'".$ReceiverMobile."\'\}";
            
            
            // order info to python 
            $addorder = "/var/www/shell/addorder.py -o ".$OrderInformation." -i ".$OrderItemsInformation." -d ".$DeliveryInformation;
            
			
			$returnstatus="4";
			$order->setOmcstatus($returnstatus)->save();
			Mage::log($addorder, null, 'omc.log');
			try{
				$returnstatus=shell_exec($addorder);
				Mage::log($returnstatus, null,'omc.log');
			
			}
			catch(Exception $e) {
				Mage::log($e, null, 'omc.log');
			}
            //passthru('python /var/www/shell/addorder.py'.$addorder);
            //exit;
			//$this->_redirect('checkout/onepage/success');

        }
	}

	/**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
	protected function saveInvoice(Mage_Sales_Model_Order $order)
	{
		if ($order->canInvoice()) {
			$convertor = Mage::getModel('sales/convert_order');
			$invoice = $convertor->toInvoice($order);
			foreach ($order->getAllItems() as $orderItem) {
				if (!$orderItem->getQtyToInvoice()) {
					continue;
				}
				$item = $convertor->itemToInvoiceItem($orderItem);
				$item->setQty($orderItem->getQtyToInvoice());
				$invoice->addItem($item);
			}
			$invoice->collectTotals();
			$invoice->register()->capture();
			Mage::getModel('core/resource_transaction')
			->addObject($invoice)
			->addObject($invoice->getOrder())
			->save();
			return true;
		}

		return false;
	}

	/**
	 *  Success payment page
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function successAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getAlipayPaymentQuoteId());
		$session->unsAlipayPaymentQuoteId();

		$order = $this->getOrder();

		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}

		$order->addStatusToHistory(
		$order->getStatus(),
		Mage::helper('alipay')->__('Customer successfully returned from Alipay')
		);
		$order->save();
		$this->_redirect('checkout/onepage/success');
	}

  public function loginAction()
	{
	  $alipaylogin    = Mage::getStoreConfig('payment/alipay_payment/alipaylogin');
	  if($alipaylogin == 1){
  		$this->getResponse()
  		->setBody($this->getLayout()
  		->createBlock('alipay/login')
  		->toHtml());
		}else{
		  $this->_redirect('customer/account/login');
    }
	}
	
	public function backAction()
	{
	  $notify = Mage::getModel('alipay/notify');
	  $verify_result = $notify->confirm($_GET);
	  $_GET['email'] = 'alipay_'.$_GET['email'];
	  if($verify_result){
	     if($this->checkVipExist($_GET['email'])){
	       $this->getResponse()->setBody($this->getLayout()->createBlock('alipay/loginbk')->toHtml());
       }else{
         $this->getResponse()->setBody($this->getLayout()->createBlock('alipay/loginpost')->toHtml());
       }
    }else{
      $this->_redirect('/');
    }
	}
  
  function checkVipExist($email)
  {
     $flag = true;
     $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('email');
     $collection->load();
     foreach ($collection as $_customer){
       if($_customer['email']==$email){
         $flag = false;
         break;
       }
     }
     return $flag;
  }

	/**
	 *  Failure payment page
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function errorAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$errorMsg = Mage::helper('alipay')->__(' There was an error occurred during paying process.');

		$order = $this->getOrder();

		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}
		if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
			$order->addStatusToHistory(
			Mage_Sales_Model_Order::STATE_CANCELED,//$order->getStatus(),
			Mage::helper('alipay')->__('Customer returned from Alipay.') . $errorMsg
			);

			$order->save();
		}

		$this->loadLayout();
		$this->renderLayout();
		Mage::getSingleton('checkout/session')->unsLastRealOrderId();
	}
	
	/**
   * 接口调用记录
   *
   * 使用时注意日志存放位置(****)
   * 
   * @param        String      $function_name(调用方法名)
   * @param        String      $postData(调用或返回数据)
   * @param        String      $inout(操作方式)
   * @param        String      $msg(异常信息)
   *  
   * @access       public   
   */
	function logvarRS($function_name,$postData,$inout,$msg=''){
		//define( 'DS', DIRECTORY_SEPARATOR );
		$content = array(
		'function_name' => $function_name,
		$inout      	=> $postData,
		'msg'           => $msg
		);
		$_path = dirname(__FILE__).DS;
		ini_set('date.timezone','PRC');
		file_put_contents($_path.date('Ymd').'.txt',date('Y-m-d H:i:s')."\n".var_export($content,true)."\n"."\n",FILE_APPEND);
	}
}
