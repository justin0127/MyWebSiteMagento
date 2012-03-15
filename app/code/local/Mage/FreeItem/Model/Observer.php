<?php

class Mage_FreeItem_Model_Observer 
{

    public function __construct(){}
            
    public function cart_update($observer)
    {
    	try
    	{
	    	$grand_total = Mage::getModel('checkout/cart')->getQuote()->getData('grand_total');
	    	$sku =  Mage::getStoreConfig('catalog/freeitem/product_sku');
	        $total_purchase = Mage::getStoreConfig('catalog/freeitem/total_purchase');
	        $product = Mage::getModel('catalog/product');
	        $id = $product->getIdBySku($sku);
	        $product->setStoreId(Mage::app()->getStore()->getId());
	        $product->load($id);
	        if($grand_total>=$total_purchase)
	        {
	        	   $cart = Mage::getModel('checkout/cart');                
	               $cart->init();
	               $cart_items = $cart->getQuote()->getAllItems();
	               foreach($cart_items as $items)
	               {
	               		if($items->getProduct()->getId()==$id)
	               		{
	               			$status = true;
	               			break;
	               		}
	               		else
	               		{
	               			$status = false;
	               		}
	               }
	               if(!$status)
	               {
	               		$cart->addProduct($product,array('qty'=>1));
	               		$cart->save();
	               		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);	               		
	               }
	               
			       //$cart->addProduct($product,array('qty'=>1,'price'=>0));
			       
                     //$free_item = $cart->getQuote()->getId();
                    // if(!($free_item))
                    // {
                     	//$cart->save();
                     	//Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                     //}
	               }
	             
                   /*$cart_items = $cart->getQuote()->getAllItems();  			       
			       foreach($cart_items as $cart_item)
			       {
			       	if($cart_item->getProductId() != $id)
			       	{
			       		
	 		          $cart->save();
	 		          //print_r($cart->save());
	 		          //die();
			          Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
			         
			        }       
			        
			       }*/
	    }
    	catch(Exception $e)
    	{
    		echo $e;
    	}
    }
    
    public function cart_add($observer)
    {
    	/*$cart = new Mage_Checkout_Model_Cart();
    	$data = $observer->getEvent()->getInfo();
    	Mage::dispatchEvent('checkout_cart_update_items_before', array('cart'=>$cart, 'info'=>$data));
        foreach ($data as $itemId => $itemInfo) {
            $item = $cart->getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }

            if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && $itemInfo['qty']=='0')) {
                $cart->removeItem($itemId);
                continue;
            }

            $qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
            if ($qty > 0) {
                $item->setQty($qty);
            }
        }
        Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$cart, 'info'=>$data));
        $cart->save();
        return;*/
    	//print_r($observer->getEvent()->getInfo());
    	//die();
    	try
    	{
    	//print_r($observer->getEvent()->getInfo());
    	//echo 'Hello';
    	//die();
    	//$updateshop = Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$this, 'info'=>$data));
    	$grand_total = Mage::getModel('checkout/cart')->getQuote()->getData('grand_total');
    	//$sub_total = Mage::getModel('checkout/cart')->getQuote()->getData('subtotal');
    	$total_purchase = Mage::getStoreConfig('catalog/freeitem/total_purchase');
    	$sku =  Mage::getStoreConfig('catalog/freeitem/product_sku');
    	$product = Mage::getModel('catalog/product');
	    $id = $product->getIdBySku($sku);
	    $product->setStoreId(Mage::app()->getStore()->getId());
	    $product->load($id);
	    //$cartupdate = Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
    	 if($grand_total>=$total_purchase)
	        {
	        	   
	        	   $cart = Mage::getModel('checkout/cart');                
	               //$cart->init();	 
	               //Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$cart, 'info'=>$grand_total));           
	               $cart_items = $cart->getQuote()->getAllItems();
	               
	               foreach($cart_items as $items)
	               {
	               		if($items->getProduct()->getId()==$id)
	               		{
	               			$status = true;
	               		
	               		}
	               		else
	               		{
	               			$status = false;
	               		}
	               }
	               if(!$status)
	               {
	               	    //Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$this, 'info'=>$data));
	               		$cart->addProduct($product,array('qty'=>1));
	               		$cart->save();
	                    //Mage::getSingleton('checkout/session')->setCartWasUpdated(true);	               			               		
	               }
	        }
	   else	   
	     {
	        $cart = Mage::getModel('checkout/cart');
	        $cart_items = $cart->getQuote()->getAllItems();
	        foreach($cart_items as $items)
	        {
	        	if($items->getProduct()->getId()==$id)
	        	{
	        		$cart->getQuote()->removeItem($id);
	                $cart->getQuote()->isDeleted(true);
	        		$cart->save();	     
	        	    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
	        	}
	        }	        	
	     }
    	}
    	catch(Exception $e)
    	{
    		echo $e;
    	}   
	        
    }   
    
   public function cart_delete($observer)
    
    {
    	//echo 'Hello';
    	//die();
     try
    {
    	$sku =  Mage::getStoreConfig('catalog/freeitem/product_sku');
    	$product = Mage::getModel('catalog/product');
	    $id = $product->getIdBySku($sku);
	    $product->setStoreId(Mage::app()->getStore()->getId());
	    $product->load($id);
	    $cart = Mage::getModel('checkout/cart');                
	    $cart_items = $cart->getQuote()->getAllItems();
	    foreach($cart_items as $items)
	    {
	       if($items->getProduct()->getId()==$id)	
	    //if($items->getQuote()->getItemById($id) == $product)
	       {
	    	$cart->getQuote()->removeItem($id);
	    	$cart->getQuote()->isDeleted(true);
	    	$cart->save();	    		
	       }
	    }
	    /*foreach($cart_items as $items)
	    {
	    	if($items->getProduct()->getId()==$id)
	    	{
	    		$cart->getQuote()->removeItem($id);
	    		$cart->save();	    		
	    		//Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
	    	}      		
	    }*/
    } 	
    	
     catch(Exception $e)
    	{
    		echo $e;
    	}
    	
    }
}