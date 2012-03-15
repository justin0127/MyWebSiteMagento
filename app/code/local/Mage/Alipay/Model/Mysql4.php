<?php


class Mage_Alipay_Model_Mysql4 extends Mage_Payment_Model_Method_Abstract{
    public function _construct(){  
      parent::_construct();  
      $this->_init('model/Mysql4');  
    }  
}
?>