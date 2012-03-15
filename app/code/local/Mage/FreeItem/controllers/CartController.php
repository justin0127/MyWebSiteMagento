<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Mage_FreeItem_CartController extends Mage_Checkout_CartController
{
	/** 
	 * Adds a free item to cart, on the basis of grand-total exceeding the free item amount
	 * Only if the customer is not a Sales Rep or a Reseller.
	 *
	 * @author Soham Sen
	 * @copyright InSync Tech-Fin Solutions Ltd.
	 */
	
	public function addAction()
	{
	                                       
					       $ProductList = str_replace(" ","",Mage::getStoreConfig('catalog/freeitem/product_sku'));
		         		       $ProductArr = explode(",", $ProductList);
					       $cart = Mage::getModel('checkout/cart');
					       $cart_items = $cart->getQuote()->getAllItems();     	     
					       $product = $this->_initProduct();
					       //var_dump($product->getSku());
					       //exit;
	                                       if(in_array($product->getSku(),$ProductArr)){
	                                       if($cart_items){	
					       foreach($cart_items as $item){
					       //var_dump($item->getSku());
					       $ProductSku = $item->getSku();
					       $ProductId = $item->getId();
						       	if(in_array($ProductSku,$ProductArr)) {
						       	$cart->getQuote()->removeItem($ProductId);        	      
							$cart->save();
							Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
						       	}
					       }
					       }
					       }
					       //exit;
	
		parent::addAction();
		try
		{
		  
		  $sku =  Mage::getStoreConfig('catalog/freeitem/product_sku');
		  if($sku)
		  {
		  	
		  $customer = Mage::getSingleton('customer/session')->getCustomer()->getData();			
			if(isset($customer['group_id']) && !empty($customer['group_id']))
			{			
				$customer_group = $customer['group_id'];
			}
			else
			{
				
		        
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
				               
				               //echo '123';
				               //exit;
				               
				               $product = $this->_initProduct();
				               if(in_array($product->getSku(),$ProductArr)){
				               		//$cart->addProduct($product,array('qty'=>1));
				               		$cart->save();
				               		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
				               		}	               		
				               }				               
			        }
		        }			    
			
			 //if($customer_group!=4 && $customer_group!=6 && $customer_group!=8 && $customer_group!=7) 
			 //{
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
			               //if($customer['entity_id']!=null)
			               //{
				               if(!$status && $customer_group!=4 && $customer_group!=6 && $customer_group!=8 && $customer_group!=7)
				               {
				               //echo '456';
				               //exit;
				               
				               $product = $this->_initProduct();
				               if(in_array($product->getSku(),$ProductArr)){
				               		//$cart->addProduct($product,array('qty'=>1));
				               		$cart->save();
				               		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
				               		}	               		
				               }
			              // }
			               
			        }
		  }
		 
		 }			  
		    
		  catch(Exception $e)
		  {
			echo $e;
		  }
	}
    
	/**
	 * Updates the cart, on the basis of grand-total exceeding/limiting within the free item amount
	 * @author Soham Sen
	 * @copyright Insync Tech-Fin Solutions Ltd.
	 */
	public function updatePostAction()
	{
		parent::updatePostAction();
		try 
		{
			$grand_total = Mage::getModel('checkout/cart')->getQuote()->getData('grand_total');
		    $ProductList = str_replace(" ","",Mage::getStoreConfig('catalog/freeitem/product_sku'));
		    $ProductArr = explode(",", $ProductList);
		    foreach($ProductArr as $key => $sku) {
		    //$sku =  Mage::getStoreConfig('catalog/freeitem/product_sku');
		    if($sku)
		    {
					$customer = Mage::getSingleton('customer/session')->getCustomer()->getData();
				    if(isset($customer['group_id']) && !empty($customer['group_id']))
					{			
						$customer_group = $customer['group_id'];
					}	
					else
					{		 	    
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
					                //$product = $this->_initProduct();
				                                if(in_array($product->getSku(),$ProductArr)){
					               		//$cart->addProduct($product,array('qty'=>1));
					               		$cart->save();
					               		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
					               		}	               		
					               }				               
				        }
				        else
						  {
						     $cart = Mage::getModel('checkout/cart');	       	     
						     $cart_items = $cart->getQuote()->getAllItems();
						     foreach($cart_items as $items)
						     {
						      if($items->getProduct()->getId() == $id)   
						      {
						       	  $itemId = $items->getItemId();
						          $cart->getQuote()->removeItem($itemId);        	      
						          $cart->save();
						       	  Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
						      }
						     }	            
						   }
			        
				}		
				
				     $grand_total = Mage::getModel('checkout/cart')->getQuote()->getData('grand_total');
					 $total_purchase = Mage::getStoreConfig('catalog/freeitem/total_purchase');
					 $sku =  Mage::getStoreConfig('catalog/freeitem/product_sku');
					 
			    	 $product = Mage::getModel('catalog/product');
				     $id = $product->getIdBySku($sku);
				     $product->setStoreId(Mage::app()->getStore()->getId());
				     $product->load($id);
		        
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
												break;
						               		}
						               		else
						               		{
						               			$status = false;
						               		}
						               }
						               
						              //if($customer['entity_id']!=null)
				                     //{
						               if(!$status && $customer_group!=4 && $customer_group!=6 && $customer_group!=8 && $customer_group!=7)
						               {
						               	    //Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$this, 'info'=>$data));
						               	    // $product = $this->_initProduct();
				               if(in_array($product->getSku(),$ProductArr)){
						               		//$cart->addProduct($product,array('qty'=>1));
						               		$cart->save();
						                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);	
						                    }               			               		
						               }
				                    //}
						        }
				               else
						       {
						       	    $cart = Mage::getModel('checkout/cart');	       	     
						       	    $cart_items = $cart->getQuote()->getAllItems();
						          foreach($cart_items as $items)
						          {
						        	 if($items->getProduct()->getId() == $id)   
						        	 {
						        	 	  $itemId = $items->getItemId();
						        	      $cart->getQuote()->removeItem($itemId);        	      
						        	      $cart->save();
						        		  Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
						        	 }
						          }	            
						        }
		      }
		      }			 
			}	      
		catch (Exception $e)
		{
			echo $e;
		}
	}	
	/**
	 * Removes the free item from cart, on deleting a product, if the grand-total of the cart
	 * falls below the free item amount
	 * @author Soham Sen
	 * @copyright Insync Tech-Fin Solutions Ltd. 
	 */
	public function deleteAction()
	{
		parent::deleteAction();
		try 
		{           
		            $ProductList = str_replace(" ","",Mage::getStoreConfig('catalog/freeitem/product_sku'));
		            $ProductArr = explode(",", $ProductList);
		            //var_dump($ProductArr);
		            //exit;
		            if($ProductArr) {
			    //$sku =  Mage::getStoreConfig('catalog/freeitem/product_sku');
			    foreach($ProductArr as $key => $sku) {
			    //var_dump($sku);
			    //exit;
			    if($sku)
			    {
							$grand_total = Mage::getModel('checkout/cart')->getQuote()->getData('grand_total');
							$total_purchase = Mage::getStoreConfig('catalog/freeitem/total_purchase');
						    
					        $product = Mage::getModel('catalog/product');
					        $id = $product->getIdBySku($sku);
					        $product->setStoreId(Mage::app()->getStore()->getId());
					        $product->load($id);
					        $cart = Mage::getModel('checkout/cart');                
					        $cart_items = $cart->getQuote()->getAllItems();
						    foreach($cart_items as $items)
						    {
						       if($items->getProduct()->getId()==$id && $grand_total<$total_purchase)	
						       {	   
						       	$itemId = $items->getItemId();     
						       	$cart->getQuote()->removeItem($itemId);
						    	$cart->save();
						    	Mage::getSingleton('checkout/session')->setCartWasUpdated(true);	       		    		
						       }
						    }
			    }	
			    }
			    }  
		}
		catch (Exception $e)
		{
			echo $e;
		}
	}
}
?>
