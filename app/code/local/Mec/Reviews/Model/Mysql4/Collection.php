<?php
class Mec_Reviews_Model_Mysql4_Collection extends Mage_Review_Model_Mysql4_Review_Product_Collection
{
    public function addVisiblityFilter()
    {
        $store = Mage::app()->getStore();
        
        $this->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
            ->addStoreFilter($store->getId()) 
            ->addAttributeToSelect(array('name','visibility'), 'inner')
            ->addAttributeToSelect('thumbnail');
           // ->setStoreFilter(Mage::app()->getStore()->getId());
            
        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this);   
        
        $urCondions = array(
            'e.entity_id=ur.product_id',
            'ur.category_id IS NULL',
            'ur.store_id='.intVal($store->getId()),
            'ur.is_system=1'
        );
        
        $this->getSelect()->joinLeft(
            array('ur' => $this->getTable('core/url_rewrite')),
            join(' AND ', $urCondions),
            array('url' => 'request_path')
        );        
         
        return $this;
    }
    
    public function addRatingFilter($minRating)
    {
        $ratingTable = Mage::getSingleton('core/resource')->getTableName('rating/rating_option_vote');
        $this->getSelect()
            ->joinLeft(array('rat' => $ratingTable),
                'rat.review_id = rt.review_id',
                array('av_rating' => new Zend_Db_Expr('AVG(rat.percent)')))
            ->group('rt.review_id');
            
        if ($minRating)
        {
            $minRating = abs(floatVal($minRating));
            //somethimes value field is empty (bug ?), so its safer to use percent
            $minRating = ceil($minRating/5 *100);
            
            $this->getSelect()->having(new Zend_Db_Expr('AVG(rat.percent) > ' . $minRating) );
        }

        return $this;        
    }
        
    public function excludeAlreadyViewed($viewed) {
        if ($viewed){
            $this->getSelect()->where('rt.review_id NOT IN(?)', $viewed);
        }
        return $this;
    }
    
    public function addRateVotes() {
        foreach ($this->getItems() as $item ) {
            $votesCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setReviewFilter($item->getReviewId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->addRatingInfo(Mage::app()->getStore()->getId())
                ->load();
            
            $item->setRatingVotes( $votesCollection );
        }

        return $this;
    }
    
        
} 
