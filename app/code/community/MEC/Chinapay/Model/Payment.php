<?php
	include_once("netpayclient_config.php");
	//加载 netpayclient 组件
	include_once("netpayclient.php");
?>
<?php
class MEC_Chinapay_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
	
    protected $_code  = 'chinapay_payment';
    protected $_formBlockType = 'chinapay/form';

    // Chinapay return codes of payment
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
    public function getChinapayUrl()
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

	/**
	 *  Return URL for Chinapay success response
	 *
	 *  @return	  string URL
	 */
	protected function getSuccessURL()
	{
		return Mage::getUrl('checkout/onepage/success', array('_secure' => true));
	}

    /**
     *  Return URL for Chinapay failure response
     *
     *  @return	  string URL
     */
    protected function getErrorURL()
    {
        return Mage::getUrl('chinapay/payment/error', array('_secure' => true));
    }

	/**
	 *  Return URL for Chinapay notify response
	 *
	 *  @return	  string URL
	 */
	protected function getNotifyURL()
	{
		return Mage::getUrl('chinapay/payment/notify/', array('_secure' => true));
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
        $block = $this->getLayout()->createBlock('chinapay/form_payment', $name);
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
        return Mage::getUrl('chinapay/payment/redirect');
    }

    /**
     *  Return Standard Checkout Form Fields for request to Chinapay
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
		//导入私钥文件, 返回值即为您的商户号，长度15位
		$merid = buildKey(PRI_KEY);
		if(!$merid) {
		echo "导入私钥文件失败！";
		exit;
		}
		//var_dump('123');
		//exit;
		
		//生成订单号，定长16位，任意数字组合，一天内不允许重复，本例采用当前时间戳，必填
		$ordid = "00" . date('YmdHis');
		//订单金额，定长12位，以分为单位，不足左补0，必填
		$total_fee = sprintf('%.2f', $order->getBaseGrandTotal())*100;
		$transamt = padstr($total_fee,12);
		//货币代码，3位，境内商户固定为156，表示人民币，必填
		$curyid = "156";
		//订单日期，本例采用当前日期，必填
		$transdate = date('Ymd');
		//交易类型，0001 表示支付交易，0002 表示退款交易
		$transtype = "0001";
		//$transtype = $this->getConfigData('service_type');
		//接口版本号，境内支付为 20070129，必填
		$version = "20070129";
		//页面返回地址(您服务器上可访问的URL)，最长80位，当用户完成支付后，银行页面会自动跳转到该页面，并POST订单结果信息，可选
		$pagereturl = $this->getSuccessURL();
		//$pagereturl = "$site_url/netpayclient_order_feedback.php";
		//后台返回地址(您服务器上可访问的URL)，最长80位，当用户完成支付后，我方服务器会POST订单结果信息到该页面，必填
		$bgreturl = $this->getNotifyURL();
		
		//Mage::log($bgreturl);
		//$bgreturl = "$site_url/netpayclient_order_feedback.php";
		
		/************************
		页面返回地址和后台返回地址的区别：
		后台返回从我方服务器发出，不受用户操作和浏览器的影响，从而保证交易结果的送达。
		************************/
	
		//支付网关号，4位，上线时建议留空，以跳转到银行列表页面由用户自由选择，本示例选用0001农商行网关便于测试，可选
		//$gateid = "0001";
		$gateid = "";
		//备注，最长60位，交易成功后会原样返回，可用于额外的订单跟踪等，可选
		//$priv1 = "memo";
		$priv1 = $order->getRealOrderId();
		//按次序组合订单信息为待签名串
		$plain = $merid . $ordid . $transamt . $curyid . $transdate . $transtype . $priv1;
		//生成签名值，必填
		$chkvalue = sign($plain);
		if (!$chkvalue) {
			echo "签名失败！";
			exit;
		}
	
		$parameter = array(
		           'MerId'  =>  $merid,
		           'Version'  =>  $version,
		           'OrdId'  =>  $ordid,
		           'TransAmt'  =>  $transamt,
		           'CuryId'  =>  $curyid,
		           'TransDate'  =>  $transdate,
		           'TransType'  =>  $transtype,
		           'BgRetUrl'  =>  $bgreturl,
		           'PageRetUrl'  =>  $pagereturl,
		           'GateId'  =>  $gateid,
		           'Priv1'  =>  $priv1,
		           'ChkValue'  =>  $chkvalue,
                        );
		//Mage::log($parameter);
		//exit;
        return $parameter;
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
	 * Return authorized languages by Chinapay
	 *
	 * @param	none
	 * @return	array
	 */
	protected function _getAuthorizedLanguages()
	{
		$languages = array();
		
        foreach (Mage::getConfig()->getNode('global/payment/chinapay_payment/languages')->asArray() as $data) 
		{
			$languages[$data['code']] = $data['name'];
		}
		
		return $languages;
	}
	
	/**
	 * Return language code to send to Chinapay
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
     *  Return response for Chinapay success payment
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
     *  Return response for Chinapay failure payment
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
