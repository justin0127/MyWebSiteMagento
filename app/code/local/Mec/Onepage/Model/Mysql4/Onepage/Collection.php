<?php

class Mec_Onepage_Model_Mysql4_Onepage_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('onepage/onepage');
    }
}