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
 
class CosmoCommerce_Udpay_Model_Source_Transport
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'https', 'label' => Mage::helper('udpay')->__('https')),
            array('value' => 'http', 'label' => Mage::helper('udpay')->__('http')),
        );
    }
}