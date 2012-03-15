<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer api V2
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Api_V2 extends Mage_Customer_Model_Customer_Api
{
    /**
     * Prepare data to insert/update.
     * Creating array for stdClass Object
     *
     * @param stdClass $data
     * @return array
     */
    protected function _prepareData($data)
    {
        if (null !== ($_data = get_object_vars($data))) {
            return $_data;
        }
        return array();
    }

    /**
     * Create new customer
     *
     * @param array $customerData
     * @return int
     */
    public function create($customerData)
    {
        $customerData = $this->_prepareData($customerData);
        try {
            $customer = Mage::getModel('customer/customer')
                ->setData($customerData)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $customer->getId();
    }

    /**
     * Retrieve cutomers data
     *
     * @param  array $filters
     * @return array
     */
    public function items($filters)
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('*');

        $preparedFilters = array();
        if (isset($filters->filter)) {
            foreach ($filters->filter as $_filter) {
                $preparedFilters[$_filter->key] = $_filter->value;
            }
        }
        if (isset($filters->complex_filter)) {
            foreach ($filters->complex_filter as $_filter) {
                $_value = $_filter->value;
                $preparedFilters[$_filter->key] = array(
                    $_value->key => $_value->value
                );
            }
        }

        if (!empty($preparedFilters)) {
            try {
                foreach ($preparedFilters as $field => $value) {
                    if (isset($this->_mapAttributes[$field])) {
                        $field = $this->_mapAttributes[$field];
                    }
                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        $result = array();
        foreach ($collection as $customer) {
            $data = $customer->toArray();
            $row  = array();

            foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
                $row[$attributeAlias] = (isset($data[$attributeCode]) ? $data[$attributeCode] : null);
            }

            foreach ($this->getAllowedAttributes($customer) as $attributeCode => $attribute) {
                if (isset($data[$attributeCode])) {
                    $row[$attributeCode] = $data[$attributeCode];
                }
            }

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Update customer data
     *
     * @param int $customerId
     * @param array $customerData
     * @return boolean
     */
     
	public function SendEmail($customer) { 
	/*** start edm ***/
	$SoapClient = new SoapClient("http://app.focussend.com/webservice/FocusSendWebService.asmx?WSDL",array('trace' => 1));
	$FocusUser   = new StdClass;
	$FocusUser->Email="arvatoservices@bertelsmann.com.cn";
	$FocusUser->Password=sha1("EDM$%^456");

	$FocusEmail=new StdClass;

	//邮件内容谨慎修改
	$header_txt = file_get_contents("/var/www/app/design/frontend/default/default/template/edm/edm_header.html");
	$content_txt = file_get_contents("/var/www/app/design/frontend/default/default/template/edm/edm_ecard.html");
	$footer_txt = file_get_contents("/var/www/app/design/frontend/default/default/template/edm/edm_footer.html");

	$content_txt = str_ireplace ('{{$customer.name}}',$customer->getFirstname().$customer->getLastname(),$content_txt);
	$content_txt = str_ireplace ('{{$customer.rank}}',$customer->getRank(),$content_txt);
	$content_txt = str_ireplace ('{{$customer.ecard}}',$customer->getECard(),$content_txt);
	$content_full = $header_txt.$content_txt.$footer_txt;
								  

	$FocusEmail->Body=$content_full;

	$FocusEmail->IsBodyHtml=true;
  
	$subject="恭喜您成为【希思黎官方网站暨网上商城】会员";

	//$FocusReceiver=new StdClass;
	$FocusReceiver->Email=$customer->getEmail();

	//send one email
	$result= $SoapClient->SendOne(array("user"=>$FocusUser,"email"=>$FocusEmail,"subject"=>$subject,"receiver"=>$FocusReceiver));
	/*** end edm ***/
	}
     
    public function update($customerId, $customerData)
    {
  
 $msg =   var_export($customerData,"true");
 $logFile = date('Y-m-d').'v2.txt';
 $msg = date('Y-m-d H:i:s').' '.$customerId.' >>> '.$msg."\r\n";
 file_put_contents($logFile,$msg,FILE_APPEND );
  	
    	$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
	$username = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/username');
	$password = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/password');
	
	$a = strval($customerId);
	
	//Mage::log('posinfo');
	//Mage::log($a);
	
    	$_customerId = '0'.$a;
    	
    	
	//$posstr = substr($_customerId, 5);
	//$posid = intval($_customerId);
	$posid = $_customerId;
	
	//Mage::log('posid');
	//Mage::log($posid);
	
	$link = mysql_connect('localhost', $username, $password);
				if (!$link) {
					die('Not connected : ' . mysql_error());
				}//end if
				$db_selected = mysql_select_db($dbname, $link);
				if (!$db_selected) {
					die ('Can\'t use foo : ' . mysql_error());
					Mage::log('can not connect ');
				}else{
				$query = sprintf("SELECT entity_id FROM customer_entity_varchar WHERE value ='%s' AND attribute_id='180'",
						mysql_real_escape_string($posid));
						$result = mysql_query($query); 
						if (!$result) {
							$message = 'Invalid query: ' . mysql_error() . "\n";
							$message .= 'Whole query: ' . $query;
							die($message);
						}//end if 
						while ($row = mysql_fetch_assoc($result)) {
							$cId = $row['entity_id'];
						}
				}
				Mage::log('get sql entity id');
				Mage::log($row['entity_id']);
				
				//mysql_close($link);
				
        $customer = Mage::getModel('customer/customer')->load($cId);
        
        Mage::log('@#$%^');
	Mage::log($cId);
        
	//Mage::log($customer);

        if (!$customer->getId()) {
            $this->_fault('not_exists');
        }

	//from no rank to ...
	$CurrentRank = $customer->getRank();

        foreach ($this->getAllowedAttributes($customer) as $attributeCode=>$attribute) {
            if (isset($customerData->$attributeCode)) {
                $customer->setData($attributeCode, $customerData->$attributeCode);
            }
        }

        $customer->save();
        
        //new rank
        $NewRank = $customer->getRank();
        
        if ($CurrentRank == NULL && $NewRank != NULL) {
        	$this->SendEmail($customer);
        }
        
        return true;
    }
}
