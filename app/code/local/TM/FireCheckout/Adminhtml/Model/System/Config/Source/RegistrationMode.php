<?php

class TM_FireCheckout_Adminhtml_Model_System_Config_Source_RegistrationMode
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'optional', 'label' => Mage::helper('firecheckout')->__('Guest checkout is allowed')),
            array('value' => 'required', 'label' => Mage::helper('firecheckout')->__('Registration is required')),
            array('value' => 'hidden', 'label' => Mage::helper('firecheckout')->__('User is registered during checkout without prompting a password'))
        );
    }
}
