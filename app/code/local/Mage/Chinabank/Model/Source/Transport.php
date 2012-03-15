<?php

class Mage_Chinabank_Model_Source_Transport
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'https', 'label' => Mage::helper('chinabank')->__('https')),
            array('value' => 'http', 'label' => Mage::helper('chinabank')->__('http')),
        );
    }
}