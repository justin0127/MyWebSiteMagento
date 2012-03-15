<?php
/**
 * CosmoCommerce
 *
 * NOTICE OF LICENSE
 * CosmoCommerce Commercial License 
 * support@cosmocommerce.com
 *
 * @category   CosmoCommerce
 * @package    CosmoCommerce_Udpay
 * @copyright  Copyright (c) 2009 CosmoCommerce,LLC. (http://www.cosmocommerce.com)
 * @license	     CosmoCommerce Commercial License(http://www.cosmocommerce.com/cosmocommerce_commercial_license.txt)
 */

/**
 * Udpay Allowed languages Resource
 *
 * @category   Mage
 * @package    CosmoCommerce_Udpay
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */


class CosmoCommerce_Udpay_Model_Source_Language
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'EN', 'label' => Mage::helper('udpay')->__('English')),
            array('value' => 'FR', 'label' => Mage::helper('udpay')->__('French')),
            array('value' => 'DE', 'label' => Mage::helper('udpay')->__('German')),
            array('value' => 'IT', 'label' => Mage::helper('udpay')->__('Italian')),
            array('value' => 'ES', 'label' => Mage::helper('udpay')->__('Spain')),
            array('value' => 'NL', 'label' => Mage::helper('udpay')->__('Dutch')),
        );
    }
}



