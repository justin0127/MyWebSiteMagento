<?php
class MEC_Chinapay_Model_Source_Language
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'CN', 'label' => Mage::helper('chinapay')->__('Chinese')),
            array('value' => 'EN', 'label' => Mage::helper('chinapay')->__('English')),
            array('value' => 'FR', 'label' => Mage::helper('chinapay')->__('French')),
            array('value' => 'DE', 'label' => Mage::helper('chinapay')->__('German')),
            array('value' => 'IT', 'label' => Mage::helper('chinapay')->__('Italian')),
            array('value' => 'ES', 'label' => Mage::helper('chinapay')->__('Spain')),
            array('value' => 'NL', 'label' => Mage::helper('chinapay')->__('Dutch')),
        );
    }
}



