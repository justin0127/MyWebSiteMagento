<?php

class Mec_Help_Model_Mysql4_Help extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the help_id refers to the key field in your database table.
        $this->_init('help/help', 'help_id');
    }
}