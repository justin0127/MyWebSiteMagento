<?php

class CosmoCommerce_Campaign_Model_Mysql4_Campaign extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the campaign_id refers to the key field in your database table.
        $this->_init('campaign/campaign', 'campaign_id');
    }
}