<?php

class Mage_Chinabank_Model_Source_Servicetype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'trade_create_by_buyer', 'label' => Mage::helper('chinabank')->__('Products')),
            array('value' => 'create_digital_goods_trade_p', 'label' => Mage::helper('chinabank')->__('Virtual Products')),
        );
    }
}



