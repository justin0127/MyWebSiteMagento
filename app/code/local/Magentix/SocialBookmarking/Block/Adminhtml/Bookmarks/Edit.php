<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Block_Adminhtml_Bookmarks_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	public function __construct() {
		parent::__construct();
				 
		$this->_objectId = 'id';
		$this->_blockGroup = 'socialbookmarking';
		$this->_controller = 'adminhtml_bookmarks';
		
		$this->_updateButton('save', 'label', Mage::helper('socialbookmarking')->__('Save'));
		$this->_updateButton('delete', 'label', Mage::helper('socialbookmarking')->__('Delete'));
	}

	public function getHeaderText() {
		if( Mage::registry('socialbookmarking_data') && Mage::registry('socialbookmarking_data')->getId() ) {
			return Mage::helper('socialbookmarking')->__("Edit Bookmark '%s'", $this->htmlEscape(Mage::registry('socialbookmarking_data')->getName()));
		} else {
			return Mage::helper('socialbookmarking')->__('Add Bookmark');
		}
	}
}