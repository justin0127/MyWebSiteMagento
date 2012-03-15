<?php

class MEC_Chinapay_Model_Source_Servicetype
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0001', 'label' => Mage::helper('chinapay')->__('支付交易')),
        );
    }
}



