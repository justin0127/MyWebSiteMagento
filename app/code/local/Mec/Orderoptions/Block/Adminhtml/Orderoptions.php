<?php
class Mec_Orderoptions_Block_Adminhtml_Orderoptions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_orderoptions';
    $this->_blockGroup = 'orderoptions';
    $this->_headerText = Mage::helper('orderoptions')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('orderoptions')->__('Add Item');
    parent::__construct();
  }
}