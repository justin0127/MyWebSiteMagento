<?php
class Mec_Help_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/help?id=15 
    	 *  or
    	 * http://site.com/help/id/15 	
    	 */
    	/* 
		$help_id = $this->getRequest()->getParam('id');

  		if($help_id != null && $help_id != '')	{
			$help = Mage::getModel('help/help')->load($help_id)->getData();
		} else {
			$help = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($help == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$helpTable = $resource->getTableName('help');
			
			$select = $read->select()
			   ->from($helpTable,array('help_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$help = $read->fetchRow($select);
		}
		Mage::register('help', $help);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}