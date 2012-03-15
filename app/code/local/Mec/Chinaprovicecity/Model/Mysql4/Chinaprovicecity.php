<?php

class Mec_Chinaprovicecity_Model_Mysql4_Chinaprovicecity extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the chinaprovicecity_id refers to the key field in your database table.
        $this->_init('chinaprovicecity/chinaprovicecity', 'chinaprovicecity_id');
    }
}