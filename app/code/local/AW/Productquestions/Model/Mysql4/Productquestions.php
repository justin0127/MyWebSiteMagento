<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Productquestions
 * @copyright  Copyright (c) 2008-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

class AW_Productquestions_Model_Mysql4_Productquestions extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('productquestions/productquestions', 'question_id');
    }

    // Updates product title for product id
    public function setProductTitleById($productId, $title, $storeId=null)
    {
        $db = $this->_getWriteAdapter();

        $prop = array(
            'question_product_name' => $title
        );

        if(!is_null($storeId)){
            $db->update($this->getMainTable(), $prop, $db->quoteInto('question_product_id=? AND question_store_id=?', $productId, $storeId));
        }
        else
        {
            $db->update($this->getMainTable(), $prop, $db->quoteInto('question_product_id=?', $productId));
        }
    }

    // deletes by product id
    public function deleteByProductId($productId, $storeId=null)
    {
        $db = $this->_getWriteAdapter();

        if(!is_null($storeId))
        {
            $db->delete($this->getMainTable(),  $db->quoteInto('question_product_id=? AND question_store_id=?', $productId, $storeId));
        }
        else
        {
            $db->delete($this->getMainTable(),  $db->quoteInto('question_product_id=?', $productId));
        }
    }

    public function vote($questionId, $value)
    {
        $db = $this->_getWriteAdapter();
        $tableName = $this->getTable('productquestions/helpfulness');

        $voted = $db->fetchOne(
                        $db->select()
                            ->from($tableName, new Zend_Db_Expr('COUNT(*)'))
                            ->where('question_id=?', $questionId)
                    );

        if($voted)
            $db->query('UPDATE '.$tableName.' SET vote_count=vote_count+1, vote_sum=vote_sum+'.($value ? 1 : 0).' WHERE question_id='.$db->quote($questionId));
        else
            $db->query('INSERT INTO '.$tableName.' SET question_id='.$questionId.', vote_count=1, vote_sum='.($value ? 1 : 0));
    }
}
