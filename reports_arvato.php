<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh_cn" lang="zh_cn">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8;no-cache"  />
</head>
<?php
$task = $_REQUEST['task'];
$begDate = $_REQUEST['begDate'];
$endDate = $_REQUEST['endDate'];

$dbname = "sisley";
$username = "root";
$password = "toor";

mysql_connect("localhost", $username, $password) or  die("Could not connect: " . mysql_error());
mysql_select_db($dbname);

if($task == 1){
	$query = "SELECT sfo.customer_id,cev.`VALUE` AS pos_id,	customer_email,	date_add(min(sfo.created_at),interval 8 hour),	min(sfo.increment_id)AS first_order_date FROM sales_flat_order sfo,customer_entity_varchar cev WHERE	cev.entity_id = sfo.customer_id AND cev.attribute_id=180 GROUP BY sfo.customer_id ORDER BY	first_order_date";
	$result = mysql_query($query) or die("Invalid query: " . mysql_error());
	$count=mysql_num_rows($result);
	echo "<br/>共有".$count."条数<br/>";
	$table = "";
	$table = "<table border='1' cellpadding='1' cellspacing='1'><tr><th>客户ID</th><th>POS_ID</th><th>客户邮箱</th><th>客户首次下单时间</th><th>客户首次订单编号</th></tr>";
	while($arr = mysql_fetch_array($result,MYSQL_ASSOC)){ 
			$table .= "\t\t<tr>\n"; 
			foreach ($arr as $col_arr) { 
			$table .= "\t\t\t".'<td>'.$col_arr.'</td>'."\n";
			 } 
			$table .= "\t\t</tr>\n";                                       
	 } 
	$table .= "</table>"; 
	echo $table;
}
else if($task == 2){
	$query = "select DATE(`value`),count(*) from customer_entity_varchar where attribute_id = 185 and `value` <> '' group by DATE(`value`)";
	$result = mysql_query($query) or die("Invalid query: " . mysql_error());
	$count=mysql_num_rows($result);
	echo "<br/>共有".$count."条数<br/>";
	$table = "";
	$table = "<table border='1' cellpadding='1' cellspacing='1'><tr><th>激活日</th><th>激活人数</th></tr>";
	while($arr = mysql_fetch_array($result,MYSQL_ASSOC)){ 
			$table .= "\t\t<tr>\n"; 
			foreach ($arr as $col_arr) { 
			$table .= "\t\t\t".'<td>'.$col_arr.'</td>'."\n";
			 } 
			$table .= "\t\t</tr>\n";                                       
	 } 
	$table .= "</table>"; 
	echo $table;
}
else if($task == 3){
	$query = "select aae.ecard,aae.customerid,cev.`value` from arvato_ecard aae,customer_entity_varchar cev where aae.customerid = cev.entity_id and cev.attribute_id = 180";
	$result = mysql_query($query) or die("Invalid query: " . mysql_error());
	$count=mysql_num_rows($result);
	echo "<br/>共有".$count."条数<br/>";
	$table = "";
	$table = "<table border='1' cellpadding='1' cellspacing='1'><tr><th>E卡卡</th><th>Entity ID</th><th>POS ID</th></tr>";
	while($arr = mysql_fetch_array($result,MYSQL_ASSOC)){ 
			$table .= "\t\t<tr>\n"; 
			foreach ($arr as $col_arr) { 
			$table .= "\t\t\t".'<td>'.$col_arr.'</td>'."\n";
			 } 
			$table .= "\t\t</tr>\n";                                       
	 } 
	$table .= "</table>"; 
	echo $table;
}
mysql_close();
header("cache-control:no-cache,must-revalidate");
?>

<body>
<form>
<select name="task">
<option value="1" >客户首次下单明细</option>
<option value="2" >线下会员激活人数统计</option>
<option value="3" >线上会员E卡发放统计</option>
</select>
<input type="submit" name="查询" />
</form>
</body>
