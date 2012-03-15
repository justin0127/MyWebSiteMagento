<?php
class Mec_Search_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
	if ($_GET) {
		if (!$_GET['name']){
			$this->_redirect('/');  //if post null
		}else{
			$ProductName = $_GET['name'];
			
			
			
			$StoreId = 0; //Mage::app()->getStore()->getId();
			$ProductIdArr = array();
			$ProductLinkArr = array();
			
			//get magento sql info
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
				$query = sprintf("SELECT entity_id FROM catalog_product_entity_varchar WHERE attribute_id='60' AND value LIKE BINARY '%s'",
				mysql_real_escape_string($ProductName));
				$result = mysql_query($query); 
				if (!$result) {
					$message = 'Invalid query: ' . mysql_error() . "\n";
					$message .= 'Whole query: ' . $query;
					die($message);
				}//end if 
				while ($row = mysql_fetch_assoc($result)) {
					$ProductIdArr[] = $row['entity_id'];
				}
				//var_dump($ProductName);
				//var_dump($ProductIdArr);
				//exit;
				foreach ($ProductIdArr as $key => $productId) {
					$query = sprintf("SELECT value FROM catalog_product_entity_varchar WHERE attribute_id='87' AND entity_id='%s' AND store_id='%s'",
					mysql_real_escape_string($productId),
					mysql_real_escape_string($StoreId));
					$result = mysql_query($query); 
					if (!$result) {
						$message = 'Invalid query: ' . mysql_error() . "\n";
						$message .= 'Whole query: ' . $query;
						die($message);
					}//end if 
					while ($row = mysql_fetch_assoc($result)) {
						$ProductLinkArr[] = $row['value'];
					}
				}
			}
			mysql_close($link);
			if(isset($ProductLinkArr['0'])){
			
				$url = $ProductLinkArr['0'];
				$this->_redirect($url);
			}else{
				$this->_redirect('http://www.sisley.com.cn/');
			}
			
			//$this->loadLayout();
			//$this->renderLayout();
		
			
		}	
		//var_dump($_GET);
	}else{
		$this->_redirect('/');   //if no anything to search
	}
    }
}
