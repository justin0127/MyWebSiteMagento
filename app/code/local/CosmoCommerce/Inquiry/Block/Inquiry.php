<?php
/**
 * @copyright  Copyright (c) 2010 Capacity Web Solutions Pvt. Ltd  (http://www.capacitywebsolutions.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class CosmoCommerce_Inquiry_Block_Inquiry extends Mage_Core_Block_Template
{

/*Start fo functions for admin section.*/
	public function getAllInquires()
	{
		if($collection = Mage::getModel("inquiry/inquiry")->getCollection())
			$collection->setOrder('createddt',"Desc")->load();
		return $collection;
	}
	
	public function getAllDealer($delId)
	{
		$collection = Mage::getModel("inquiry/inquiry")->load($delId)->getData();
		return $collection;
	}
	public function getIsCreated($email)
	{
		$collection = Mage::getModel("customer/customer")->getCollection()->addFieldToFilter("email",$email);
		if($collection->count())
			return 1;
		else
			return 0;
	}
/*End of functions for admin section.*/
}

