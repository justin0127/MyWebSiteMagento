<?php

class Mec_Pro_Model_Mysql4_Pro extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the pro_id refers to the key field in your database table.
        $this->_init('pro/pro', 'pro_id');
    }
}