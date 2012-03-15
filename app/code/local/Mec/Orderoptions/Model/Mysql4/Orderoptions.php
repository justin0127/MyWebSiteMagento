<?php

class Mec_Orderoptions_Model_Mysql4_Orderoptions extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the orderoptions_id refers to the key field in your database table.
        $this->_init('orderoptions/orderoptions', 'orderoptions_id');
    }
}