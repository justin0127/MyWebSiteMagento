<?php
/**
 * @copyright  Copyright (c) 2010 Capacity Web Solutions Pvt. Ltd  (http://www.capacitywebsolutions.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CosmoCommerce_Inquiry_Model_Inquiry extends Mage_Core_Model_Abstract
{
  // necessary methods
  public function _construct()
  {
  		parent::_construct();
	    $this->_init('inquiry/inquiry');
  }
}

