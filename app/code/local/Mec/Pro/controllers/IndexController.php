<?php
class Mec_Pro_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/pro?id=15 
    	 *  or
    	 * http://site.com/pro/id/15 	
    	 */
    	/* 
		$pro_id = $this->getRequest()->getParam('id');

  		if($pro_id != null && $pro_id != '')	{
			$pro = Mage::getModel('pro/pro')->load($pro_id)->getData();
		} else {
			$pro = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($pro == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$proTable = $resource->getTableName('pro');
			
			$select = $read->select()
			   ->from($proTable,array('pro_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$pro = $read->fetchRow($select);
		}
		Mage::register('pro', $pro);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}