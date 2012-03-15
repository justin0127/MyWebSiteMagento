<?php
class Mec_Search_Block_Adminhtml_Search extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_search';
    $this->_blockGroup = 'search';
    $this->_headerText = Mage::helper('search')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('search')->__('Add Item');
    parent::__construct();
  }
}