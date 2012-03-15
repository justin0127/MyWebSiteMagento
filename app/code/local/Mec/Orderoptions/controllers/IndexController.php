<?php
class Mec_Orderoptions_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/orderoptions?id=15 
    	 *  or
    	 * http://site.com/orderoptions/id/15 	
    	 */
    	/* 
		$orderoptions_id = $this->getRequest()->getParam('id');

  		if($orderoptions_id != null && $orderoptions_id != '')	{
			$orderoptions = Mage::getModel('orderoptions/orderoptions')->load($orderoptions_id)->getData();
		} else {
			$orderoptions = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($orderoptions == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$orderoptionsTable = $resource->getTableName('orderoptions');
			
			$select = $read->select()
			   ->from($orderoptionsTable,array('orderoptions_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$orderoptions = $read->fetchRow($select);
		}
		Mage::register('orderoptions', $orderoptions);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}