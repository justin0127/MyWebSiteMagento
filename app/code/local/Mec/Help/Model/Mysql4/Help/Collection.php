<?php

class Mec_Help_Model_Mysql4_Help_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('help/help');
    }
}