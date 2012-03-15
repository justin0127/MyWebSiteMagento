<?php
/*Class Mage_FreeItem_Model_FreeItem extends Mage_Core_Model_Abstract
{
	
	$sku =  Mage::getStoreConfig('catalog/freeitem/product_sku');
	$total_purchase = Mage::getStoreConfig('catalog/freeitem/total_purchase');
	$product = new Mage_Catalog_Model_Product();
	$cart = Mage::getSingleton('checkout/cart');
	
	$price = $product->getPrice();
	
	try
	{
	     if($price > $total_purchase)
	      {
	        $productid = $product->load(getIdBySku($sku));
	        $productid->save();
	        header('Location: '. Mage::getUrl('checkout/cart/add', array('product' => $productid))); 
	      }
	}
	catch(Exception $e)
	{
		echo $e;
		die();
	}
	
	
       $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load(getIdBySku($sku));
    
}*/




?>