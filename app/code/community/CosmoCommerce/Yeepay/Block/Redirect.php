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
 * Redirect to Yeepay
 *
 * @category   Mage
 * @package    CosmoCommerce_Yeepay
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */
class CosmoCommerce_Yeepay_Block_Redirect extends Mage_Core_Block_Abstract
{

	protected function _toHtml()
	{
		$standard = Mage::getModel('yeepay/payment');
        $form = new Varien_Data_Form();
        $form->setAction($standard->getYeepayUrl())
            ->setId('yeepay_payment_checkout')
            ->setName('yeepay_payment_checkout')
            ->setMethod('GET')
            ->setUseContainer(true);
        foreach ($standard->setOrder($this->getOrder())->getStandardCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        $formHTML = $form->toHtml();

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Yeepay in a few seconds.');
        $html.= $formHTML;
        $html.= '<script type="text/javascript">document.getElementById("yeepay_payment_checkout").submit();</script>';
        $html.= '</body></html>';


        return $html;
    }
}