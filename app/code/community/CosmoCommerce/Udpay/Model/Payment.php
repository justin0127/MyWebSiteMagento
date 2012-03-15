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
 * Udpay Payment Model
 *
 * @category   Mage
 * @package    CosmoCommerce_Udpay
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */
 
class CosmoCommerce_Udpay_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'udpay_payment';
    protected $_formBlockType = 'udpay/form';

    // Udpay return codes of payment
    const RETURN_CODE_ACCEPTED      = 'Success';
    const RETURN_CODE_TEST_ACCEPTED = 'Success';
    const RETURN_CODE_ERROR         = 'Fail';

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
    public function getUdpayUrl()
    {
        $url = $this->getConfigData('transport').'://'.$this->getConfigData('gateway');
        return $url;
    }

    /**
     *  Return back URL
     *
     *  @return	  string URL
     */
	protected function getReturnURL()
	{
		return Mage::getUrl('checkout/onepage/success', array('_secure' => true));
	}

         public function getMessage() {
	   return $this->getConfigData('redirect_text');
         }

	/**
	 *  Return URL for Udpay success response
	 *
	 *  @return	  string URL
	 */
	protected function getSuccessURL()
	{
		return Mage::getUrl('checkout/onepage/success', array('_secure' => true));
	}

    /**
     *  Return URL for Udpay failure response
     *
     *  @return	  string URL
     */
    protected function getErrorURL()
    {
        return Mage::getUrl('udpay/payment/error', array('_secure' => true));
    }

	/**
	 *  Return URL for Udpay notify response
	 *
	 *  @return	  string URL
	 */
	protected function getNotifyURL()
	{
		return Mage::getUrl('checkout/onepage/success', array('_secure' => true));
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
        $block = $this->getLayout()->createBlock('udpay/form_payment', $name);
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
        return Mage::getUrl('udpay/payment/redirect');
    }

    /**
     *  Return Standard Checkout Form Fields for request to Udpay
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
		
		$_interfaceType=$this->getConfigData('service_type');
		$_merchantId=$this->getConfigData('partner_id');
		$_transDate="2009-05-13";
		$_transFlow=$order->getRealOrderId();
		$_curCode="156";
		$_orderId=$order->getRealOrderId();
		$_orderInfo=$order->getRealOrderId();
		$_comment=$order->getRealOrderId();
		$_amount=sprintf('%.2f', $order->getBaseGrandTotal())*100;
		$_txCode='TP001';
		$_merURL=$this->getSuccessURL();
		
		$_privatekey=$this->getConfigData('private_key');
		$_privateModulus=$this->getConfigData('private_modulus');
		$_privateExponent=$this->getConfigData('private_exponent');
		$_publicKey=$this->getConfigData('public_key');
		$_publicModulus=$this->getConfigData('public_modulus');
		$_publicExponent=$this->getConfigData('public_exponent');

		$parameter = array('interfaceType'=>$_interfaceType,
                           'merchantId'=> $_merchantId,
                           'transDate' => $_transDate,
                           'transFlow' => $_transFlow, 
                           'curCode'   => $_curCode,
                           'orderId'   => $_orderId,
                           'orderInfo' => $_orderInfo,
                           'comment'   => $_comment,                           'amount'    => $_amount,
                           'txCode'    => $_txCode, 
                           'merURL'    => $_merURL
                        );
						
		$tempmsg = 'txCode=' . $_txCode . '&' . 'merchantId=' .$_merchantId . '&' . 'transDate=' . $_transDate . '&' . 'transFlow=' . $_transFlow . '&' . 'orderId=' . $_orderId . '&' . 'curCode=' . $_curCode . '&' . 'amount=' . $_amount . '&' . 'orderInfo=' . $_orderInfo . '&' . 'comment=' .$_comment . '&' . 'merURL=' . $_merURL . '&' . 'interfaceType=' . $_interfaceType;
	
		$msg = $tempmsg;
		
		$testRsaDecrypt = $this->generateSigature($msg, $_privateExponent, $_privateModulus);
		
	$parameter['sign'] = $testRsaDecrypt;
		
        return $parameter;
    }

	
	public function generateSigature($message, $exponent, $modulus) {          
	 $md5Message = md5($message);   
	 $fillStr = "01ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff003020300c06082a864886f70d020505000410";                 
	 $md5Message = $fillStr.$md5Message;
	 $intMessage = $this->bin2int($this->hex2bin($md5Message));
	 $intE = $this->bin2int($this->hex2bin($exponent));
	 $intM = $this->bin2int($this->hex2bin($modulus));
	 $intResult = $this->powmod($intMessage, $intE, $intM);
	 $hexResult = bin2hex($this->int2bin($intResult));
	 return $hexResult;
	}                                                                                                                                                      
	public function hex2bin($hexdata) {    
	$bindata="";
  	 for ($i=0;$i<strlen($hexdata);$i+=2) { 
 	    $bindata=chr(hexdec(substr($hexdata,$i,2))).$bindata; 
	   } 
	   return $bindata; 
	}

	function bin2int($str)
	{
    $result = '0';
    $n = strlen($str);
    do {
        $result = bcadd(bcmul($result, '256'), ord($str{--$n}));
    } while ($n > 0);
    return $result;
	}

	public function int2bin($num)
	{
    $result = '';
    do {
        $result= chr(bcmod($num, '256')).$result;
        $num = bcdiv($num, '256');
    } while (bccomp($num, '0'));
    return $result;
	}

	public function powmod($num, $pow, $mod){
 	 $result = '1';
 	 do {
      if (!bccomp(bcmod($pow, '2'), '1')) {
          $result = bcmod(bcmul($result, $num), $mod);
      }
      $num = bcmod(bcpow($num, '2'), $mod);
      $pow = bcdiv($pow, '2');
 	 } while (bccomp($pow, '0'));
	  return $result;
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
	 * Return authorized languages by Udpay
	 *
	 * @param	none
	 * @return	array
	 */
	protected function _getAuthorizedLanguages()
	{
		$languages = array();
		
        foreach (Mage::getConfig()->getNode('global/payment/udpay_payment/languages')->asArray() as $data) 
		{
			$languages[$data['code']] = $data['name'];
		}
		
		return $languages;
	}
	
	/**
	 * Return language code to send to Udpay
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
     *  Return response for Udpay success payment
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
     *  Return response for Udpay failure payment
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
