<?php
/**
 * @author Adjustware
 */ 
class Mec_Reviews_Model_Product extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('reviews/product');
    }
    
    /**
     * Checks if customer reseive emails for given products
     *
     * @param int $storeId
     * @param string $email
     * @param array $ids product ids to check
     * @return array of not used in previous reminders product ids
     */
    public function getNewOnly($storeId, $email, $ids)
    {
        return $this->_getResource()->getNewOnly($storeId, $email, $ids);
    } 
    
    
    public function bulkInsert($storeId, $email, $ids)
    {
        return $this->_getResource()->bulkInsert($storeId, $email, $ids);
    } 
} ?>
