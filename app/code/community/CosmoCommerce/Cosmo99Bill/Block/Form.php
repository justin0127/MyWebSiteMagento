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
 * Cosmo99Bill Form Block
 *
 * @category   Mage
 * @package    CosmoCommerce_Cosmo99Bill
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */
class CosmoCommerce_Cosmo99Bill_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('cosmo99bill/form.phtml');
        parent::_construct();
    }
    
    public function getPayment()
    {
        return Mage::getSingleton('cosmo99bill/payment');
    }
    
    public function showMessage(){
         return $this->getPayment()->getMessage();
    }

}
