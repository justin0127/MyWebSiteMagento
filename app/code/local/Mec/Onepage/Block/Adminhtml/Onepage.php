<?php
class Mec_Onepage_Block_Adminhtml_Onepage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_onepage';
    $this->_blockGroup = 'onepage';
    $this->_headerText = Mage::helper('onepage')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('onepage')->__('Add Item');
    parent::__construct();
  }
}