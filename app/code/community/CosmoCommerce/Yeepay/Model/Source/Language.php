<?php
/**
 * CosmoCommerce
 *
 * NOTICE OF LICENSE
 * CosmoCommerce Commercial License 
 * support@cosmocommerce.com
 *
 * @category   CosmoCommerce
 * @package    CosmoCommerce_Yeepay
 * @copyright  Copyright (c) 2009 CosmoCommerce,LLC. (http://www.cosmocommerce.com)
 * @license	     CosmoCommerce Commercial License(http://www.cosmocommerce.com/cosmocommerce_commercial_license.txt)
 */

/**
 * Yeepay Allowed languages Resource
 *
 * @category   Mage
 * @package    CosmoCommerce_Yeepay
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */


class CosmoCommerce_Yeepay_Model_Source_Language
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'EN', 'label' => Mage::helper('yeepay')->__('English')),
            array('value' => 'FR', 'label' => Mage::helper('yeepay')->__('French')),
            array('value' => 'DE', 'label' => Mage::helper('yeepay')->__('German')),
            array('value' => 'IT', 'label' => Mage::helper('yeepay')->__('Italian')),
            array('value' => 'ES', 'label' => Mage::helper('yeepay')->__('Spain')),
            array('value' => 'NL', 'label' => Mage::helper('yeepay')->__('Dutch')),
        );
    }
}



