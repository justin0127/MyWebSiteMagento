<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Chinabank
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Alipay Payment Model
 *
 * @category   Mage
 * @package    Chinabank
 * @name       Chinabank_Model_Payment
 * @author	   Magento Core Team <core@magentocommerce.com>, Quadra Informatique - Nicolas Fischer <nicolas.fischer@quadra-informatique.fr>
 */

class Mage_Chinabank_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'chinabank_payment';
    protected $_formBlockType = 'chinabank/form';

    // Alipay return codes of payment
    const RETURN_CODE_ACCEPTED      = 'paiement';
    const RETURN_CODE_TEST_ACCEPTED = 'payetest';
    const RETURN_CODE_ERROR         = 'Annulation';

    // Payment configuration
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    // Order instance
    protected $_order = null;

    /**
     *  Returns Target URL
     *
     *  @return	  string Target URL
     */
    public function getAlipayUrl()
    {
        $url = $this->getConfigData('transport').'://'.$this->getConfigData('gateway');
        return $url;
    }
    
    public function getMessage() {
    return $this->getConfigData('redirect_text');
    }

    /**
     *  Return back URL
     *
     *  @return	  string URL
     */
	protected function getReturnURL()
	{
		return Mage::getUrl('chinabank/payment/normal', array('_secure' => true));
	}

	/**
	 *  Return URL for Alipay success response
	 *
	 *  @return	  string URL
	 */
	protected function getSuccessURL()
	{
		return Mage::getUrl('chinabank/payment/success', array('_secure' => true));
	}

    /**
     *  Return URL for Alipay failure response
     *
     *  @return	  string URL
     */
    protected function getErrorURL()
    {
        return Mage::getUrl('chinabank/payment/error', array('_secure' => true));
    }

	/**
	 *  Return URL for Alipay notify response
	 *
	 *  @return	  string URL
	 */
	protected function getNotifyURL()
	{
		return Mage::getUrl('chinabank/payment/notify');//, array('_secure' => true)
	}

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('chinabank/form_payment', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());

        return $block;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('chinabank/payment/redirect');
    }

    /**
     *  Return Standard Checkout Form Fields for request to Alipay
     *
     *  @return	  array Array of hidden form fields
     */
    public function getStandardCheckoutFormFields()
    {
        $session = Mage::getSingleton('checkout/session');
        
        $order = $this->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        }

		$parameter = array(    
                           //'service'           => $this->getConfigData('service_type'),
                           'v_mid'           => $this->getConfigData('partner_id'),
                           'v_url'        => $this->getNotifyURL(),  //$this->getReturnURL(),
                           //'notify_url'        => $this->getNotifyURL(),
                           'v_oid'              => $order->getRealOrderId(),
                           'v_amount'         => sprintf('%.2f', $order->getBaseGrandTotal()),
                           'key'                  =>$this->getConfigData('security_code'),
                           'v_moneytype' => "CNY",                           
                           'v_md5info'    => strtoupper(md5(sprintf('%.2f', $order->getBaseGrandTotal())."CNY".$order->getRealOrderId().$this->getConfigData('partner_id').$this->getNotifyURL().$this->getConfigData('security_code'))),
                           '_input_charset'    => 'utf-8',                           
                           //'logistics_fee'     => sprintf('%.2f',$order->getShipping_amount()), //because magento has shipping system, it has included 
                           //'price'             => sprintf('%.2f', $order->getBaseGrandTotal()),// . $order->getBaseCurrencyCode(),
                           //'show_url'          => Mage::getUrl(),
                           //'seller_email'      => $this->getConfigData('seller_email')
                        );
		$parameter = $this->para_filter($parameter);
		$security_code = $this->getConfigData('security_code');
		$sign_type = 'MD5';
		
		$sort_array = array();
		$arg = "";
		$sort_array = $this->arg_sort($parameter); //$parameter
		
		while (list ($key, $val) = each ($sort_array)) {
			$arg.=$key."=".$this->charset_encode($val,$parameter['_input_charset'])."&";
		}
		
		$prestr = substr($arg,0,count($arg)-2);
		
		$mysign = $this->sign($prestr.$security_code);
		
		$fields = array();
		$sort_array = array();
		$arg = "";
		$sort_array = $this->arg_sort($parameter); //$parameter
		while (list ($key, $val) = each ($sort_array)) {
			$fields[$key] = urlencode($this->charset_encode($val,'utf-8'));
		}
		$fields['sign'] = $mysign;
		$fields['sign_type'] = $sign_type;
//		$fields = array('TPE'            => $this->getConfigData('tpe_no'),
//                        'date'           => date('d/m/Y:H:i:s'),
//                        'montant'        => sprintf('%.2f', $order->getBaseGrandTotal()) . $order->getBaseCurrencyCode(),
//                        'reference'      => $order->getRealOrderId(),
//                        'lgue'           => $this->_getLanguageCode(),
//                        'societe'        => $this->getConfigData('site_code'),
//                        'url_retour'     => $this->getReturnURL(),
//                        'url_retour_ok'  => $this->getSuccessURL(),
//                        'url_retour_err' => $this->getErrorURL()
//                        );

        return $fields;
    }
    
	public function sign($prestr) {
	   
		$mysign = md5($prestr);
		return $mysign;
	}
    
	public function para_filter($parameter) {
		$para = array();
		while (list ($key, $val) = each ($parameter)) {
			if($key == "sign" || $key == "sign_type" || $val == "")continue;
			else	$para[$key] = $parameter[$key];

		}
		return $para;
	}
	
	public function arg_sort($array) {
		ksort($array);
		reset($array);
		return $array;
	}

	public function charset_encode($input,$_output_charset ,$_input_charset ="GBK" ) {
		$output = "";
		if($_input_charset == $_output_charset || $input ==null) {
			$output = $input;
		} elseif (function_exists("mb_convert_encoding")){
			$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
		} elseif(function_exists("iconv")) {
			$output = iconv($_input_charset,$_output_charset,$input);
		} else die("sorry, you have no libs support for charset change.");
		return $output;
	}
   
	/**
	 * Return authorized languages by Alipay
	 *
	 * @param	none
	 * @return	array
	 */
	protected function _getAuthorizedLanguages()
	{
		$languages = array();
		
        foreach (Mage::getConfig()->getNode('global/payment/chinabank_payment/languages')->asArray() as $data) 
		{
			$languages[$data['code']] = $data['name'];
		}
		
		return $languages;
	}
	
	/**
	 * Return language code to send to Alipay
	 *
	 * @param	none
	 * @return	String
	 */
	protected function _getLanguageCode()
	{
		// Store language
		$language = strtoupper(substr(Mage::getStoreConfig('general/locale/code'), 0, 2));

		// Authorized Languages
		$authorized_languages = $this->_getAuthorizedLanguages();

		if (count($authorized_languages) === 1) 
		{
			$codes = array_keys($authorized_languages);
			return $codes[0];
		}
		
		if (array_key_exists($language, $authorized_languages)) 
		{
			return $language;
		}
		
		// By default we use language selected in store admin
		return $this->getConfigData('language');
	}



    /**
     *  Output failure response and stop the script
     *
     *  @param    none
     *  @return	  void
     */
    public function generateErrorResponse()
    {
        die($this->getErrorResponse());
    }

    /**
     *  Return response for Alipay success payment
     *
     *  @param    none
     *  @return	  string Success response string
     */
    public function getSuccessResponse()
    {
        $response = array(
            'Pragma: no-cache',
            'Content-type : text/plain',
            'Version: 1',
            'OK'
        );
        return implode("\n", $response) . "\n";
    }

    /**
     *  Return response for Alipay failure payment
     *
     *  @param    none
     *  @return	  string Failure response string
     */
    public function getErrorResponse()
    {
        $response = array(
            'Pragma: no-cache',
            'Content-type : text/plain',
            'Version: 1',
            'Document falsifie'
        );
        return implode("\n", $response) . "\n";
    }

}
