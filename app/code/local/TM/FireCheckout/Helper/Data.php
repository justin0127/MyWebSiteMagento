<?php

class TM_FireCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_agreements = null;

    /**
     * Get fire checkout availability
     *
     * @return bool
     */
    public function canFireCheckout()
    {
        return (bool)Mage::getStoreConfig('firecheckout/general/enabled');
    }

    public function getRequiredAgreementIds()
    {
        if (is_null($this->_agreements)) {
            if (!Mage::getStoreConfigFlag('firecheckout/agreements/enabled')) {
                $this->_agreements = array();
            } else {
                $this->_agreements = Mage::getModel('checkout/agreement')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1)
                    ->getAllIds();
            }
        }
        return $this->_agreements;
    }

    public function isAllowedGuestCheckout()
    {
        return 'optional' == Mage::getStoreConfig('firecheckout/general/registration_mode');
    }

    public function getIsSubscribed()
    {
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            return false;
        }
        return Mage::getModel('newsletter/subscriber')->getCollection()
            ->useOnlySubscribed()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addFieldToFilter('subscriber_email', $customerSession->getCustomer()->getEmail())
            ->getAllIds();
    }

    public function canShowNewsletter()
    {
        if (!Mage::getStoreConfig('firecheckout/general/newsletter_checkbox')) {
            return false;
        }

        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn() && !Mage::getStoreConfig('newsletter/subscription/allow_guest_subscribe')) {
            return false;
        }

        return !Mage::helper('firecheckout')->getIsSubscribed();
    }
}