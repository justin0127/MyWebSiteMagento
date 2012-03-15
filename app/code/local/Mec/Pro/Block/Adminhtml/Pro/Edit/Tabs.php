<?php

class Mec_Pro_Block_Adminhtml_Pro_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('pro_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('pro')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('pro')->__('Item Information'),
          'title'     => Mage::helper('pro')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('pro/adminhtml_pro_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}