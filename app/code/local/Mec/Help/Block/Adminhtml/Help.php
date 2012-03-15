<?php
/*class Mec_Help_Block_Adminhtml_Help extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_help';
    $this->_blockGroup = 'help';
    $this->_headerText = Mage::helper('help')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('help')->__('Add Item');
    parent::__construct();
  }
}
*/
class Mec_Help_Block_Adminhtml_Help extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('help/index.phtml');

    }
}
