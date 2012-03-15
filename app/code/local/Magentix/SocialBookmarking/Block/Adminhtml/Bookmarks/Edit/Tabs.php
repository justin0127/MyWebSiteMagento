<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Block_Adminhtml_Bookmarks_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

	public function __construct() {
		parent::__construct();
		$this->setId('socialbookmarking_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('socialbookmarking')->__('Bookmark Information'));
	}

	protected function _beforeToHtml() {
		$this->addTab('form_section', array(
			'label'     => Mage::helper('socialbookmarking')->__('Bookmark Information'),
			'title'     => Mage::helper('socialbookmarking')->__('Bookmark Information'),
			'content'   => $this->getLayout()->createBlock('socialbookmarking/adminhtml_bookmarks_edit_tab_form')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}

}