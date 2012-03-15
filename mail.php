<?php
$ref =  $_SERVER['HTTP_REFERER'];
$name = $_POST['nom_expediteur'];
$sender = $_POST['adresse_expediteur'];
$rev = $_POST['adresse_destinataire'];
$msg = $_POST['message_destinataire'];

$context_header = file_get_contents("/var/www/app/design/frontend/default/default/template/edm/edm_header.html");
$context_body   = file_get_contents("/var/www/app/design/frontend/default/default/template/edm/edm_share.html");
$context_footer = file_get_contents("/var/www/app/design/frontend/default/default/template/edm/edm_footer.html");

$context_body = str_ireplace ('{{$name}}',$name,$context_body);
$context_body = str_ireplace ('{{$msg}}',$msg,$context_body);

$context = $context_header.$context_body.$context_footer;

//echo $context;

							  $SoapClient = new SoapClient("http://app.focussend.com/webservice/FocusSendWebService.asmx?WSDL",array('trace' => 1));
							  $FocusUser   = new StdClass;
							  $FocusUser->Email="arvatoservices@bertelsmann.com.cn";
							  $FocusUser->Password=sha1("EDM$%^456");
							  
							  $FocusEmail=new StdClass;
							  $FocusEmail->Body=$context;
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

							  $subject=" 你的好友想跟您分享一些关于【希思黎官方网站暨网上商城】的信息";
							  
							  //$FocusReceiver=new StdClass;
							  $FocusReceiver->Email=$rev;
							  
							  //Mage::log('123');
							  //Mage::log($FocusEmail);
							  
							  //send one email
							  $result= $SoapClient->SendOne(array("user"=>$FocusUser,"email"=>$FocusEmail,"subject"=>$subject,"receiver"=>$FocusReceiver));
							  //var_dump($result);
header("Location: $ref");
?>
