<?php

class Mec_Help_Block_Adminhtml_Help_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('help_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('help')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('help')->__('Item Information'),
          'title'     => Mage::helper('help')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('help/adminhtml_help_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
