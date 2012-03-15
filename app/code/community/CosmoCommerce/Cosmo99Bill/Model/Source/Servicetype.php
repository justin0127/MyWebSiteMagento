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
 
class CosmoCommerce_Cosmo99Bill_Model_Source_Servicetype
{
    public function toOptionArray()
    {
        return array(
            array('value' => '3', 'label' => Mage::helper('cosmo99bill')->__('接口类型3')),
            array('value' => '5', 'label' => Mage::helper('cosmo99bill')->__('接口类型5')),
        );
    }
}



