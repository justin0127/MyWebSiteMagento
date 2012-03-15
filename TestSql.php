<?php

//error_reporting(E_ERROR );


// connect to DSN MSSQL with a user and password

$connect = odbc_connect("mssql", "sa", "Password01") or die ("couldn't connect");

odbc_exec($connect, "use UPPOS");

//$result = odbc_exec($connect, "select top(5) CustomerID,CustomerName,DateOfBirth,Province,City,Address,Zip,Email,Mobile from CustomerBasicInf ");
$result = odbc_exec($connect, "select top(5) * from CustomerBasicInf ");

while($row = odbc_fetch_array($result)){
	//$CustomerArr[] = $row;
	$customerData['id'] = $row['CustomerID'];
	$customerData['firstname'] = substr($row['CustomerName'],0,2);
	$customerData['lastname'] = substr($row['CustomerName'],2);
	$customerData['dob'] = $row['DateOfBirth'];
	$customerData['region'] = $row['Province'];
	$customerData['city'] = $row['City'];
	$customerData['address'] = $row['Address'];
	$customerData['postcode'] = $row['Zip'];	
	$customerData['email'] = $row['Email'];
	$customerData['mobile'] = $row['Mobile'];		
	
	//var_dump($customerData);	
	$NewCustomer = updateCustomer($customerData);

	echo $NewCustomer;
}

odbc_free_result($result);

odbc_close($connect);


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


/* $customerData = array(
         'email' => 'test1@test.com',
         'firstname' => 'asd',
         'lastname' => 'asd',
         'password' => '123456'，
		 'id'=>'sdsdad'
); */

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
		}
	else{
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
	return 1;
       }
else{
		 if ($customerData['email'] == NULL || $customerData['email'] == '') {
		 	$EleA = 'P'.$posid;
		 	$email = $EleA.'@sisley.com';
		 	$customerData['email'] = $email;
		 }
		 $customerData['password'] = '123123';
		 $customer = setCustomer($customerData);
        }
        
     return 0;
}

//$NewCustomer = updateCustomer($customerData);

 //生成客户并返回$customerid
function setCustomer($customerData) {
        $customer = Mage::getModel('customer/customer')->setId(null);
        $customer = Mage::getModel('customer/customer')
        ->setData($customerData)
        ->save();
        $CustomerId = $customer->getId();
        return $CustomerId;
}

?>
