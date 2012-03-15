<?php

class Mec_Search_Block_Adminhtml_Search_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('search_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('search')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('search')->__('Item Information'),
          'title'     => Mage::helper('search')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('search/adminhtml_search_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}