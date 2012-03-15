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
 * @package    Chinabank
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Alipay Payment Front Controller
 *
 * @category   Mage
 * @package    Chinabank
 * @name       Chinabank_PaymentController
 * @author	   Magento Core Team <core@magentocommerce.com>, Quadra Informatique - Nicolas Fischer <nicolas.fischer@quadra-informatique.fr>
 */
class Mage_Chinabank_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order  ��ö���
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
    
    public function payAction()
  	{
      $data=$this->getRequest()->getQuery();
      $order = Mage::getModel('sales/order')->loadByIncrementId($data['orderid']);      	      
  		$this->getResponse()
  			->setBody($this->getLayout()
  				->createBlock('chinabank/redirect')
  				->setOrder($order)
  				->toHtml());
      }
    /**
     * When a customer chooses Alipay on Checkout/Payment page
     *  ֧�����
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
			Mage::helper('chinabank')->__('Customer was redirected to Alipay')
		);
		
    //exit(var_dump(sprintf('%.2f',$order->getShipping_amount())));
		$order->save();
  
		$this->getResponse()
			->setBody($this->getLayout()
				->createBlock('chinabank/redirect')
				->setOrder($order)
				->toHtml());

        $session->unsQuoteId();
    }

	/**
	 *  Alipay response router
	 *
	 *  @param    none
	 *  @return	  void
	 */
  	public function notifyAction()
  	{  
  		$model = Mage::getModel('chinabank/payment');
  		
      if ($this->getRequest()->isPost()) {
  			$postData = $this->getRequest()->getPost();
        $method   = 'post';
  
  		} else if ($this->getRequest()->isGet()) {
  			$postData = $this->getRequest()->getQuery();
  			$method   = 'get';
  
  		} else {
  			$model->generateErrorResponse();
  		};
  
  	   
  		$order = Mage::getModel('sales/order')
  			->loadByIncrementId($postData['v_oid']);
  
  		if (!$order->getId()) {
  			$model->generateErrorResponse();
  		}
  	
				$order->addStatusToHistory(
					$model->getConfigData('order_status_payment_accepted'),
					Mage::helper('chinabank')->__('Payment accepted by Alipay')
				);
				
				$order->sendNewOrderEmail();

				$this->saveInvoice($order);
  				
  			$order->save();
  			$this->_redirect('checkout/onepage/success');
  			/*$this->getResponse()
    			->setBody($this->getLayout()
    				->createBlock('chinabank/success')
    				->setOrder($order)
    				->toHtml());*/
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
			Mage::helper('chinabank')->__('Customer successfully returned from Alipay')
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
        $errorMsg = Mage::helper('chinabank')->__(' There was an error occurred during paying process.');

        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }
        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            $order->addStatusToHistory(
                Mage_Sales_Model_Order::STATE_CANCELED,//$order->getStatus(),
                Mage::helper('chinabank')->__('Customer returned from Alipay.') . $errorMsg
            );
            
            $order->save();
        }

        $this->loadLayout();
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
    }    
}
