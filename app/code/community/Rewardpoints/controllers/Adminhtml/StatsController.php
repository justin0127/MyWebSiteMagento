<?php
/**
 * J2T RewardsPoint2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@j2t-design.com so we can send you a copy immediately.
 *
 * @category   Magento extension
 * @package    RewardsPoint2
 * @copyright  Copyright (c) 2009 J2T DESIGN. (http://www.j2t-design.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Rewardpoints_Adminhtml_StatsController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
            $this->loadLayout()
                    ->_setActiveMenu('rewardpoints/stats')
                    ->_addBreadcrumb(Mage::helper('rewardpoints')->__('Statistics'), Mage::helper('rewardpoints')->__('Statistics'));

            return $this;
	}

	public function indexAction() {
            $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('rewardpoints/adminhtml_stats'))
                ->renderLayout();
	}

        public function editAction() {
		$id     = $this->getRequest()->getParam('id');		
                $model  = Mage::getModel('rewardpoints/stats')->load($id);
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

			Mage::register('stats_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('rewardpoints/stats');

			$this->_addBreadcrumb(Mage::helper('rewardpoints')->__('Manage Points'), Mage::helper('adminhtml')->__('Manage Points'));
			$this->_addBreadcrumb(Mage::helper('rewardpoints')->__('Point Configuration'), Mage::helper('adminhtml')->__('Point Configuration'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('rewardpoints/adminhtml_stats_edit'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rewardpoints')->__('No points'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction() {
		$this->_forward('edit');
	}

        public function checkpointsAction() {
            $id     = $this->getRequest()->getParam('id');
            $model  = Mage::getModel('rewardpoints/stats')->load($id);
            
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                    $model->setData($data);
            }

            Mage::register('stats_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('rewardpoints/stats');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('rewardpoints/adminhtml_stats_checkpoints'));
            $this->renderLayout();
	}
        
        public function savecheckAction(){
            if ($data = $this->getRequest()->getPost()) {
                //$from = Mage::app()->getLocale()->date($this->getRequest()->getParam('from'), Zend_Date::DATE_SHORT, null, false);
                //$to = Mage::app()->getLocale()->date($this->getRequest()->getParam('ends'), Zend_Date::DATE_SHORT, null, false);

                $date = Mage::app()->getLocale()->date($data['from'], Zend_Date::DATE_SHORT, null, false);
                $time = $date->getTimestamp();
                $from = Mage::getModel('core/date')->gmtDate(null, $time);


                $date = Mage::app()->getLocale()->date($data['ends'], Zend_Date::DATE_SHORT, null, false);
                $time = $date->getTimestamp();
                $to = Mage::getModel('core/date')->gmtDate(null, $time);


                $order_states = array("processing","complete");
                $orders = Mage::getModel('sales/order')->getCollection()
                    ->addAttributeToSelect('*')
                    
                    ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
                    ->joinAttribute('status', 'order/status', 'entity_id', null, 'left');

                $orders_array =array();

                $orders_array[] = '0';
                foreach ($orders as $order){
                   
                    $order = Mage::getModel('sales/order')->load($order->getId());
                    $items = $order->getAllItems();
                    /*
                    $prodIds = array();
                    foreach ($items as $_item) {
                        $prodIds[] = $_item->getProductId();
                    }

                    $prod = Mage::getResourceModel('catalog/product_collection')
                                                ->addAttributeToSelect('reward_points')
                                                ->addIdFilter($prodIds);
                    */
                    $orders_array[] = "'".$order->getIncrementId()."'";

                    $rules = Mage::getModel('rewardpoints/rules')->getPointsByRule();
                    $cart_amount = 0;


                    $rewardPoints = 0;
                    foreach ($items as $_item) {
                        
                        //$_product = $prod->getItemById($_item->getProductId());
                        $_product = Mage::getModel('catalog/product')->load($_item->getProductId());
                        $product_points = $_product->getData('reward_points');

                        if ($product_points > 0){
                            $rewardPoints += $product_points * $_item->getQtyOrdered();
                        } else {
                            /*
                            $price = ($_item->getCalculationPrice() ? $_item->getCalculationPrice() : $_item->getPrice());
                            $qty = ($_item->getQty() ? $_item->getQty() : ($_item->getQtyOrdered() ? $_item->getQtyOrdered() : 1));
                            $tax = ($_item->getTaxBeforeDiscount() ? $_item->getTaxBeforeDiscount() : $_item->getTaxAmount());
                            $rewardPoints += ($price + $tax) * $qty;
                            */
                            $price = $_item->getRowTotal() + $_item->getTaxAmount() - $_item->getDiscountAmount();
                            $rewardPoints += (int)Mage::getStoreConfig('rewardpoints/default/money_points', Mage::app()->getStore()->getId()) * $price;
                        }

                        $cart_amount += $_item->getRowTotal() + $_item->getTaxAmount() - $_item->getDiscountAmount();
                        if ($rules != array()){
                            foreach ($rules as $rule){
                                if ($rule['type'] == Rewardpoints_Model_Rules::TARGET_SKU){
                                    if ($_product->getSku() == $rule['test_value']){
                                        $rewardPoints += (int)$rule['points'] * $_item->getQty();
                                    }
                                }
                            }
                        }
                    }

                    if ($cart_amount > 0){
                        if ($rules != array()){
                            foreach ($rules as $rule){
                                if ($rule['type'] == Rewardpoints_Model_Rules::TARGET_CART){
                                    switch ($rule['operator']){
                                        case Rewardpoints_Model_Rules::OPERATOR_1: // =
                                            if ($cart_amount == $rule['test_value']){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                        case Rewardpoints_Model_Rules::OPERATOR_2: // <
                                            if ($cart_amount < $rule['test_value']){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                        case Rewardpoints_Model_Rules::OPERATOR_3: // <=
                                            if ($cart_amount <= $rule['test_value']){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                        case Rewardpoints_Model_Rules::OPERATOR_4: // >
                                            if ($cart_amount > $rule['test_value']){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                        case Rewardpoints_Model_Rules::OPERATOR_5: // >=
                                            if ($cart_amount >= $rule['test_value']){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                        case Rewardpoints_Model_Rules::OPERATOR_6: // Between
                                            $test_values = explode(";",$rule['test_value']);
                                            if ($cart_amount >= (int)$test_values[0] && $cart_amount <= (int)$test_values[1]){
                                                $rewardPoints += (int)$rule['points'];
                                            }
                                            break;
                                    }

                                }
                            }
                        }
                    }


                    $reward_points = Mage::getModel('rewardpoints/account');
                    $reward_points->saveCheckedOrder($order->getIncrementId(), $order->getCustomerId(), $order->getStoreId(), $rewardPoints);
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rewardpoints')->__('Full check was proceed on %s orders', (sizeof($orders_array)-1) ));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                
                $this->_redirect('*/*/');
                return;
            }
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rewardpoints')->__('Unable to proccess the full order checking'));
            $this->_redirect('*/*/');
        }

	public function saveAction() {
            if ($data = $this->getRequest()->getPost()) {

                if (isset($data['date_start'])){
                    $date = Mage::app()->getLocale()->date($data['date_start'], Zend_Date::DATE_SHORT, null, false);
                    $time = $date->getTimestamp();
                    $data['date_start'] = Mage::getModel('core/date')->gmtDate(null, $time);
                }
                if (isset($data['date_end'])){
                    if ($data['date_end'] != ""){
                        $date = Mage::app()->getLocale()->date($data['date_end'], Zend_Date::DATE_SHORT, null, false);
                        $time = $date->getTimestamp();
                        $data['date_end'] = Mage::getModel('core/date')->gmtDate(null, $time);
                    }
                    else {
                        unset($data['date_end']);
                    }
                }

                /*if (!empty($data)) {
                    $model->setData($data);
                }*/

                if (!empty($data)) {
                    $data['store_id'] = implode(',',$data['store_id']);

                    $model = Mage::getModel('rewardpoints/stats');
                    $model->setData($data)
                            ->setId($this->getRequest()->getParam('id'));
                }

                try {
                    $model->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rewardpoints')->__('Points were successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);

                    if ($this->getRequest()->getParam('back')) {
                            $this->_redirect('*/*/edit', array('id' => $model->getId()));
                            return;
                    }
                    $this->_redirect('*/*/');
                    return;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
            }
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rewardpoints')->__('Unable to find points to save'));
            $this->_redirect('*/*/');
	}

	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('rewardpoints/stats');

				$model->setId($this->getRequest()->getParam('id'))
					->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rewardpoints')->__('Points were successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $ruleIds = $this->getRequest()->getParam('stats');
        if(!is_array($ruleIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select points'));
        } else {
            try {
                foreach ($ruleIds as $ruleId) {
                    $rule = Mage::getModel('rewardpoints/points')->load($ruleId);
                    $rule->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('rewardpoints')->__(
                        'Total of %d points were successfully deleted', count($ruleIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'rewardpoints.csv';
        $content    = $this->getLayout()->createBlock('rewardpoints/adminhtml_stats_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'rewardpoints.xml';
        $content    = $this->getLayout()->createBlock('rewardpoints/adminhtml_stats_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }


}