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
 * Yeepay Form Block
 *
 * @category   Mage
 * @package    CosmoCommerce_Yeepay
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */
class CosmoCommerce_Yeepay_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('yeepay/form.phtml');
        parent::_construct();
    }
    
    public function getPayment()
    {
        return Mage::getSingleton('yeepay/payment');
    }
   
    public function showMessage()
    {
         return $this->getPayment()->getMessage();
    }

}
