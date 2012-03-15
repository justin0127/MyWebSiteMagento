<?php

class Mec_Pro_Model_Mysql4_Pro_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pro/pro');
    }
}