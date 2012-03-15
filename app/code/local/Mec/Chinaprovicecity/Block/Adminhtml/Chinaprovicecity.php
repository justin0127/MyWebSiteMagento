<?php
class Mec_Chinaprovicecity_Block_Adminhtml_Chinaprovicecity extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_chinaprovicecity';
    $this->_blockGroup = 'chinaprovicecity';
    $this->_headerText = Mage::helper('chinaprovicecity')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('chinaprovicecity')->__('Add Item');
    parent::__construct();
  }
}