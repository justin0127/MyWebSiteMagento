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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('view', 'index');

    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        return $this;
    }

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    /**
     * Orders grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders'));

        $this->_initAction()
            ->renderLayout();
    }

    public function orderreportAction()
    {
       $this->_title($this->__('Order Report'))->_title($this->__('Order Report'));
	
		
        $this->loadLayout();
        $this->renderLayout();
    }
    public function sendCustomerToArvato($customer){
		if($customer->getPosId() != NULL) {
			$OnlineCustomerID = $customer->getPosId();
			$CustomerName = $customer->getFirstname().$customer->getLastname();
			$CustomerTitle = '0';
			$CustomerBirthday = '1900-01-01';
			$CustomerProvince = '0';
			$CustomerCity = '0';
			$CustomerAddress = '0';
			$CustomerZip = '0';
			$CustomerEmail = $customer->getEmail();
			$CustomerAreaCode = '0';
			$CustomerTele = '0';
			$CustomerMobile = $customer->getMobile()?$customer->getMobile():"0";
			$ParentID = '0';
			$Userrank = '0';
			$Msn = '0';
			$Qq = '0';
			$Officephone = '0';
			$Alias = $customer->getAlias();
			// replace ' ' with '\ ' to avoid python error.
			$CustomerName = str_replace(' ','\ ', $CustomerName);
			$CustomerTitle = str_replace(' ','\ ', $CustomerTitle);
			$CustomerAddress = str_replace(' ','\ ', $CustomerAddress);
			$Alias = str_replace(' ','\ ', $Alias);
			
			$onlineAdd = "/var/www/shell/addcustomer.py -c \{\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'CustomerName\':\'".$CustomerName."\',\'CustomerTitle\':\'".$CustomerTitle."\',\'CustomerBirthday\':\'".$CustomerBirthday."\',\'CustomerProvince\':\'".$CustomerProvince."\',\'CustomerCity\':\'".$CustomerCity."\',\'CustomerAddress\':\'".$CustomerAddress."\',\'CustomerZip\':\'".$CustomerZip."\',\'CustomerEmail\':\'".$CustomerEmail."\',\'CustomerAreaCode\':\'".$CustomerAreaCode."\',\'CustomerTele\':\'".$CustomerTele."\',\'CustomerMobile\':\'".$CustomerMobile."\',\'ParentID\':\'".$ParentID."\',\'Userrank\':\'".$Userrank."\',\'Msn\':\'".$Msn."\',\'Qq\':\'".$Qq."\',\'Officephone\':\'".$Officephone."\',\'Alias\':\'".$Alias."\'\}";
			//Mage::log($onlineAdd);
//return true;
			//print_r($onlineAdd);
			//exit();
			$this->_getSession()->addSuccess($onlineAdd);
			$return=shell_exec($onlineAdd);
			$this->_getSession()->addSuccess($return);
			/***  end insert  ***/
			$this->_getSession()->addSuccess($this->__('Cusomter was sent to arvato successfully.'));
			return true;
		}
		$this->_getSession()->addError($this->__("Customer don't have POS ID."));
		return false;
	}
    public function sendOrderToArvato($order,$customer){
		$orderId = $order->getIncrementId();
		$OnlineOrderID = $orderId;
			
		/*  gift message  */		
		$gift_message_id = $order->getGiftMessageId();
		$giftMessage = Mage::getModel('giftmessage/message')->load((int)$gift_message_id);
		$IsPrint =($giftMessage->getData('print') == 'no')?'0':'1';
		$GiftCardFrom = $giftMessage->getData('sender')?$giftMessage->getData('sender'):'0';
		$GiftCardTo = $giftMessage->getData('recipient')? $giftMessage->getData('recipient'):'0';
		$GiftCardContent = $giftMessage->getData('message')? $giftMessage->getData('message'):'0';
		/*  end gift message  */
		
		
		$DateOfOrder = date("Y-m-d");
		
		$OrderSource = '10';
		$OnlineCustomerID = $customer->getPosId();
		$RetailAmountOfOrder = strval($order->getBaseGrandTotal());
		$ActualAmountOfOrder = strval($order->getBaseGrandTotal());
		$DiscountAmountOfOrder = '0';
		
		$paymentcode = $order->getPayment()->getMethod();
		$Payment = ('cashondelivery' == $paymentcode)? '0':'1';
		
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
		$TotalProductAmount = $RetailAmountOfOrder-$ShippingCost-$PaymentCost;
		$InvoiceTitle = '0';
		
		$UsedPoints = '0';
		$Package = 'Normal';
		$PackageCost = '0';
		$PromotionCode = '0';
		
		$FapiaoTitle = $order->getFapiaoTitle(); 
		$FapiaoTitle = str_replace(' ','\ ', $FapiaoTitle);
		$IsFapiao =($FapiaoTitle == '^^^')? 'No':'Yes'.'^^^'.$FapiaoTitle;
		$Remark = '0'.'^^^'.$IsFapiao.'^^^'.$order->getShippingTime();
		$UsedItemPoints = '0';
		
		// replace ' ' with '\ ' to avoid python error.
		$GiftCardFrom = str_replace(' ','\ ', $GiftCardFrom);
		$GiftCardTo = str_replace(' ','\ ', $GiftCardTo);
		$GiftCardContent = str_replace(' ','\ ', $GiftCardContent);
		$GiftCardContent = str_replace(',','\,', $GiftCardContent);
		$GiftCardContent = str_replace("\n",'', $GiftCardContent);
		$GiftCardContent = str_replace("\r",'', $GiftCardContent);
		$GiftCardContent = str_replace("(",'\ ', $GiftCardContent);
		$GiftCardContent = str_replace(")",'\ ', $GiftCardContent);
		$GiftCardContent = str_replace("!",'\!', $GiftCardContent);
		$GiftCardContent = str_replace("'",'\'', $GiftCardContent);
		$GiftCardContent = str_replace("`",'\`', $GiftCardContent);
		$GiftCardContent = str_replace("’",'\’', $GiftCardContent);
		$GiftCardContent = str_replace("'","\'", $GiftCardContent);
		//$GiftCardContent="";
		$Remark = str_replace(' ','\ ', $Remark);
		
		$OrderInformation = "\{\'OnlineOrderID\':\'".$OnlineOrderID."\',\'DateOfOrder\':\'".$DateOfOrder."\',\'OrderSource\':\'".$OrderSource."\',\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'RetailAmountOfOrder\':\'".$RetailAmountOfOrder."\',\'ActualAmountOfOrder\':\'".$ActualAmountOfOrder."\',\'DiscountAmountOfOrder\':\'".$DiscountAmountOfOrder."\',\'Payment\':".$Payment.",\'PaymentChannel\':".$PaymentChannel.",\'PaymentCost\':\'".$PaymentCost."\',\'TotalProductAmount\':\'".$TotalProductAmount."\',\'InvoiceTitle\':\'".$InvoiceTitle."\',\'ShippingCost\':\'".$ShippingCost."\',\'UsedPoints\':".$UsedPoints.",\'Package\':\'".$Package."\',\'PackageCost\':\'".$PackageCost."\',\'PromotionCode\':\'".$PromotionCode."\',\'Remark\':\'".$Remark."\',\'UsedItemPoints\':".$UsedItemPoints.",\'IsPrint\':\'".$IsPrint."\',\'GiftCardFrom\':\'".$GiftCardFrom."\',\'GiftCardTo\':\'".$GiftCardTo."\',\'GiftCardContent\':\'".$GiftCardContent."\'\}";
		
		$products = $order->getAllVisibleItems();
		
		$items = array();
		$i = 0;
		foreach ($products as $product){
				
			$name = $product->getName();
			$qty = round($product->getQtyOrdered()); 
			$price = round($product->getPrice(), 2); 
			
			if($product->getProductOptionByCode() && $product->getProductType()!="simple") {
				$sku = $product->getProductOptionByCode('simple_sku');
			}else{
				$sku = $product->getSku();
			}	  
			//echo "调试中，请稍后再用";
			//echo $sku;
			//exit();
			$quote_str = array(
				'OnlineOrderID' => $orderId,
				'ProductID' => $sku,
				'ProductType' => '0',
				'Quantity' => $qty,
				'BasePrice' => $price*$qty,
				//'Price' => $price,
				'Price' => $price,
				'UsedItemPoints' => '0'
			);   
			$item = "\{\'OnlineOrderID\':\'".$quote_str['OnlineOrderID']."\',\'ProductID\':\'".$quote_str['ProductID']."\',\'ProductType\':".$quote_str['ProductType'].",\'Quantity\':".$quote_str['Quantity'].",\'BasePrice\':\'".$quote_str['BasePrice']."\',\'Price\':\'".$quote_str['Price']."\',\'UsedItemPoints\':".$quote_str['UsedItemPoints']."\}";
			$items[$i] = $item;
			$i++;
			
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
		$ship_addr = $order->getShippingAddress();
		$addr_info = $ship_addr->getData();
		//Mage::log($addr_info);
		
		$OnlineOrderID = $orderId;
		$DeliveryType = 'EMS_COD1';
		$ReceiverName = trim($ship_addr->getFirstname().$ship_addr->getLastname());
		$ReceiverName = str_replace(' ','\ ', $ReceiverName);
		$ReceiverProvince = $ship_addr->getRegion();
		$ReceiverCity = $ship_addr->getCity();
		
		//print_r($addr_info); 
		//exit();
		$ReceiverAddress = $addr_info['street'];
		$ReceiverAddress = str_replace(' ','\ ', $ReceiverAddress);
		$ReceiverAddress = str_replace('(','\(', $ReceiverAddress);
		$ReceiverAddress = str_replace(')','\)', $ReceiverAddress);
		$ReceiverZip = $ship_addr->getPostcode();
		$ReceiverZip = str_replace(' ','\ ', $ReceiverZip);
		$ReceiverAreaCode = $ship_addr->getPostcode();
		$ReceiverAreaCode = str_replace(' ','\ ', $ReceiverAreaCode);
		$ReceiverTelePhone = $ship_addr->getFax();
		$ReceiverTelePhone = str_replace(' ','\ ', $ReceiverTelePhone);
		$ReceiverMobile = $ship_addr->getTelephone();
		$ReceiverMobile = str_replace(' ','\ ', $ReceiverMobile);

		
		$DeliveryInformation = "\{\'OnlineOrderID\':\'".$OnlineOrderID."\',\'DeliveryType\':\'".$DeliveryType."\',\'ReceiverName\':\'".$ReceiverName."\',\'ReceiverProvince\':\'".$ReceiverProvince."\',\'ReceiverCity\':\'".$ReceiverCity."\',\'ReceiverAddress\':\'".$ReceiverAddress."\',\'ReceiverZip\':\'".$ReceiverZip."\',\'ReceiverAreaCode\':\'".$ReceiverAreaCode."\',\'ReceiverTelePhone\':\'".$ReceiverTelePhone."\',\'ReceiverMobile\':\'".$ReceiverMobile."\'\}";
		
		//Mage::log('7@');
		// order info to python 
		$addorder = "/var/www/shell/addorder.py -o ".$OrderInformation." -i ".$OrderItemsInformation." -d ".$DeliveryInformation;
		//Mage::log($addorder);
		//echo "F";
		//print_r($addorder);
		//print_r($x);
		//exit();
		
		$x=(shell_exec($addorder));
		$this->_getSession()->addSuccess($addorder);
		$this->_getSession()->addSuccess($x);
		//passthru('python /var/www/shell/addorder.py'.$addorder);
		//exit;
		try{
		$returnstatus=shell_exec($addorder);
		
		$order->setOmcstatus($returnstatus)->save();
		$this->_getSession()->addSuccess($addorder);
		$this->_getSession()->addSuccess($returnstatus);
		} catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e);
		} catch (Exception $e) {
			$this->_getSession()->addError($e);
			Mage::logException($e);
		}
		
		return true;
	}
    public function resendAction()
    {
		//added by cosmo
		//needed to merged into Sales/Order Model
		
        if ($order = $this->_initOrder()) {
            $this->_initAction();
			if($order->getCustomerId()){
				$customer= Mage::getModel('customer/customer')->load($order->getCustomerId());
				if($customer->getId()){
					if($this->sendCustomerToArvato($customer)){
					
						if($this->sendOrderToArvato($order,$customer)){
							$this->_getSession()->addSuccess($this->__('Order was sent to arvato successfully.'));
							$this->_redirect('*/*/');
							return;
						}else{
							$this->_getSession()->addError($this->__('Order was sent to arvato failed.'));
							$this->_redirect('*/*/');
							return;
						}
						
					}else{
						$this->_getSession()->addError($this->__('Cusomter was sent to arvato failed.'));
						$this->_redirect('*/*/');
						return;
					}
				}else{
					$this->_getSession()->addError($this->__('Cusomter not exist or throw exception when loading.'));
					$this->_redirect('*/*/');
					return;
				}
			}else{
				$this->_getSession()->addError($this->__("Order's cusomter is empty."));
				$this->_redirect('*/*/');
				return;
			}
        }
		
    }
    /**
     * Order grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * View order detale
     */
    public function viewAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders'));

        if ($order = $this->_initOrder()) {
            $this->_initAction();

            $this->_title(sprintf("#%s", $order->getRealOrderId()));

            $this->renderLayout();
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $order->sendNewOrderEmail();
                $this->_getSession()->addSuccess($this->__('The order email has been sent.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Failed to send the order email.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }
    /**
     * Cancel order
     */
    public function cancelAction()
    {
        if ($order = $this->_initOrder()) {
            try {
			
				$orderId = $order->getIncrementId();
				$DateOfOrder = date("Y-m-d");
					
				$cancelInformation = "\{\'OnlineOrderID\':\'".$orderId."\',\'CancelDate\':\'".$DateOfOrder."\',\'CancelOption\':\'"."cancel"."\',\'CancelReason\':\'"."cancel"."\'\}";
				
				$cancelorder = "/var/www/shell/cancelorder.py -o ".$cancelInformation;
				 
				$x=(shell_exec($cancelorder));
				$this->_getSession()->addSuccess($cancelorder);
				$this->_getSession()->addSuccess("返回信息：".$x);			
			
			
                $order->cancel()
                    ->save();
                $this->_getSession()->addSuccess(
                    $this->__('The order has been cancelled.')
                );
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                Mage::logException($e);
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Hold order
     */
    public function obflagAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $order->setObflag(1)
                    ->save();
                $this->_getSession()->addSuccess(
                    $this->__('The order OB Flag has been set to 1.')
                );
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }
	
	
    public function holdAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $order->hold()
                    ->save();
                $this->_getSession()->addSuccess(
                    $this->__('The order has been put on hold.')
                );
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order was not put on hold.'));
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Unhold order
     */
    public function unholdAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $order->unhold()
                    ->save();
                $this->_getSession()->addSuccess(
                    $this->__('The order has been released from holding status.')
                );
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order was not unheld.'));
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Manage payment state
     *
     * Either denies or approves a payment that is in "review" state
     */
    public function reviewPaymentAction()
    {
        try {
            if (!$order = $this->_initOrder()) {
                return;
            }
            $action = $this->getRequest()->getParam('action', '');
            switch ($action) {
                case 'accept':
                    $order->getPayment()->accept();
                    $message = $this->__('The payment has been accepted.');
                    break;
                case 'deny':
                    $order->getPayment()->deny();
                    $message = $this->__('The payment has been denied.');
                    break;
                case 'update':
                    $order->getPayment()
                        ->registerPaymentReviewAction(Mage_Sales_Model_Order_Payment::REVIEW_ACTION_UPDATE, true);
                    $message = $this->__('Payment update has been made.');
                    break;
                default:
                    throw new Exception(sprintf('Action "%s" is not supported.', $action));
            }
            $order->save();
            $this->_getSession()->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to update the payment.'));
            Mage::logException($e);
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }

    /**
     * Add order comment action
     */
    public function addCommentAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $order->addStatusHistoryComment($data['comment'], $data['status'])
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));

                $order->save();
                $order->sendOrderUpdateEmail($notify, $comment);

                $this->loadLayout('empty');
                $this->renderLayout();
            }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

    /**
     * Generate invoices grid for ajax request
     */
    public function invoicesAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_invoices')->toHtml()
        );
    }

    /**
     * Generate shipments grid for ajax request
     */
    public function shipmentsAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_shipments')->toHtml()
        );
    }

    /**
     * Generate creditmemos grid for ajax request
     */
    public function creditmemosAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_creditmemos')->toHtml()
        );
    }

    /**
     * Generate order history for ajax request
     */
    public function commentsHistoryAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_history')->toHtml()
        );
    }

    /**
     * Cancel selected orders
     */
    public function massCancelAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countCancelOrder = 0;
        $countNonCancelOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->canCancel()) {
                $order->cancel()
                    ->save();
                $countCancelOrder++;
            } else {
                $countNonCancelOrder++;
            }
        }
        if ($countNonCancelOrder) {
            if ($countCancelOrder) {
                $this->_getSession()->addError($this->__('%s order(s) cannot be canceled', $countNonCancelOrder));
            } else {
                $this->_getSession()->addError($this->__('The order(s) cannot be canceled'));
            }
        }
        if ($countCancelOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been canceled.', $countCancelOrder));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Hold selected orders
     */
    public function massHoldAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countHoldOrder = 0;
        $countNonHoldOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->canHold()) {
                $order->hold()
                    ->save();
                $countHoldOrder++;
            } else {
                $countNonHoldOrder++;
            }
        }
        if ($countNonHoldOrder) {
            if ($countHoldOrder) {
                $this->_getSession()->addError($this->__('%s order(s) were not put on hold.', $countNonHoldOrder));
            } else {
                $this->_getSession()->addError($this->__('No order(s) were put on hold.'));
            }
        }
        if ($countHoldOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been put on hold.', $countHoldOrder));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Unhold selected orders
     */
    public function massUnholdAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countUnholdOrder = 0;
        $countNonUnholdOrder = 0;

        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->canUnhold()) {
                $order->unhold()
                    ->save();
                $countUnholdOrder++;
            } else {
                $countNonUnholdOrder++;
            }
        }
        if ($countNonUnholdOrder) {
            if ($countUnholdOrder) {
                $this->_getSession()->addError($this->__('%s order(s) were not released from holding status.', $countNonUnholdOrder));
            } else {
                $this->_getSession()->addError($this->__('No order(s) were released from holding status.'));
            }
        }
        if ($countUnholdOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been released from holding status.', $countUnholdOrder));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Change status for selected orders
     */
    public function massStatusAction()
    {

    }

    /**
     * Print documents for selected orders
     */
    public function massPrintAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $document = $this->getRequest()->getPost('document');
    }

    public function pdfinvoicesAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($invoices->getSize() > 0) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }

        }
        $this->_redirect('*/*/');

    }

    public function pdfshipmentsAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($shipments->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse('packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    public function pdfcreditmemosAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse('creditmemo'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    public function pdfdocsAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($invoices->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($shipments->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse('docs'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Atempt to void the order payment
     */
    public function voidPaymentAction()
    {
        if (!$order = $this->_initOrder()) {
            return;
        }
        try {
            $order->getPayment()->void(
                new Varien_Object() // workaround for backwards compatibility
            );
            $order->save();
            $this->_getSession()->addSuccess($this->__('The payment has been voided.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to void the payment.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/view', array('order_id' => $order->getId()));
    }

    protected function _isAllowed()
    {
        if ($this->getRequest()->getActionName() == 'view') {
            return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view');
        }
        return Mage::getSingleton('admin/session')->isAllowed('sales/order');
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportOrderreportAction()
    {
        $fileName   = 'Orderreport.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_orderreport');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Order transactions grid ajax action
     *
     */
    public function transactionsAction()
    {
        $this->_initOrder();
        $this->loadLayout(false);
        $this->renderLayout();
    }
}
