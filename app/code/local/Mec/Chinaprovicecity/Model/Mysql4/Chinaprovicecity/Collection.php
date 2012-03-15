<?php

class Mec_Chinaprovicecity_Model_Mysql4_Chinaprovicecity_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('chinaprovicecity/chinaprovicecity');
    }
}