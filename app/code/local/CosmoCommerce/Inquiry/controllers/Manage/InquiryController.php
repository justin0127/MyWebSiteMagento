<?php
/**
 * @copyright  Copyright (c) 2010 Capacity Web Solutions Pvt. Ltd  (http://www.capacitywebsolutions.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CosmoCommerce_Inquiry_Manage_InquiryController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
		$action = $custId = "";
		$action = $this->getRequest()->getParam('ac');
		$delid = $this->getRequest()->getParam('delid');
		if($action == "del" && !empty($delid))
		{
			$collection = Mage::getModel("inquiry/inquiry")->load($delid);
			
			if($collection->delete())
			{
				Mage::getSingleton('core/session')->addSuccess("问卷删除");
			}
			else
			{
				Mage::getSingleton('core/session')->addError("删除失败");
			}
		}
		
    	$this->_title($this->__('问卷调查'));

        $this->loadLayout();
        $this->_setActiveMenu('inquiry');
       	$this->_addContent($this->getLayout()->createBlock('core/template'));
        $this->renderLayout();
    }
	public function viewAction()
    {
		$delid = $this->getRequest()->getParam('delid');
		$this->_title($this->__('问卷调查'));

        $this->loadLayout();
        $this->_setActiveMenu('inquiry');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('问卷调查'), Mage::helper('adminhtml')->__('问卷调查'));
		$this->_addContent($this->getLayout()->createBlock('core/template'));
        $this->renderLayout();
		
	}
}
?>
