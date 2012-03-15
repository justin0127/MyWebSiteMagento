<?php
class Mec_Onepage_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/onepage?id=15 
    	 *  or
    	 * http://site.com/onepage/id/15 	
    	 */
    	/* 
		$onepage_id = $this->getRequest()->getParam('id');

  		if($onepage_id != null && $onepage_id != '')	{
			$onepage = Mage::getModel('onepage/onepage')->load($onepage_id)->getData();
		} else {
			$onepage = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($onepage == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$onepageTable = $resource->getTableName('onepage');
			
			$select = $read->select()
			   ->from($onepageTable,array('onepage_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$onepage = $read->fetchRow($select);
		}
		Mage::register('onepage', $onepage);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}