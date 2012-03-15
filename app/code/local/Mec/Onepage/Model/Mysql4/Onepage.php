<?php

class Mec_Onepage_Model_Mysql4_Onepage extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the onepage_id refers to the key field in your database table.
        $this->_init('onepage/onepage', 'onepage_id');
    }
}