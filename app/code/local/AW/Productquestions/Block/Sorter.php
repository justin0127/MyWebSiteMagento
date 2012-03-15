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

class AW_Productquestions_Block_Sorter extends Mage_Core_Block_Template
{
    protected $_orderVarName = 'orderby';
    protected $_dirVarName = 'dir';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('productquestions/sorter.phtml');
    }

    public static function getAllowedSorting()
    {
        $allowedSorting = Mage::getStoreConfig('productquestions/interface/allowed_sorting_type');
        $allowedSorting = $allowedSorting
                            ? explode(',', $allowedSorting)
                            : array();

        $res = array();
        if(empty($allowedSorting)) return $res;

        $allSortings = AW_Productquestions_Model_Source_Question_Sorting::toShortOptionArray();

        foreach($allowedSorting as $key)
            if(array_key_exists($key, $allSortings))
                $res[$key] = $allSortings[$key];

        return $res;
    }

    public function getCurrentSorting()
    {
        $allowedSorting = array_keys(self::getAllowedSorting());

        if(empty($allowedSorting)) return array(false, false);

        $sortOrder = $this->getRequest()->getParam($this->_orderVarName);
        if(!$sortOrder
        || !in_array($sortOrder, $allowedSorting)
        )   $sortOrder = reset($allowedSorting);

        $sortDir = $this->getRequest()->getParam($this->_dirVarName);
        if( AW_Productquestions_Model_Source_Question_Sorting::SORT_ASC != $sortDir
        &&  AW_Productquestions_Model_Source_Question_Sorting::SORT_DESC != $sortDir
        )   $sortDir = AW_Productquestions_Model_Source_Question_Sorting::SORT_ASC;

        return array($sortOrder, $sortDir);
    }

    public function getSortOrderUrl($sortOrder)
    {
        return $this->getSorterUrl(array($this->_orderVarName => $sortOrder));
    }

    public static function getInvertedDir($dir)
    {
        return (AW_Productquestions_Model_Source_Question_Sorting::SORT_ASC == $dir)
                ? AW_Productquestions_Model_Source_Question_Sorting::SORT_DESC
                : AW_Productquestions_Model_Source_Question_Sorting::SORT_ASC;
    }

    public function getSortDirUrl($dir)
    {
        return $this->getSorterUrl(array($this->_dirVarName => $dir));
    }

    public function getSorterUrl($params=array())
    {
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }
}
