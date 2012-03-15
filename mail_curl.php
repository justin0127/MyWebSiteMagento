<?php
$ref =  "/skincare.html";
$name = $_POST['nom_expediteur1'];
$sender = $_POST['adresse_expediteur1'];
$rev = $_POST['adresse_destinataire1'];
$msg = $_POST['message_destinataire1'];

$curlPost['name'] = $name;
$curlPost['sender'] = $sender;
$curlPost['rev'] = $rev;
$curlPost['msg'] = $msg;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.sisley.com.cn/mail.php');
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
$data = curl_exec($ch);
//var_dump($curlPost);
//exit(0);

curl_close($ch); header("Location: $ref");

?><!--<script type="text/javascript">
	setTimeout(5000);
	alert("分享成功");
	window.location = "/skincare.html";
</script>

<div>感谢您的参与!正在提交。。。</div>
<script language=javascript>setTimeout("location.href='http://sisley.mec365.com/skincare.html'", 8);</script>

-->