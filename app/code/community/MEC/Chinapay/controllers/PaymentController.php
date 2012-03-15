<?php
	include_once("/var/www/app/code/community/MEC/Chinapay/Model/netpayclient_config.php");
	//加载 netpayclient 组件
	include_once("/var/www/app/code/community/MEC/Chinapay/Model/netpayclient.php");
?>

<?php
class MEC_Chinapay_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
     
    public function getOrder()
    {
        if ($this->_order == null)
        {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     * When a customer chooses Chinapay on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setChinapayPaymentQuoteId($session->getQuoteId());

        $order = $this->getOrder();

        if (!$order->getId())
        {
            $this->norouteAction();
            return;
        }

        $order->addStatusToHistory(
        $order->getStatus(),
        Mage::helper('chinapay')->__('Customer was redirected to Chinapay')
        );
		
		$returnstatus="-4";
		$order->setOmcstatus($returnstatus);
	
		$order->setStatus("online_unpaid");
        $order->save();

        $this->getResponse()
        ->setBody($this->getLayout()
        ->createBlock('chinapay/redirect')
        ->setOrder($order)
        ->toHtml());

        $session->unsQuoteId();
    }

    public function notifyAction()
    {

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
           // Mage::log('postdata');
           // Mage::log($postData);
            $method = 'post';
            	//获取交易应答的各项值
		$merid = $postData["merid"];
		$orderno = $postData["orderno"];
		$transdate = $postData["transdate"];
		$amount = $postData["amount"];
		$currencycode = $postData["currencycode"];
		$transtype = $postData["transtype"];
		$status = $postData["status"];
		$checkvalue = $postData["checkvalue"];
		$gateId = $postData["GateId"];
		$priv1 = $postData["Priv1"];


        } else if ($this->getRequest()->isGet())
        {
            $postData = $this->getRequest()->getQuery();
            $method = 'get';
			//Mage::log('getdata');
            //Mage::log($postData);
            
            
            
        } else
        {
            return;
        }
	
	
	$flag = buildKey(PUB_KEY);
	if(!$flag) {
		echo "导入公钥文件失败！";
		exit;
	}
	
	$flag = verifyTransResponse($merid, $orderno, $amount, $currencycode, $transdate, $transtype, $status, $checkvalue);
	if($flag == 'true') {
        if ($status == '1001')
        {
            $model = Mage::getModel('chinapay/payment');
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($priv1);
            //$order->setChinapayTradeno($postData['trade_no']);
            $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
            // $order->sendNewOrderEmail();
            $order->addStatusToHistory(
            //$order->getStatus(),
            //'processing',
            $model->getConfigData('order_status_payment_accepted'),
            Mage::helper('chinapay')->__('买家已付款， 等待卖家发货。'),
            true
            );
            //$this->saveInvoice($order);
            try
            {
                $order->save();
            } catch(Exception $e)
            {
                ;
            }
            //$this->_redirect('checkout/onepage/success');
            $id = Mage::getSingleton('customer/session')->getCustomerId();
			
			
			
            ### get order info ###
            $OnlineOrderID = $priv1;
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
	    Mage::log($FapiaoTitle);
            if($FapiaoTitle == '^^^') {
            $IsFapiao = 'No';
            }else{
            $IsFapiao = 'Yes'.'^^^'.$FapiaoTitle;
            }
            $shipping_time = $order->getShippingTime(); 
            $Remark = '0'.'^^^'.$IsFapiao.'^^^'.$shipping_time;
            
            $GiftCardFrom = str_replace(' ','\ ', $GiftCardFrom);
            $GiftCardTo = str_replace(' ','\ ', $GiftCardTo);
            $GiftCardContent = str_replace(' ','\ ', $GiftCardContent);
            $Remark =  str_replace(' ','\ ', $Remark); 
                    
            
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
	  	
	  	
	  	$name = $product->getName();
	  	//$qty = round($product->getQty()); 
	  	$qty = round($product->getQtyOrdered());
	    $quote_str = array(
	    	'OnlineOrderID' => $priv1,
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
            
            $OnlineOrderID = $priv1;
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
            

        }
	}
        return;
    }

     /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function saveInvoice(Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice())
        {
            $convertor = Mage::getModel('sales/convert_order');
            $invoice = $convertor->toInvoice($order);
            foreach ($order->getAllItems() as $orderItem)
            {
                if (!$orderItem->getQtyToInvoice())
                {
                    continue ;
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
        $this->getIcePayResponseUrl();
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getChinapayPaymentQuoteId());
        $session->unsChinapayPaymentQuoteId();

        $order = $this->getOrder();

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            $method = 'post';
            	//获取交易应答的各项值
		$merid = $postData["merid"];
		$orderno = $postData["orderno"];
		$transdate = $postData["transdate"];
		$amount = $postData["amount"];
		$currencycode = $postData["currencycode"];
		$transtype = $postData["transtype"];
		$status = $postData["status"];
		$checkvalue = $postData["checkvalue"];
		$gateId = $postData["GateId"];
		$priv1 = $postData["Priv1"];


        } else if ($this->getRequest()->isGet())
        {
            $postData = $this->getRequest()->getQuery();
            $method = 'get';

        } else
        {
            return;
        }

        if ($status == '1001')
        {
            $model = Mage::getModel('chinapay/payment');
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($priv1);
            //$order->setChinapayTradeno($postData['trade_no']);
            $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
            // $order->sendNewOrderEmail();
            $order->addStatusToHistory(
            //$order->getStatus(),
            //'processing',
            $model->getConfigData('order_status_payment_accepted'),
            Mage::helper('chinapay')->__('买家从chinapay返回， 等待确认付款。'),
            true
            );
            try
            {
                $order->save();
            } catch(Exception $e)
            {
                ;
            }

        }
        $this->_redirect('checkout/onepage/success');
    }
    
    protected function getTenPayResponseUrl()
	{
		$responseArr['result'] 				= $this->getRequest()->pay_result;
		$responseArr['sp_billno'] 			= $this->getRequest()->sp_billno;
		$this->responseArr = $responseArr;
		if (is_array($this->responseArr) && sizeof($this->responseArr) > 0) {
			$this->_WHAT_STATUS = true;
		}
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
        $errorMsg = Mage::helper('chinapay')->__(' There was an error occurred during paying process.');

        $order = $this->getOrder();

        if (!$order->getId())
        {
            $this->norouteAction();
            return;
        }
        if ($order instanceof Mage_Sales_Model_Order && $order->getId())
        {
            $order->addStatusToHistory(
            Mage_Sales_Model_Order::STATE_CANCELED,//$order->getStatus(),
            Mage::helper('chinapay')->__('Customer returned from Chinapay.').$errorMsg
            );

            $order->save();
        }

        $this->loadLayout();
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
    }
}
