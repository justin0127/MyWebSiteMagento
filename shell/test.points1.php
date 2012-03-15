<?php
require_once '/var/www/app/Mage.php';
umask ( 0 );

Mage::app ( 'default' );
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
//获取调查ID号
function GetSuveryID(){
	return "sisleysuvery20111124";
}
function SendEmail($customer, $_order) { 
/*** start edm ***/
$SoapClient = new SoapClient("http://app.focussend.com/webservice/FocusSendWebService.asmx?WSDL",array('trace' => 1));
$FocusUser   = new StdClass;
$FocusUser->Email="arvatoservices@bertelsmann.com.cn";
$FocusUser->Password=sha1("EDM$%^456");

$FocusEmail=new StdClass;

$suveryId=GetSuveryID();
//邮件内容谨慎修改

$FocusEmail->Body="<style>#__01{border-spacing:0;}</style><table id='__01' width='640' height='822' border='0' cellpadding='0' cellspacing='0'>
	<tr  height='82' border='0'>
		<td colspan='9'  height='82' border='0'><a href='http://www.sisley.com.cn/?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=navi%2Dhome'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_01.png' alt='' width='640' height='82' border='0' style='vertical-align:bottom;'></a></td>
	</tr>
	<tr height='21' border='0'>
		<td height='21' border='0'><a href='http://www.sisley.com.cn/news.html?utm_source=edm_delivery_confirm&utm_medium=email&utm_campaign=Sisley-edm-delivery_confirm-arvato&utm_content=navi-news'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_02.png' alt='' width='112' height='21' border='0' style='vertical-align:bottom;'></a></td>
		<td height='21' border='0'><a href='http://www.sisley.com.cn/about-sisley.html?utm_source=edm%5Forder%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Dorder%5Fconfirm%2Darvato&utm_content=navi%2Dabout%2Dsisley'><img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_03.png' alt='' width='108' height='21' border='0'></a></td>
		<td colspan='2' height='21'><a href='http://www.sisley.com.cn/skincare.html?utm_source=edm_delivery_confirm&utm_medium=email&utm_campaign=Sisley-edm-delivery_confirm-arvato&utm_content=navi-skincare'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_04.png' alt='' width='106' height='21' border='0' style='vertical-align:bottom;'></a></td>
		<td colspan='2' height='21'><a href='http://www.sisley.com.cn/color.html?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=navi%2Dcolor'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_05.png' alt='' width='106' height='21' border='0' style='vertical-align:bottom;'></a></td>
		<td colspan='2' height='21'><a href='http://www.sisley.com.cn/perfume.html?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=navi%2Dperfume'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_06.png' alt='' width='106' height='21' border='0' style='vertical-align:bottom;'></a></td>
		<td height='21'><a href='http://www.sisley.com.cn/man.html?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=navi%2Dman'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_07.png' alt='' width='102' height='21' border='0' style='vertical-align:bottom;'></a></td>
	</tr>
	<tr  height='26' border='0'>
		<td colspan='9'  height='26' border='0'>
			<img src='http://sisley.cosmocommerce.com/campaign/images/edm2_08.png' width='640' height='26' alt='' style='vertical-align:bottom;'></td>
	</tr>
	<tr height='34' >
		<td colspan='9' height='34' >
			<img src='http://sisley.cosmocommerce.com/campaign/images/edm2_09.png' width='640' height='34' alt='' style='vertical-align:bottom;'></td>
	</tr>
	<tr height='150'>
		<td colspan='3' rowspan='3' height='150'><a href='http://www.sisley.com.cn/package?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=package'><img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_10.png' alt='' width='307' height='492' border='0'></a></td>
		<td colspan='6' height='150'>
			<img src='http://sisley.cosmocommerce.com/campaign/images/edm2_11.png' width='333' height='150' alt='' style='vertical-align:bottom;'></td>
	</tr>
	<tr height='19'>
		<td colspan='2' height='19'>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_12.png' width='70' height='19' alt=''></td>
		<td colspan='2' height='19' style='font-size:12px;line-height:19px;'>".$_order."</td>
		<td colspan='2' height='19'>
			<img  style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_14.png' width='189' height='19' alt=''></td>
	</tr>
	<tr  height='323'>
		<td colspan='6'  height='323'>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_15.png' alt='' width='333' height='323' border='0' usemap='#Map'></td>
	</tr>
	<tr  height='166'>
		<td colspan='9'  height='166'>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_16.png' alt='' width='640' height='166' border='0' usemap='#Map2'></td>
	</tr>
	<tr height='1'>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='112' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='108' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='87' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='19' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='51' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='55' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='19' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='87' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='102' height='1' alt=''></td>
	</tr>
</table>
<!-- End Save for Web Slices -->

<map name='Map'>
  <area shape='rect' coords='40,58,97,83' href='http://www.sisley-beauty.com.cn?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=score' target='_blank'>
  <area shape='rect' coords='234,53,300,84' href='http://www.sisley-beauty.com.cn?utm_source=edm_delivery_confirm&utm_medium=email&utm_campaign=Sisley-edm-delivery_confirm-arvato&utm_content=score' target='_blank'>
  <area shape='rect' coords='41,223,100,241' href='http://www.sisley.com.cn/inquiry?email=".$customer->getEmail()."&id=".$suveryId."' target='_blank'>
</map>

<map name='Map2'>
  <area shape='rect' coords='12,8,624,47' href='http://www.sisley.com.cn/package?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=package' target='_blank'>
</map>";




$FocusEmail->IsBodyHtml=true;

$FocusTask=new StdClass;
$FocusTask->TaskName="batch send php";
$FocusTask->Subject="first subject";
$FocusTask->SenderName="abc";
$FocusTask->SenderEmail="abc@focussend.com";
$FocusTask->ReplyName="reply";
$FocusTask->ReplyEmail="zcz_wn@163.com";
$FocusTask->SendDate=date("Y-m-d\TH-m-s");
//$FocusTask->SendDate="2010-06-04T00:00:00";    

$subject="感谢您购买【希思黎官方网站暨网上商城】商品";

//$FocusReceiver=new StdClass;
//$FocusReceiver->Email='justin.huang@bertelsmann.com.cn';
$FocusReceiver->Email=$customer->getEmail();

//send one email
$result= $SoapClient->SendOne(array("user"=>$FocusUser,"email"=>$FocusEmail,"subject"=>$subject,"receiver"=>$FocusReceiver));

/*** end edm ***/
}

//SendEmail('f','f');
//echo "f";
//exit();
function getOrderCollection() {
	$targetday = date('Y-m-d',strtotime('-10 days'));
	$OrderArr = array();
	
	$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
	$username = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/username');
	$password = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/password');
	$link = mysql_connect('localhost', $username, $password);
				if (!$link) {
					die('Not connected : ' . mysql_error());
				}//end if
				$db_selected = mysql_select_db($dbname, $link);
				if (!$db_selected) {
					die ('Can\'t use foo : ' . mysql_error());
				}else{
				$_query = sprintf("SELECT orderid FROM arvato_point WHERE date='%s'",
						mysql_real_escape_string($targetday));
						$_result = mysql_query($_query); 
						if (!$_result) {
							$message = 'Invalid query: ' . mysql_error() . "\n";
							$message .= 'Whole query: ' . $_query;
							die($message);
						}
						while ($row = mysql_fetch_assoc($_result)) {
							$OrderArr[] = $row['orderid'];
						}
				}
				mysql_close($link);
	return $OrderArr;
}


$OrderList = getOrderCollection();
echo "total send mail count is ".count($OrderList)."<br>";
foreach ($OrderList as $_order) {
	$order = Mage::getModel('sales/order');
        $order->loadByIncrementId($_order);
	
	$email = $order->getCustomerEmail();
	$id = $order->getCustomerId();
	$customer = Mage::getModel('customer/customer');
	$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
	$customer->load($id);
	
	if ($order->getStatus() == 'Shipping' || $order->getStatus() == 'Success') {
		echo "sending email to ".$customer->getEmail();
		SendEmail($customer, $_order);
		echo " success<br>";
	}
	//var_dump($customer);
}


//
///
//从这里开始是mockup

/*** start edm ***/
/**
$SoapClient = new SoapClient("http://app.focussend.com/webservice/FocusSendWebService.asmx?WSDL",array('trace' => 1));
$FocusUser   = new StdClass;
$FocusUser->Email="arvatoservices@bertelsmann.com.cn";
$FocusUser->Password=sha1("EDM$%^456");

$FocusEmail=new StdClass;

$suveryId=GetSuveryID();
//邮件内容谨慎修改

$FocusEmail->Body="<style>#__01{border-spacing:0;}</style><table id='__01' width='640' height='822' border='0' cellpadding='0' cellspacing='0'>
	<tr  height='82' border='0'>
		<td colspan='9'  height='82' border='0'><a href='http://www.sisley.com.cn/?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=navi%2Dhome'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_01.png' alt='' width='640' height='82' border='0' style='vertical-align:bottom;'></a></td>
	</tr>
	<tr height='21' border='0'>
		<td height='21' border='0'><a href='http://www.sisley.com.cn/news.html?utm_source=edm_delivery_confirm&utm_medium=email&utm_campaign=Sisley-edm-delivery_confirm-arvato&utm_content=navi-news'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_02.png' alt='' width='112' height='21' border='0' style='vertical-align:bottom;'></a></td>
		<td height='21' border='0'><a href='http://www.sisley.com.cn/about-sisley.html?utm_source=edm%5Forder%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Dorder%5Fconfirm%2Darvato&utm_content=navi%2Dabout%2Dsisley'><img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_03.png' alt='' width='108' height='21' border='0'></a></td>
		<td colspan='2' height='21'><a href='http://www.sisley.com.cn/skincare.html?utm_source=edm_delivery_confirm&utm_medium=email&utm_campaign=Sisley-edm-delivery_confirm-arvato&utm_content=navi-skincare'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_04.png' alt='' width='106' height='21' border='0' style='vertical-align:bottom;'></a></td>
		<td colspan='2' height='21'><a href='http://www.sisley.com.cn/color.html?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=navi%2Dcolor'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_05.png' alt='' width='106' height='21' border='0' style='vertical-align:bottom;'></a></td>
		<td colspan='2' height='21'><a href='http://www.sisley.com.cn/perfume.html?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=navi%2Dperfume'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_06.png' alt='' width='106' height='21' border='0' style='vertical-align:bottom;'></a></td>
		<td height='21'><a href='http://www.sisley.com.cn/man.html?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=navi%2Dman'><img src='http://sisley.cosmocommerce.com/campaign/images/edm2_07.png' alt='' width='102' height='21' border='0' style='vertical-align:bottom;'></a></td>
	</tr>
	<tr  height='26' border='0'>
		<td colspan='9'  height='26' border='0'>
			<img src='http://sisley.cosmocommerce.com/campaign/images/edm2_08.png' width='640' height='26' alt='' style='vertical-align:bottom;'></td>
	</tr>
	<tr height='34' >
		<td colspan='9' height='34' >
			<img src='http://sisley.cosmocommerce.com/campaign/images/edm2_09.png' width='640' height='34' alt='' style='vertical-align:bottom;'></td>
	</tr>
	<tr height='150'>
		<td colspan='3' rowspan='3' height='150'><a href='http://www.sisley.com.cn/package?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=package'><img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_10.png' alt='' width='307' height='492' border='0'></a></td>
		<td colspan='6' height='150'>
			<img src='http://sisley.cosmocommerce.com/campaign/images/edm2_11.png' width='333' height='150' alt='' style='vertical-align:bottom;'></td>
	</tr>
	<tr height='19'>
		<td colspan='2' height='19'>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_12.png' width='70' height='19' alt=''></td>
		<td colspan='2' height='19' style='font-size:12px;line-height:19px;'> 1234567890</td>
		<td colspan='2' height='19'>
			<img  style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_14.png' width='189' height='19' alt=''></td>
	</tr>
	<tr  height='323'>
		<td colspan='6'  height='323'>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_15.png' alt='' width='333' height='323' border='0' usemap='#Map'></td>
	</tr>
	<tr  height='166'>
		<td colspan='9'  height='166'>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/edm2_16.png' alt='' width='640' height='166' border='0' usemap='#Map2'></td>
	</tr>
	<tr height='1'>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='112' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='108' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='87' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='19' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='51' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='55' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='19' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='87' height='1' alt=''></td>
		<td>
			<img style='vertical-align:bottom;' src='http://sisley.cosmocommerce.com/campaign/images/spacer.gif' width='102' height='1' alt=''></td>
	</tr>
</table>
<!-- End Save for Web Slices -->

<map name='Map'>
  <area shape='rect' coords='40,58,97,83' href='http://www.sisley-beauty.com.cn?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=score' target='_blank'>
  <area shape='rect' coords='234,53,300,84' href='http://www.sisley-beauty.com.cn?utm_source=edm_delivery_confirm&utm_medium=email&utm_campaign=Sisley-edm-delivery_confirm-arvato&utm_content=score' target='_blank'>
  <area shape='rect' coords='41,223,100,241' href='http://www.sisley.com.cn/inquiry?email=".$customer->getEmail()."&id=".$suveryId."' target='_blank'>
</map>

<map name='Map2'>
  <area shape='rect' coords='12,8,624,47' href='http://www.sisley.com.cn/package?utm_source=edm%5Fdelivery%5Fconfirm&utm_medium=email&utm_campaign=Sisley%2Dedm%2Ddelivery%5Fconfirm%2Darvato&utm_content=package' target='_blank'>
</map>";
*/


/**
$FocusEmail->IsBodyHtml=true;

$FocusTask=new StdClass;
$FocusTask->TaskName="batch send php";
$FocusTask->Subject="first subject";
$FocusTask->SenderName="abc";
$FocusTask->SenderEmail="abc@focussend.com";
$FocusTask->ReplyName="reply";
$FocusTask->ReplyEmail="zcz_wn@163.com";
$FocusTask->SendDate=date("Y-m-d\TH-m-s");
//$FocusTask->SendDate="2010-06-04T00:00:00";    

$subject="感谢您购买【希思黎官方网站暨网上商城】商品";

//$FocusReceiver=new StdClass;
//$FocusReceiver->Email='justin.huang@bertelsmann.com.cn';
$FocusReceiver->Email='justin0127_23@163.com';
//$FocusReceiver->Email="airforce.e@gmail.com";
//$FocusReceiver->Email="airforce.e@gmail.com";

//send one email
$result= $SoapClient->SendOne(array("user"=>$FocusUser,"email"=>$FocusEmail,"subject"=>$subject,"receiver"=>$FocusReceiver));

*/

?>