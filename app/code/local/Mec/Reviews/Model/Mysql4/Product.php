<?php
class Mec_Reviews_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('reviews/product', 'id');
    }
    
    public function getNewOnly($storeId, $email, $ids)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('p' => $this->getMainTable()), 'product_id')
            ->where('p.store_id = ?',  $storeId)
            ->where('p.email = ?',  $email)
            ->where('p.product_id IN(?)', $ids);
            
        $oldIds = array();
        $rows = $this->_getReadAdapter()->fetchAll($select);    
        foreach ($rows as $row) 
            $oldIds[] = $row['product_id'];
            
        $ids = array_diff($ids, $oldIds);
        return $ids;
    }    
    
    public function bulkInsert($storeId, $email, $ids)
    {
        if (!$ids)
            return;
            
        $db = $this->_getWriteAdapter();    
        $insertSql = "INSERT INTO `{$this->getMainTable()}`  (`store_id`, `product_id`, `email`) VALUES ";
        
        // quoteInto does not accept several args, arrrgh!
        $line = $db->quoteInto('(?,', $storeId)
              . '?,'
              . $db->quoteInto('?),', $email);
              
        foreach ($ids as $id)
            $insertSql .= $db->quoteInto($line, $id);  
        
        $db->raw_query(substr($insertSql, 0, -1));
    }    
} ?>
