<?php
 /**
 * CosmoCommerce
 *
 * NOTICE OF LICENSE
 * CosmoCommerce Commercial License 
 * support@cosmocommerce.com
 *
 * @category   CosmoCommerce
 * @package    CosmoCommerce_Udpay
 * @copyright  Copyright (c) 2009 CosmoCommerce,LLC. (http://www.cosmocommerce.com)
 * @license	     CosmoCommerce Commercial License(http://www.cosmocommerce.com/cosmocommerce_commercial_license.txt)
 */

/**
 * Udpay Payment Front Controller
 *
 * @category   Mage
 * @package    CosmoCommerce_Udpay
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */
class CosmoCommerce_Udpay_PaymentController extends Mage_Core_Controller_Front_Action
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
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     * When a customer chooses Udpay on Checkout/Payment page
     *
     */
	public function redirectAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$session->setUdpayPaymentQuoteId($session->getQuoteId());

		$order = $this->getOrder();

		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}

		$order->addStatusToHistory(
			$order->getStatus(),
			Mage::helper('udpay')->__('Customer was redirected to Udpay')
		);
		$order->save();

		$this->getResponse()
			->setBody($this->getLayout()
				->createBlock('udpay/redirect')
				->setOrder($order)
				->toHtml());

        $session->unsQuoteId();
    }

	/**
	 *  Udpay response router
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function notifyAction()
	{
		$model = Mage::getModel('udpay/payment');
        
        if ($this->getRequest()->isPost()) {
			$postData = $this->getRequest()->getPost();
        	$method = 'post';

		} else if ($this->getRequest()->isGet()) {
			$postData = $this->getRequest()->getQuery();
			$method = 'get';

		} else {
			$model->generateErrorResponse();
		}



		$order = Mage::getModel('sales/order')
			->loadByIncrementId($postData['reference']);

		if (!$order->getId()) {
			$model->generateErrorResponse();
		}

		if ($returnedMAC == $correctMAC) {
			if (1) {
				$order->addStatusToHistory(
					$model->getConfigData('order_status_payment_accepted'),
					Mage::helper('udpay')->__('Payment accepted by Udpay')
				);
				
				$order->sendNewOrderEmail();

				if ($this->saveInvoice($order)) {
//                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
				}
				
			 } else {
			 	$order->addStatusToHistory(
					$model->getConfigData('order_status_payment_refused'),
					Mage::helper('udpay')->__('Payment refused by Udpay')
				);
				
				// TODO: customer notification on payment failure
			 }
				
			$order->save();

        } else {
            $order->addStatusToHistory(
                Mage_Sales_Model_Order::STATE_CANCELED,//$order->getStatus(),
                Mage::helper('udpay')->__('Returned MAC is invalid. Order cancelled.')
            );
            $order->cancel();
            $order->save();
            $model->generateErrorResponse();
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
		$session->setQuoteId($session->getUdpayPaymentQuoteId());
		$session->unsUdpayPaymentQuoteId();
		
		$order = $this->getOrder();
		
		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}

		$order->addStatusToHistory(
			$order->getStatus(),
			Mage::helper('udpay')->__('Customer successfully returned from Udpay')
		);
        
		$order->save();
        
		$this->_redirect('checkout/onepage/success');
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
        $errorMsg = Mage::helper('udpay')->__(' There was an error occurred during paying process.');

        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }
        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            $order->addStatusToHistory(
                Mage_Sales_Model_Order::STATE_CANCELED,//$order->getStatus(),
                Mage::helper('udpay')->__('Customer returned from Udpay.') . $errorMsg
            );
            
            $order->save();
        }

        $this->loadLayout();
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
    }
}
