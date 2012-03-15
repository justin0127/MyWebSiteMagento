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
 * Redirect to Cosmo99Bill
 *
 * @category   Mage
 * @package    CosmoCommerce_Cosmo99Bill
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */
class CosmoCommerce_Cosmo99Bill_Block_Redirect extends Mage_Core_Block_Abstract
{

	protected function _toHtml()
	{
		$standard = Mage::getModel('cosmo99bill/payment');
        $form = new Varien_Data_Form();
        $form->setAction($standard->getCosmo99BillUrl())
            ->setId('cosmo99bill_payment_checkout')
            ->setName('cosmo99bill_payment_checkout')
            ->setMethod('GET')
            ->setUseContainer(true);
        foreach ($standard->setOrder($this->getOrder())->getStandardCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        $formHTML = $form->toHtml();

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Cosmo99Bill in a few seconds.');
        $html.= $formHTML;
        $html.= '<script type="text/javascript">document.getElementById("cosmo99bill_payment_checkout").submit();</script>';
        $html.= '</body></html>';


        return $html;
    }
}