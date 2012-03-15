<?php
$mail_sender=$_POST['mail_sender'];
$mail_from =$_POST['mail_from'];
$mail_to = $_POST['mail_to'];
$mail_content = $_POST['mail_content'];


							  $SoapClient = new SoapClient("http://app.focussend.com/webservice/FocusSendWebService.asmx?WSDL",array('trace' => 1));
							  $FocusUser   = new StdClass;
							  $FocusUser->Email="arvatoservices@bertelsmann.com.cn";
							  $FocusUser->Password=sha1("arvatoli");
							  
							  $FocusEmail=new StdClass;
							  $FocusEmail->Body="<table width='635px' cellspacing='0' cellpadding='0' border='0'><tr><td colspan='6'><a href='http://www.sisley.com.cn'><img src='http://202.170.217.58/skin/frontend/default/default/images/logo_email.gif' alt='sisley_logo'/></a></td></tr><tr><td colspan='6'><table  style='text-align: center;' cellspacing='0' cellpadding='0' border='0' width='635px'><tr style='background:#AAAAAA; color:white; ' height='1px'><td style='width:105px; border-right:1px solid white;'><a href='http://202.170.217.58/news.html' ><font size='2'>希思黎最新动态</font></a></td><td style='width:105px; border-right:1px solid white;'><a href='http://202.170.217.58/about-sisley.html'><font size='2'>关于希思</font></a></td><td style='width:105px; border-right:1px solid white;'><a href='http://202.170.217.58/skin.html'><font size='2'>护肤系列</font></a></td><td style='width:105px; border-right:1px solid white;'><a href='http://202.170.217.58/color.html'><font size='2'>彩妆系列</font></a></td><td style='width:105px; border-right:1px solid white;'><a href='http://202.170.217.58/perfume.html'><font size='2'>香水系列</font></a></td><td style='width:105px;'><a href='http://202.170.217.58/man.html'><font size='2'>男士系列</font></a></td></tr></table></td></tr><tr><td colspan='6'><table  style='text-align: center;' cellspacing='0' cellpadding='0' border='0' width='635px'><tr><td valign='top' width='390px' align='left'><font size='2'>/font><br /><br /><font size='2'>
							  您的朋友".$mail_sender."<".$mail_from."> 想跟您分享以下信息:<br/>".$mail_content."
							  <br /></font></td></tr><tr><td colspan='6'><table width='635px' ><tr style='background:#E0E0E0;' ><td colspan='9'><font  size='2'>希思黎最受欢迎产品:</font></td></tr><tr><td width='20px'></td><td ><table><tr><td align='center' width='110px'><a href='http://202.170.217.58/114100.html'><img src='http://202.170.217.58/skin/frontend/default/default/images/email-img/全能乳液.jpg' alt='全能乳液'  style='border:1px solid #e1e1e1; margin-left=32px; margin-right:32px;width:100px;height:100px;'/></a></td></tr><tr><td align='center' width='110px'><a href='http://202.170.217.58/114100.html'><font size='2'>全能乳液</font></a><br /><a href='http://202.170.217.58/114100.html'><font size='2'>125ml - ￥1,480.00</font></a></td></tr></table></td><td width='25px'></td><td><table><tr><td align='center' width='110px'><a href='http://202.170.217.58/154000.html'><img src='http://202.170.217.58/skin/frontend/default/default/images/email-img/致臻夜间修复精华露.jpg' alt='致臻夜间修复精华露'  style='border:1px solid #e1e1e1;margin-left=32px; margin-right:32px;width:100px;height:100px;' /></a></td></tr><tr><td align='center' width='110px'><a href='http://202.170.217.58/154000.html'><font size='2'>致臻夜间修复精华露</font></a><br /><a href='http://202.170.217.58/154000.html'><font size='2'>50ml - ￥5,000.00</font></a></td></tr></table><td width='25px'></td><td><table><tr><td align='center' width='110px'><a href='http://202.170.217.58/162300.html'><img src='http://202.170.217.58/skin/frontend/default/default/images/email-img/全日呵护精华乳.jpg' alt='全日呵护精华乳'  style='border:1px solid #e1e1e1;margin-left=32px; margin-right:32px;width:100px;height:100px;'/></a></td></tr><tr><td align='center' width='110px'><a href='http://202.170.217.58/162300.html'><font size='2'>全日呵护精华乳</font></a><br /><a href='http://202.170.217.58/162300.html'><font size='2'>50ml - ￥2,500.00</font></a></td></tr></table><td width='25px'></td><td><table><tr><td align='center' width='110px'><a href='http://202.170.217.58/150000.html'><img src='http://202.170.217.58/skin/frontend/default/default/images/email-img/抗皱活肤驻颜霜.jpg' alt='抗皱活肤驻颜霜'  style='border:1px solid #e1e1e1; margin-left=32px; margin-right:32px;width:100px;height:100px;'/></a></td></tr><tr><td align='center' width='110px'><a href='http://202.170.217.58/150000.html'><font size='2'>抗皱活肤驻颜霜</font></a><br /><a href='http://202.170.217.58/150000.html'><font size='2'>50ml - ￥2,850.00</font></a></td></tr></table><td></td></tr></table></td></tr><tr><td height='20px'></td></tr><tr><td colspan='6' align='center'><img src='http://202.170.217.58/skin/frontend/default/default/images/email-bg.jpg' alt='促销商品通栏Banner'/></td></tr><tr><td colspan='6' height='20px'><font size='2'><a href='#'>促销商品链接</a></font></td></tr><tr><td colspan='6' style='background:#e0e0e0'><font size='2'>在希思黎官网您可以尊享：100%正品保证<br />希思黎官方网站暨网上商城会员热线：4008-208-139(9:00-21:00国定节假日除外)<br />了解更多信息，请访问帮助中心。请保管您的邮箱，以免他人盗用，如有任何疑问，请查看希思黎会员条款。<br />请注意：此邮件发送地址仅用于发送邮件，请勿直接回复。如须与我们联系，您可以发送邮件至cs@sisley.com.cn<br /></font></td></tr></table>";  //内容,链接不能删
							  $FocusEmail->IsBodyHtml=true;
							  
							  $FocusTask=new StdClass;
							  $FocusTask->TaskName="batch send php";
							  $FocusTask->Subject="first subject";
							  $FocusTask->SenderName="abc";
							  $FocusTask->SenderEmail="abc@focussend.com";
							  $FocusTask->ReplyName="reply";
							  $FocusTask->ReplyEmail="zcz_wn@163.com";
							  //$FocusTask->SendDate=date("Y-m-d\TH-m-s");
							  $FocusTask->SendDate="2010-06-04T00:00:00";    
							  
							  //echo $FocusTask->SendDate;

							  $subject=" 你的好友想跟您分享一些有关于【希思黎官方网站暨网上商城】的信息";
							  
							  //$FocusReceiver=new StdClass;
							  $FocusReceiver->Email=$mail_to;
							  
							  //Mage::log('123');
							  //Mage::log($FocusEmail);
							  
							  //send one email
							  $result= $SoapClient->SendOne(array("user"=>$FocusUser,"email"=>$FocusEmail,"subject"=>$subject,"receiver"=>$FocusReceiver));
							  //var_dump($result);
?>