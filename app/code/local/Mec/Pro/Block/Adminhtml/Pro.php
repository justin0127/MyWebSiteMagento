<?php
class Mec_Pro_Block_Adminhtml_Pro extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_pro';
    $this->_blockGroup = 'pro';
    $this->_headerText = Mage::helper('pro')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('pro')->__('Add Item');
    parent::__construct();
  }
}