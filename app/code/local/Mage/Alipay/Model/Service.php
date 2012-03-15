<?php
/*
	*功能：输入类
	*版本：2.0
	*日期：2009-01-05
	*作者：支付宝公司销售部技术支持团队
	*联系：0571-26888888
	*版权：支付宝公司
*/
class Mage_Alipay_Model_Service extends Mage_Payment_Model_Method_Abstract{

	var $gateway = "https://www.alipay.com/cooperate/gateway.do?";         //支付接口
	var $parameter;       //全部需要传递的参数
	var $security_code;  	//安全校验码
	var $mysign;             //签名
	//var $partner_id = Mage::getStoreConfig('payment/alipay_payment/partner_id');
	//var $security_code  = Mage::getStoreConfig('payment/alipay_payment/security_code');
	
	function getForm($r_url){
	//exit(var_dump(Mage::getStoreConfig('payment/alipay_payment/security_code')));
     $id    = Mage::getStoreConfig('payment/alipay_payment/partner_id');
     $code  = Mage::getStoreConfig('payment/alipay_payment/security_code');
	   $parameter = array(
      	"service"    => "user_authentication", //user_authentication 会员验证接口
      	"partner"    => $id,              //合作商户号
      	"return_url" => $r_url,          //同步返回
      	"_input_charset" =>"GBK",
      	"email"      => ""                //卖家邮箱，必填
      );
      $alipay = $this->alipay_service($parameter,$code);
      $html = '<form id="alipaysubmit" name="alipaysubmit" method="post" action="https://www.alipay.com/cooperate/gateway.do?_input_charset=GBK">';
      $html .= '<input type=hidden name="email" value="">';
      $html .= '<input type=hidden name="service" value="user_authentication">';
      $html .= '<input type=hidden name="sign" value="'.$this->signParams().'">';
      $html .= '<input type=hidden name="sign_type" value="MD5">';
      $html .= '<input type=hidden name="partner" value="'.$parameter['partner'].'">';
      $html .= '<input type=hidden name="return_url" value="'.$parameter['return_url'].'">';
      $html .= '</form>';
      return $html;
  }

	//构造支付宝外部服务接口控制
	function alipay_service($parameter,$security_code,$sign_type = "MD5",$transport= "https") {
		$this->parameter      = $this->para_filter($parameter);
		$this->security_code  = $security_code;
		$this->sign_type      = $sign_type;
		$this->mysign         = '';
		$this->transport      = $transport;
		if($parameter['_input_charset'] == "")
		$this->parameter['_input_charset']='GBK';
		if($this->transport == "https") {
			$this->gateway = "https://www.alipay.com/cooperate/gateway.do?";
		} else $this->gateway = "http://www.alipay.com/cooperate/gateway.do?";
		$sort_array = array();
		$arg = "";
		$sort_array = $this->arg_sort($this->parameter);
		while (list ($key, $val) = each ($sort_array)) {
			$arg.=$key."=".$this->charset_encode($val,$this->parameter['_input_charset'])."&";
		}
		$prestr = substr($arg,0,count($arg)-2);  //去掉最后一个问号
		$this->mysign = $this->sign($prestr.$this->security_code);
	}


	function create_url() {
		$url = $this->gateway;
		$sort_array = array();
		$arg = "";
		$sort_array = $this->arg_sort($this->parameter);
		while (list ($key, $val) = each ($sort_array)) {
			$arg.=$key."=".urlencode($this->charset_encode($val,$this->parameter['_input_charset']))."&";
		}
		$url.= $arg."sign=" .$this->mysign ."&sign_type=".$this->sign_type;
		
		return $url;

	}

	function signParams() {
		$url = $this->gateway;
		$sort_array = array();
		$arg = "";
		$sort_array = $this->arg_sort($this->parameter);
		while (list ($key, $val) = each ($sort_array)) {
			$arg.=$key."=".urlencode($this->charset_encode($val,$this->parameter['_input_charset']))."&";
		}
		return $this->mysign;
	}

	function arg_sort($array) {
		ksort($array);
		reset($array);
		return $array;

	}

	function sign($prestr) {
		$mysign = "";
		if($this->sign_type == 'MD5') {
			$mysign = md5($prestr);
		}elseif($this->sign_type =='DSA') {
			//DSA 签名方法待后续开发
			die("DSA 签名方法待后续开发，请先使用MD5签名方式");
		}else {
			die("支付宝暂不支持".$this->sign_type."类型的签名方式");
		}
		return $mysign;

	}
	function para_filter($parameter) { //除去数组中的空值和签名模式
		$para = array();
		while (list ($key, $val) = each ($parameter)) {
			if($key == "sign" || $key == "sign_type" || $val == "")continue;
			else	$para[$key] = $parameter[$key];

		}
		return $para;
	}
	//实现多种字符编码方式
	function charset_encode($input,$_output_charset ,$_input_charset ="GBK" ) {
		$output = "";
		if(!isset($_output_charset) )$_output_charset  = $this->parameter['_input_charset '];
		if($_input_charset == $_output_charset || $input ==null) {
			$output = $input;
		} elseif (function_exists("mb_convert_encoding")){
			$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
		} elseif(function_exists("iconv")) {
			$output = iconv($_input_charset,$_output_charset,$input);
		} else die("sorry, you have no libs support for charset change.");
		return $output;
	}
	

}


?>