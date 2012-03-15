<?php

class Mec_Search_Model_Mysql4_Search extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the search_id refers to the key field in your database table.
        $this->_init('search/search', 'search_id');
    }
}