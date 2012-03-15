<?php
/**
 * @copyright  Copyright (c) 2010 Capacity Web Solutions Pvt. Ltd  (http://www.capacitywebsolutions.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class CosmoCommerce_Inquiry_Block_Manage_Inquiry extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function getAll()
	{
		$collection = Mage::getModel("inquiry/inquiry")->getCollection();
	}
}

