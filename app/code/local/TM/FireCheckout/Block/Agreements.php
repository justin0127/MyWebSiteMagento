<?php

class TM_FireCheckout_Block_Agreements extends Mage_Core_Block_Template
{
    public function getAgreements()
    {
        if (!$this->hasAgreements()) {
            if (!Mage::getStoreConfigFlag('firecheckout/agreements/enabled')) {
                $agreements = array();
            } else {
                $agreements = Mage::getModel('checkout/agreement')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1);
            }
            $this->setAgreements($agreements);
        }
        return $this->getData('agreements');
    }
}
