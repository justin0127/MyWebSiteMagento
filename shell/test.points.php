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

$FocusEmail->Body="<table>
<table width='635px' cellspacing='0' cellpadding='0' border='0' style='margin-left:0.5em'>
<tr>
<td>
<a href='http://sisley.com.cn'>
<img src='http://www.sisley.com.cn/skin/frontend/default/default/images/logo_email.gif' alt='sisley_logo' border='0'/>
</a>
</td>
</tr>
<tr>
<td>
<table  style='text-align: center;' cellspacing='0' cellpadding='0' border='0' width='635px'>
<tr style='background:#AAAAAA; color:white; height:20px' >
<td style='width:55px; border-right:1px solid white;'><a href='http://www.sisley.com.cn/' style='text-decoration: none;'><font   style='color: #EEE;font-family: 黑体;font-size: 13px;font-weight: bold;'>首页</font></a></td>
<td style='width:113px; border-right:1px solid white;'><a href='http://www.sisley.com.cn/news.html' style='text-decoration: none;'><font style='color: #EEE;font-family: 黑体;font-size: 13px;font-weight: bold;'>希思黎最新动态</font></a></td>
<td style='width:92px; border-right:1px solid white;'><a href='http://www.sisley.com.cn/about-sisley.html'style='text-decoration: none;'><font  style='color: #EEE;font-family: 黑体;font-size: 13px;font-weight: bold;'>关于希思黎</font></a></td>
<td style='width:92px; border-right:1px solid white;'><a href='http://www.sisley.com.cn/skincare.html'style='text-decoration: none;'><font style='color: #EEE;font-family: 黑体;font-size: 13px;font-weight: bold;'>护肤系列</font></a></td>
<td style='width:92px; border-right:1px solid white;'><a href='http://www.sisley.com.cn/color.html'style='text-decoration: none;'><font style='color: #EEE;font-family: 黑体;font-size: 13px;font-weight: bold;'>彩妆系列</font></a></td>
<td style='width:92px; border-right:1px solid white;'><a href='http://www.sisley.com.cn/perfume.html'style='text-decoration: none;'><font style='color: #EEE;font-family: 黑体;font-size: 13px;font-weight: bold;'>香水系列</font></a></td>
<td style='width:92px; border-right:1px solid white;'><a href='http://www.sisley.com.cn/man.html'style='text-decoration: none;'><font style='color: #EEE;font-family: 黑体;font-size: 13px;font-weight: bold;'>男士系列</font></a></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table width='635px'>
<tr>
<td valign='top' width='380px'>
<br />
<font size='2'>亲爱的".$CustomerName = $customer->getFirstname().$customer->getLastname()."</font><br /><br />
<font size='2'>感谢您在希思黎官方购物网站<a href='http://www.sisley.com.cn'>www.sisley.com.cn</a>购买希思黎产<br />品。<br />
您的订单：".$_order."，已送达，并确认您已签收。<br /><br />
您此次的购买积分已激活为有效积分。您可以在积分有效期内使用<br />有效积分兑换喜爱的产品。详情查看<a href='http://www.sisley-beauty.com.cn'>积分规则</a>并通过链接登陆希思
<br />黎至臻坊社区查询您的积分并兑换相应的礼品。<br /><br />
<a href='http://www.sisley-beauty.com.cn'><img  align='left' src='http://www.sisley.com.cn/skin/frontend/default/default/images/jfcx.jpg'  border='0'/></a><br /><br />
<a href='http://www.sisley-beauty.com.cn' title='友情提醒:查询积分前请先注册为至臻坊会员'>http://www.sisley-beauty.com.cn/</a><br /><br />
<font color='#808080' style='font-weight: bold;'>友情提醒:查询积分前请先注册为至臻坊会员</font><br /><br />
同时，为了更好地为希思黎客人提供尊贵服务，我们希望占用您<br />
几分钟的时间，完成希思黎网上购物体验售后问卷。我们将针对<br />
您的意见，对我们的工作进行改进。<br />
<br />
<a href='http://www.sisley.com.cn/inquiry?email=".$customer->getEmail()."&id=".$suveryId."'><img  align='left' src='http://www.sisley.com.cn/skin/frontend/default/default/images/email/ljcjdtl.jpg' border='0' /></a><br /><br />
<br />
</font>
</td>
<td>
<a href='http://www.sisley.com.cn/114100.html'>
<img src='http://www.sisley.com.cn/skin/frontend/default/default/images/email-bg.png' width='241' height='332' border='0' alt='全能乳液'  /></a></td>
</tr>
<br />
</table>
</td>
</tr>
<tr>
<td >
<font size='2'>
欢迎您再次到希思黎官方网站暨网上商城，祝您购物愉快！<br /><br /></font>
</td>
</tr>
<!--tr style='background:#E0E0E0;' border='1'>
<td width='635px'>
	<table cellspacing='0' cellpadding='0' border='1' width='634px'>
	<tr><td style='background:#e0e0e0' align='left' width='634px'>
		<font size='2'>希思黎最受欢迎产品</font>
	</td></tr>
	</table>
</td></tr>
<tr>
<td width='635px'>
<table width='635px'>
<tr>
<td width='5px'>&nbsp;
</td>
<td width='135px'>
	<table cellspacing='0' cellpadding='0' border='0' width='135px'>
		<tr><td align='center' width='135px' >
			<a href='http://www.sisley.com.cn/classic-box/sv11100101.html'><img src='http://www.sisley.com.cn/skin/frontend/default/default/images/email-img/1_2.jpg' alt='紧致亮颜超值套装'  width='135px' height='135px' border='0' /></a>
		</td>
		</tr>
		<tr><td align='center' width='135px' >
			<a href='http://www.sisley.com.cn/classic-box/sv11100101.html'><font size='2'>紧致亮颜超值套装</font></a><br />
			<a href='http://www.sisley.com.cn/classic-box/sv11100101.html'><font size='2'>特献尊享价 ￥5400元 </font></a>
		</td></tr>
	</table>
</td>
<td width='15px'>&nbsp;
</td>
<td width='135px'>
	<table cellspacing='0' cellpadding='0' border='0' width='135px'>
	<tr><td align='center' width='135px'>
		<a href='http://www.sisley.com.cn/classic-box/sv11100103.html'><img src='http://www.sisley.com.cn/skin/frontend/default/default/images/email-img/2_2.jpg' alt='抗皱修护眼唇霜套装' width='135px' height='135px' border='0' /></a>
	 </td></tr>
	 <tr><td width='135px' align='center' >
		<a href='http://www.sisley.com.cn/classic-box/sv11100103.html'><font size='2'>抗皱修护眼唇霜套装</font></a><br />
		<a href='http://www.sisley.com.cn/classic-box/sv11100103.html'><font size='2'>特献尊享价 ￥1200元 </font></a>
	</td></tr>
	</table>
</td>
<td width='15px'>&nbsp;
</td>
<td width='145px'>
<table cellspacing='0' cellpadding='0' border='0'><tr><td align='center' width='145px'>
<a href='http://www.sisley.com.cn/classic-box/sv11102602.html'><img src='http://www.sisley.com.cn/skin/frontend/default/default/images/email-img/3_2.jpg' alt='抗皱修护眼唇霜超值套装' width='135px' height='135px' border='0'/></a>
</td></tr>
<tr><td align='center' width='145px' >
<a href='http://www.sisley.com.cn/classic-box/sv11102602.html'  width='145px' ><font size='2'>抗皱修护眼唇霜超值套装</font></a><br />
<a href='http://www.sisley.com.cn/classic-box/sv11102602.html'><font size='2'>特献尊享价 ￥1250元 </font></a>
</td></tr>
</table>
</td>
<td width='15px'>&nbsp;
</td>
<td width='135px'>
	<table cellspacing='0' cellpadding='0' border='0'><tr><td align='center' width='135px'>
	<a href='http://www.sisley.com.cn/classic-box/sv11102601-503.html'><img src='http://www.sisley.com.cn/skin/frontend/default/default/images/email-img/4_2.jpg' alt='瞬间紧致眼膜超值套装' width='135px' height='135px' border='0' /></a>
	 </td></tr>
	<tr><td align='center' width='135px' >
	<a href='http://www.sisley.com.cn/classic-box/sv11102601-503.html'><font size='2'>瞬间紧致眼膜超值套装</font></a><br />
	<a href='http://www.sisley.com.cn/classic-box/sv11102601-503.html'><font size='2'>特献尊享价 ￥900元</font></a>
	</td></tr>
	</table>
</td>
<td>
</td>
</tr>
</table>
</td></tr-->





<!--tr>
<td align='center' width='635px'>
<a href='http://www.sisley.com.cn/162300.html'
<img src='http://www.sisley.com.cn/skin/frontend/default/default/images/email-bg.jpg' alt='促销商品通栏Banner' width='635' height='140' border='0'/></a></td>
</tr-->
<!--tr>
<tr style='background:#E0E0E0;' border='1'>
<td width='635px' >
<table cellspacing='0' cellpadding='0' border='1' width='634px'>
	<tr><td style='background:#e0e0e0' align='left' width='634px'>
		<font size='2'>希思黎经典礼盒套装</font>
	</td></tr>
	</table>
</td>
</tr>
<tr>
<td height='15px'>
<font size='2'><a href='http://www.sisley.com.cn/classic-box/vs1-493.html'>全能乳液套装</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='http://www.sisley.com.cn/vs2-494.html'>花香保湿面膜套装</a></font>
</td>
</tr-->
<tr>
<td><br></td>
</tr>
</table>
<table width='635px' cellspacing='0' cellpadding='0' border='0' style='word-break:break-all;'>
<tr>
	<td width='635px'>&nbsp;</td>
</tr>
<tr>
<td style='background:#e0e0e0' width='635px'>
<font size='2'><br />
&nbsp;在希思黎官网您可以尊享：100%正品保证<br />
&nbsp;希思黎官方网站暨网上商城会员热线：4008-208-139(9:00-21:00国定节假日除外)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br /><br />
&nbsp;了解更多信息，请访问帮助中心。请保管您的邮箱，以免他人盗用，如有任何疑问，<br />

&nbsp;请查看希思黎会员条款。<br /><br />
&nbsp;请注意：此邮件发送地址仅用于发送邮件，请勿直接回复。<br/>
&nbsp;如须与我们联系，您可以发送邮件至cs@sisley.com.cn<br /><br />
</font>
</td>
</tr>
</table>
</table>";




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

?>
