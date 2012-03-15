<?php
/**
 * CosmoCommerce
 *
 * NOTICE OF LICENSE
 * CosmoCommerce Commercial License 
 * support@cosmocommerce.com
 *
 * @category   CosmoCommerce
 * @package    CosmoCommerce_Cosmo99Bill
 * @copyright  Copyright (c) 2009 CosmoCommerce,LLC. (http://www.cosmocommerce.com)
 * @license	     CosmoCommerce Commercial License(http://www.cosmocommerce.com/cosmocommerce_commercial_license.txt)
 */

/**
 * Cosmo99Bill Allowed languages Resource
 *
 * @category   Mage
 * @package    CosmoCommerce_Cosmo99Bill
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */


class CosmoCommerce_Cosmo99Bill_Model_Source_Language
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'EN', 'label' => Mage::helper('cosmo99bill')->__('English')),
            array('value' => 'FR', 'label' => Mage::helper('cosmo99bill')->__('French')),
            array('value' => 'DE', 'label' => Mage::helper('cosmo99bill')->__('German')),
            array('value' => 'IT', 'label' => Mage::helper('cosmo99bill')->__('Italian')),
            array('value' => 'ES', 'label' => Mage::helper('cosmo99bill')->__('Spain')),
            array('value' => 'NL', 'label' => Mage::helper('cosmo99bill')->__('Dutch')),
        );
    }
}



