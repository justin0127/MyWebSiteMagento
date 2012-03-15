<?php

class CosmoCommerce_Campaign_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('campaign')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('campaign')->__('Disabled')
        );
    }
}