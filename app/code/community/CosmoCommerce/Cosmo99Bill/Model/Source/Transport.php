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
 
class CosmoCommerce_Cosmo99Bill_Model_Source_Transport
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'https', 'label' => Mage::helper('cosmo99bill')->__('https')),
            array('value' => 'http', 'label' => Mage::helper('cosmo99bill')->__('http')),
        );
    }
}