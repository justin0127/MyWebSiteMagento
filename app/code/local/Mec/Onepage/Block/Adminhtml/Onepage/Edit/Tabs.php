<?php

class Mec_Onepage_Block_Adminhtml_Onepage_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('onepage_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('onepage')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('onepage')->__('Item Information'),
          'title'     => Mage::helper('onepage')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('onepage/adminhtml_onepage_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}