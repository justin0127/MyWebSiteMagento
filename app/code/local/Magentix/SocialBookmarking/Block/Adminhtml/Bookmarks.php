<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Block_Adminhtml_Bookmarks extends Mage_Adminhtml_Block_Widget_Grid_Container {

	public function __construct() {
		$this->_controller = 'adminhtml_bookmarks';
		$this->_blockGroup = 'socialbookmarking';
		$this->_headerText = Mage::helper('socialbookmarking')->__('Bookmark Manager');
		$this->_addButtonLabel = Mage::helper('socialbookmarking')->__('Add Bookmark');
		parent::__construct();
	}
	
}