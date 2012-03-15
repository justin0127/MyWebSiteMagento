<?php

class Mec_Chinaprovicecity_Block_Adminhtml_Chinaprovicecity_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('chinaprovicecity_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('chinaprovicecity')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('chinaprovicecity')->__('Item Information'),
          'title'     => Mage::helper('chinaprovicecity')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('chinaprovicecity/adminhtml_chinaprovicecity_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}