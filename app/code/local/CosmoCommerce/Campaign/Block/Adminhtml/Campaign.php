<?php
class CosmoCommerce_Campaign_Block_Adminhtml_Campaign extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_campaign';
    $this->_blockGroup = 'campaign';
    $this->_headerText = Mage::helper('campaign')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('campaign')->__('Add Item');
    parent::__construct();
  }
}