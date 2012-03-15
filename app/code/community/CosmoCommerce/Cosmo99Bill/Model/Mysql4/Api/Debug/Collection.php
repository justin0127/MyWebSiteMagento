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
 * Cosmo99Bill API Debug Resource Collection
 *
 * @category   Mage
 * @package    CosmoCommerce_Cosmo99Bill
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */
 
 
class CosmoCommerce_Cosmo99Bill_Model_Mysql4_Api_Debug_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('cosmo99bill/api_debug');
    }
}