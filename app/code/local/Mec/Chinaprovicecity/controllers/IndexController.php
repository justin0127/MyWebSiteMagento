<?php
class Mec_Chinaprovicecity_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/chinaprovicecity?id=15 
    	 *  or
    	 * http://site.com/chinaprovicecity/id/15 	
    	 */
    	/* 
		$chinaprovicecity_id = $this->getRequest()->getParam('id');

  		if($chinaprovicecity_id != null && $chinaprovicecity_id != '')	{
			$chinaprovicecity = Mage::getModel('chinaprovicecity/chinaprovicecity')->load($chinaprovicecity_id)->getData();
		} else {
			$chinaprovicecity = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($chinaprovicecity == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$chinaprovicecityTable = $resource->getTableName('chinaprovicecity');
			
			$select = $read->select()
			   ->from($chinaprovicecityTable,array('chinaprovicecity_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$chinaprovicecity = $read->fetchRow($select);
		}
		Mage::register('chinaprovicecity', $chinaprovicecity);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}