<?php
class MEC_Chinapay_Block_Redirect extends Mage_Core_Block_Abstract
{

	protected function _toHtml()
	{
	$standard = Mage::getModel('chinapay/payment');
        $form = new Varien_Data_Form();
        $form->setAction($standard->getChinapayUrl())
            ->setId('chinapay_payment_checkout')
            ->setName('chinapay_payment_checkout')
            ->setMethod('GET')
            ->setUseContainer(true);
        foreach ($standard->setOrder($this->getOrder())->getStandardCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        $formHTML = $form->toHtml();

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Chinapay in a few seconds.');
        $html.= $formHTML;
        $html.= '<script type="text/javascript">document.getElementById("chinapay_payment_checkout").submit();</script>';
        $html.= '</body></html>';


        return $html;
    }
}
