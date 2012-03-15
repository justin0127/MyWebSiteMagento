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

class AW_Productquestions_Model_Mysql4_Productquestions_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_sortingFields = array(
        AW_Productquestions_Model_Source_Question_Sorting::BY_DATE => 'main_table.question_date',
        AW_Productquestions_Model_Source_Question_Sorting::BY_HELPFULLNESS => 'helpfulness',
    );

    public function _construct()
    {
        parent::_construct();
        $this->_init('productquestions/productquestions');
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->joinLeft(array('h' => $this->getTable('productquestions/helpfulness')),
                        'h.question_id=main_table.question_id',
                        array('vote_count', 'vote_sum',
                            'helpfulness' => new Zend_Db_Expr('vote_sum/if(vote_count=0,1,vote_count)*100')));

        return $this;
    }

    // Covers original bug in Varien_Data_Collection_Db
    public function getSelectCountSql(){
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);

        $countSelect->from('', 'COUNT(*)');
        return $countSelect;
    }

    // Covers original bug in Mage_Core_Model_Mysql4_Collection_Abstract
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->reset(Zend_Db_Select::HAVING);
        $idsSelect->from(null, 'main_table.'.$this->getResource()->getIdFieldName());
        return $this->getConnection()->fetchCol($idsSelect);
    }

    public function getAllIdsFiltered()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->from(null, 'main_table.'.$this->getResource()->getIdFieldName());
        return $this->getConnection()->fetchCol($idsSelect);
    }

    public function addProductFilter($pId)
    {
        $this->getSelect()->where('main_table.question_product_id=?', $pId);
        return $this;
    }

    public function addStoreFilter($storeId = null)
    {
        if(is_null($storeId))
        {
            if(Mage::app()->isSingleStoreMode()) return $this;

            $storeId = Mage::app()->getStore()->getId();
        }
        $this->getSelect()->where('find_in_set(0, question_store_ids) OR find_in_set(?, question_store_ids)', (int)$storeId);
        return $this;
    }

    public function addVisibilityFilter($vis = AW_Productquestions_Model_Source_Question_Status::STATUS_PUBLIC)
    {
        $this->getSelect()->where('main_table.question_status=?', $vis);
        return $this;
    }

    public function addLastXFilter($lastX = false)
    {
        if($lastX)
            $this
                ->setPageSize($lastX)
                // ->setCurPage(0)
                ->getSelect()->limit($lastX);
        return $this;
    }

    public function addAnsweredFilter($answered = true)
    {
        if($answered)
            $this->getSelect()->where('main_table.question_reply_text!=?', '');
        else 
            $this->getSelect()->where('main_table.question_reply_text=?', '');

        return $this;
    }

    public function applySorting($sortOrder, $sortDir)
    {
        if( $sortOrder
        &&  array_key_exists($sortOrder, $this->_sortingFields)
        )   $this->getSelect()
                ->order($this->_sortingFields[$sortOrder].' '.$sortDir);
        return $this;
    }
}
