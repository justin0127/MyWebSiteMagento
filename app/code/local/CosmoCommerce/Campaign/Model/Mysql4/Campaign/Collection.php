<?php

class CosmoCommerce_Campaign_Model_Mysql4_Campaign_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('campaign/campaign');
    }
}