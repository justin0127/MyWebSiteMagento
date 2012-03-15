<h2>IMPOS</h2>
<h2>UPPOS</h2>

<?php

error_reporting(E_ERROR);


// connect to DSN MSSQL with a user and password

$connect = odbc_connect("mssql", "sa", "Password01") or die ("couldn't connect");

//odbc_exec($connect, "use SSDC");

$result = odbc_exec($connect, "select top(5) * from CustomerBasicInf");

 //SELECT row_number(1) OVER (ORDER BY Col1 DESC) FROM FROMSISLEY_BAK_WCLAD 

 //$result = mb_convert_encoding($result, "UTF-8");
 odbc_result_all($result);
//odbc_fetch_row($result);



$_customer = array();
$CustomerArr = array();
$max = odbc_num_fields($res);

while(odbc_fetch_row($result)){
$max = odbc_num_fields($res);
odbc_fetch_row($result);

for($i=1;$i<=$max;$i++) {
	$_customer[] = odbc_result($result,$i);
}



//print(odbc_result_all($result));

$CustomerArr[] = $_customer;
}


odbc_free_result($result);

odbc_close($connect);

var_dump($max);
?>

___________________________________________

<?php var_dump($CustomerArr); ?>

<?php 

/*$customerData = array(
        	'email' => 'test1@test.com',
        	'firstname' => 'asd',
        	'lastname' => 'asd',
);
 
function setCustomer($customerData) {
        $customer = Mage::getModel('customer/customer')->setId(null);
        $customer = Mage::getModel('customer/customer')
        ->setData($customerData)
        ->save();
        $CustomerId = $customer->getId();
        return $CustomerId;
}

*/
?>

<?php
/**
 ** get all new orders info
/*
$customerData = array(
         'email' => 'test1@test.com',
         'firstname' => 'asd',
         'lastname' => 'asd',
         'password' => '123456'，
		 'id'=>'sdsdad'
);
 //生成客户并返回$customerid
function setCustomer($customerData) {
        $customer = Mage::getModel('customer/customer')->setId(null);
        $customer = Mage::getModel('customer/customer')
        ->setData($customerData)
        ->save();
        $CustomerId = $customer->getId();
        return $CustomerId;
}
//更新已有的客户信息
function updateCustomer($customerData) {

$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
$username = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/username');
$password = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/password');

$posid = $customerData['id'];
$customers = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addFieldToFilter('posid', $posid);
if (count($customers) > 0) {
$link = mysql_connect('localhost', $username, $password);
if (!$link) {
die('Not connected : ' . mysql_error());
}//end if
$db_selected = mysql_select_db($dbname, $link);
if (!$db_selected) {
die ('Can\'t use foo : ' . mysql_error());
}else{
$query = sprintf("SELECT entity_id FROM customer_entity_varchar WHERE attribute_id ='180' AND value = '%s'",
mysql_real_escape_string($posid));
$result = mysql_query($query); 
if (!$result) {
$message = 'Invalid query: ' . mysql_error() . "\n";
$message .= 'Whole query: ' . $query;
die($message);
}//end if 
while ($row = mysql_fetch_assoc($result)) {
$customerId = $row['entity_id'];
}
}
$customer = Mage::getModel('customer/customer')->load($customerId);
foreach ($customerData as $key=>$value) {
			$customer->setData($key, $customerData[$key]);
		}
$customer->save();
mysql_close($link);
        }else{
		 if ($customerData['email'] != NULL) {
		 	$EleA = 'P'.$posid;
		 	$email = $EleA.'@sisley.com';
		 	$customerData[email] = $email;
		 }
		 $customerData[password] = '123123';
		 $customer = setCustomer($customerData);
        }
        
        return $customer;
}

$NewCustomer = updateCustomer($customerData);
*/
?>
