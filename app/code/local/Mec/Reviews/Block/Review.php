<?php
class Mec_Reviews_Block_Review extends Mage_Core_Block_Template
{
    public function __construct() {
        parent::__construct();
    }
    
    protected function _prepareData(){
        $items = array();
        
        $size = Mage::getStoreConfig('catalog/reviews2/count');
        if ($size > 0){
            $collection = Mage::getResourceModel('reviews/collection')
                ->addVisiblityFilter()
                ->setPageSize($size);
                
            $minRating = Mage::getStoreConfig('catalog/reviews2/min_rating');
            if ($minRating > 0 || Mage::getStoreConfig('catalog/reviews2/show_rating'))
                $collection->addRatingFilter($minRating); 
                
                
            $cat  = Mage::registry('current_category');
            $prod = Mage::registry('product');
            
            if (Mage::getStoreConfig('catalog/reviews2/from_product') && 
                    $prod instanceof Mage_Catalog_Model_Product){
                $collection->addEntityFilter($prod->getId());
            }
            elseif (Mage::getStoreConfig('catalog/reviews2/from_category') && 
                    $cat instanceof Mage_Catalog_Model_Category){
                $collection->addCategoryFilter($cat);
            }

            
            $session = Mage::getSingleton('reviews/session');
            $viewed = $session->getViewedReviews();
            if (!is_array($viewed)){
                $viewed = array();    
            }
            if (Mage::getStoreConfig('catalog/reviews2/exclude_viewed')){
                $collection->excludeAlreadyViewed($viewed);
            }
            
            if (Mage::getStoreConfig('catalog/reviews2/is_random'))   
                $collection->getSelect()->order('rand()');
            else 
                $collection->setDateOrder();

            
            $collection->load();
            
            if (Mage::getStoreConfig('catalog/reviews2/show_rating')){
                if (Mage::getStoreConfig('catalog/reviews2/show_summary_rating')) {
                    foreach ($collection->getItems() as $item ) {
                        $vote = new Varien_Object();
                        $vote->setPercent($item->getAvRating());
                        $vote->setRatingCode(Mage::helper('reviews')->__('Rating'));
                        $item->setRatingVotes(array($vote));
                    }                    
                } 
                else {
                    $collection->addRateVotes();
                }
            }
            
            $baseUrl = Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
            foreach ($collection as $product){
                $url = Mage::getUrl('catalog/product/view', array('id'=>$product->getId()));
                if ($product->getUrl())
                    $url = $baseUrl . $product->getUrl();
                $product->setUrl($url);
                
                $product->setDetail($this->trim($product->getDetail()));
                $items[] = $product;
                
                $viewed[] = $product->getReviewId();
            }
            
            if (Mage::getStoreConfig('catalog/reviews2/exclude_viewed')){
               $session->setViewedReviews(array_values(array_unique($viewed)));
               if (!$items)
                    $session->setViewedReviews(array());
            }
        }  
        
        $this->assign('items', $items);
    }
    
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    } 
    
    protected function trim($str){
        $max = Mage::getStoreConfig('catalog/reviews2/words');
        if ($max > 0)
            $str = implode(" ", array_slice(preg_split('/\s+/', $str), 0, $max)); 
            
        return $str;
    }
    
} 
