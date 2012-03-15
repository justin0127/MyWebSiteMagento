<?php
$ref =  $_SERVER['HTTP_REFERER'];
$name = $_POST['nom_expediteur1'];
$sender = $_POST['adresse_expediteur1'];
$rev = $_POST['adresse_destinataire1'];
$msg = $_POST['message_destinataire1'];



/*
$ref =  $_SERVER['HTTP_REFERER'];
$name = "riko";
$sender = "r@s.com";
$rev = "riko.chen@grahamsys.com";
$msg = "reyes";
*/

$to = $rev;
$subject = $name."发来的推荐";

$message = "
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<html>
<head>
<title>HTML email</title>
</head>
<body>
<p>您好，</p>
<p>$name 希望与您分享这方面的资料：</p>
<p>$msg</p>
<p>我们期待您下次访问我们的网站：www.sisley.com.cn</p>
</body>
</html>
";

// 当发送 HTML 电子邮件时，请始终设置 content-type
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=utf-8" . "\r\n";

// 更多报头
$headers .= 'From: 希思黎客服中心<sisley_gift@163.com>' . "\r\n";

mail($to,$subject,$message,$headers);
header("Location: $ref");
?>
