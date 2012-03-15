<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<?php

require_once '/var/www/app/Mage.php';
umask ( 0 );
Mage::app ( 'default' );
Mage::log( "Web Service for SAP start");
?>
<?php
set_time_limit(0);

UploadCustomerToPOS();
UploadOrderToPOS();


function getTotalQtyItemsOrdered($order) {
        $qty = 0;
        $orderedItems = $order->getItemsCollection();
        foreach ($orderedItems as $item)
        {
                $qty += (int)$item->getQtyOrdered();
        }
        return $qty;
}

function getItemsOrdered($order) {
	$orderedItems = $order->getItemsCollection();
	$Detail = array();
	
        foreach ($orderedItems as $item)
        {
			if(is_null($item->getParentItemId())){
				$Item['OrderID'] = $order->getIncrementId();
				$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item->getSku());
				if($product->getTypeId() =='configurable') {
					$Item['ProductID'] = $item->getProductOptionByCode('simple_sku');
				}else{
					$Item['ProductID'] = $item->getSku();
				}
				$Item['Price'] = $item->getData('price');
				$Item['Quantity'] = $item->getQtyOrdered();
				$Detail[] = $Item;
			}
        }
		
        return $Detail;
}

function UploadOrderToPOS(){
echo "[".date('Y-m-d H:i:s')."]Order Start<br/>";
$ArrayOrder = getOrderCollection();
echo "订单数".count($ArrayOrder);

$orders = Array();
$i = 0;
//var_dump($ArrayOrder);
 if(count($ArrayOrder)) {
#OrderID,CustomerID,TypeOfTransaction,DateOfTransaction,AmountOfTransaction,Quantity,ShippingCost  <--OrderHeader
#OrderID,ProductID,Price,Quantity <--OrderDetail
	 foreach($ArrayOrder as $_order){
		 $orders[$i]['OrderID'] = $_order->getIncrementId ();
		 //$orders[$i]['CustomerID'] = $_order->getCustomerId();
		 $customer = Mage::getModel('customer/customer')->load($_order->getCustomerId());
		 $orders[$i]['CustomerID'] = $customer->getPosId(); 
		 $orders[$i]['TypeOfTransaction'] = $_order->getStatus(); 
		 $orders[$i]['DateOfTransaction'] = $_order->getUpdatedAt(); 
		 $orders[$i]['AmountOfTransaction'] = $_order->getBaseGrandTotal(); 
		 $orders[$i]['Quantity'] = getTotalQtyItemsOrdered($_order); 
		 $orders[$i]['ShippingCost'] = number_format($_order->getBaseShippingAmount(), 0, '.', '');
		 $orders[$i]['Detail'] = getItemsOrdered($_order);
		 $i++;
	 }
 }
	if(count($orders)>0)
	{
		echo "[".date('Y-m-d H:i:s')."]开始往SQL导入订单信息.<br/>";
		InsertOrderData($orders);
	}
	//var_dump($orders);
}

function InsertOrderData($orders)
{
// connect to DSN MSSQL with a user and password
$connect = odbc_connect("mssql", "sa", "Password01") or die ("couldn't connect");
odbc_exec($connect, "use UPPOS");

foreach($orders as $k => $v)
{
//var_dump($orders);
#OrderID,CustomerID,TypeOfTransaction,DateOfTransaction,AmountOfTransaction,Quantity,ShippingCost,TotalAmounts  <--OrderHeader
#OrderID,ProductID,Price,Quantity <--OrderDetail

	$query = sprintf("{call Proc_Insertpurheader_C(N'%s',N'%s',N'%s',N'%s',N'%s',N'%s',N'%s',N'%s')}",$orders[$k]['OrderID'],$orders[$k]['CustomerID'],$orders[$k]['TypeOfTransaction'],$orders[$k]['DateOfTransaction'],$orders[$k]['AmountOfTransaction'],$orders[$k]['Quantity'],$orders[$k]['ShippingCost'],$orders[$k]['ShippingCost'] + $orders[$k]['AmountOfTransaction']);
	//$query = iconv("UTF-8","GBK",$query);
	echo $query."<br/>";
	$result = odbc_exec($connect,$query); 
	odbc_free_result($result);
	
	$details = $orders[$k]['Detail'];
	foreach($details as $kk => $vv){
		$query = sprintf("{call Proc_Insertpurdetail_C(N'%s',N'%s',N'%s',N'%s')}",$details[$kk]['OrderID'],$details[$kk]['ProductID'],$details[$kk]['Price'],$details[$kk]['Quantity']);
		echo $query."<br>";
		$result = odbc_exec($connect,$query);
		odbc_free_result($result);
	}
	echo "<br/><br/><br/>";
}

echo "订单数据上传到POS执行完毕。";
odbc_close($connect);
}
function UploadCustomerToPOS(){
echo "[".date('Y-m-d H:i:s')."]开始同步客户数据。<br/>";
$ArrayCustomer = getCustomerCollection();
$customers = Array();
$i = 0;
// var_dump($ArrayCustomer);
// exit(0);
//echo count($ArrayCustomer);
if (count($ArrayCustomer)) {
	foreach ($ArrayCustomer as $_customer) {
		$AddressInfo = getCustomerAddress($_customer);
		//var_dump($AddressInfo['postcode']);
		//#CustomerID,CustomerName,DateOfBirth,Province,City,Address,Zip,Email,Mobile
		$customers[$i]['ids'] = $_customer->getId();
		$customers[$i]['CustomerID'] = $_customer->getPosId();
		$customers[$i]['CustomerName'] = iconv("UTF-8","GBK",trim($_customer->getFirstname()).trim($_customer->getLastname()));
		$customers[$i]['DateOfBirth'] = $_customer->getDob();
		$customers[$i]['Province'] = iconv("UTF-8","GBK",$AddressInfo['region']);
		$customers[$i]['City'] = iconv("UTF-8","GBK",$AddressInfo['city']);
		$customers[$i]['Address'] = iconv("UTF-8","GBK",$AddressInfo['street'][0]);
		$customers[$i]['Zip'] = $AddressInfo['postcode'];
		$customers[$i]['Email'] = $_customer->getEmail();
		$customers[$i]['Mobile'] = $_customer->getMobile();	
		$customers[$i]['ECard'] = $_customer->getE_card();	
		$i++;
	}
	
	//var_dump($customers);
	if(count($customers)>0)
	{
		echo "[".date('Y-m-d H:i:s')."]开始往SQL导入客户信息.<br/>";
		InsertCustomerData($customers);
		}
	}
}

function InsertCustomerData($customers)
{
// connect to DSN MSSQL with a user and password
$connect = odbc_connect("mssql", "sa", "Password01") or die ("couldn't connect");
odbc_exec($connect, "use UPPOS");

foreach($customers as $k => $v)
{
	//#CustomerID,CustomerName,DateOfBirth,Province,City,Address,Zip,Email,Mobile
	$pos_id = $customers[$k]['CustomerID'];
	if($pos_id  <> '' && (substr($pos_id, 0, 5) == '08797' || substr($pos_id, 0, 5) == '08798')){
	$query = sprintf("{call Proc_InsertCus_C(N'%s',N'%s',N'%s',N'%s',N'%s',N'%s',N'%s',N'%s',N'%s',N'%s')",$customers[$k]['CustomerID'],
	$customers[$k]['CustomerName'],$customers[$k]['DateOfBirth'],$customers[$k]['Province'],$customers[$k]['City'],$customers[$k]['Address'],$customers[$k]['Zip'],$customers[$k]['Email'],$customers[$k]['Mobile'],$customers[$k]['ECard']);
	echo iconv("GBK","UTF-8",$query)."<br>";
	$result = odbc_exec($connect,$query);
 
	odbc_free_result($result);
	}
}

echo "客户数据上传到POS执行完毕。<br/>";
odbc_close($connect);
}

/**
 ** get all new orders info
 */
function getOrderCollection() {
	$yesterday = date('Y-m-d',strtotime('-1 days')).' 00:00:00';
	$today = date('Y-m-d',strtotime('-1 days')).' 24:00:00';
	 // $yesterday = "2011-08-01";
	 // $today = date('Y-m-d H:i:s');
	
	$collection = Mage::getModel('sales/order')->getCollection()
	    ->addAttributeToSelect('*')
	    ->addAttributeToFilter('updated_at', array("from" =>  $yesterday, "to" =>  $today, "datetime" => true))
		//->addAttributeToFilter('increment_id', '100000896');
	    //->addAttributeToFilter('status', 'shipping');
	    //->addFieldToFilter('status', array( 'success' ,'shipping' ,'reject' ,'return'));
	    ->addFieldToFilter('status', array( 'shipping' ,'reject' ,'return'));
	return $collection;
}

function getCustomerAddress($_customer) {
	$customerAddressId = $_customer -> getDefaultBilling();
	$AddressArr = array();
	if ($customerAddressId){
		$address = Mage::getModel('customer/address')->load($customerAddressId);
		$region = $address->getRegion();
		$city = $address->getCity();
		$street = $address->getStreet();
		$postcode = $address->getPostcode();
		
		//set address arr
		$AddressArr['address'] = $address;
		$AddressArr['region'] = $region;
		$AddressArr['city'] = $city;
		$AddressArr['street'] = $street;
		$AddressArr['postcode'] = $postcode;
	}
	return $AddressArr;
}

function getCustomerCollection() {
	$yesterday = date('Y-m-d',strtotime('-1 days')).' 00:00:00';
	$today = date('Y-m-d',strtotime('-1 days')).' 24:00:00';
	//$today = date('Y-m-d H:i:s');
	//$yesterday = "2011-08-15";
	//$today = "2011-08-17";
	echo "[".date('Y-m-d H:i:s')."] ".$yesterday."~".$today.".<br/>";
	$collection = Mage::getModel('customer/customer')->getCollection()
	    ->addAttributeToSelect('*')
	    ->addFieldToFilter('updated_at', array("from" =>  $yesterday, "to" =>  $today, "datetime" => true))
		->addFieldToFilter('pos_id', array("like" =>  array('08797%')));

	return $collection;
}
?>         
</html>