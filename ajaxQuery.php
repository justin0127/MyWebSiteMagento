<?php
$type = $_GET['type'];
$campaign = $_GET['campaign'];
$parameter = $_GET['parameter'];

$dbname = "sisley";
$username = "root";
$password = "toor";

mysql_connect("localhost", $username, $password) or  die("Could not connect: " . mysql_error());
mysql_select_db($dbname);

if($type == 1){
	$query = sprintf("SELECT * FROM sisley_survey WHERE campaign_id = '%s' and increment_id   ='%s'",mysql_real_escape_string($campaign),mysql_real_escape_string($parameter));
	//echo $query."<br/>";
	$result = mysql_query($query) or die("Invalid query: " . mysql_error());
	$count=mysql_num_rows($result);
	if($count>0)
		return 0;
	else
		return 1;
	}

mysql_close();

?>