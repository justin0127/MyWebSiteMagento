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
 * Udpay API Debug Resource
 *
 * @category   Mage
 * @package    CosmoCommerce_Udpay
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */

class CosmoCommerce_Udpay_Model_Mysql4_Api_Debug extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('udpay/api_debug', 'debug_id');
    }
}