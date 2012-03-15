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

class AW_Productquestions_Model_Source_Question_Sorting
{
    const BY_DATE = 1;
    const BY_HELPFULLNESS = 2;

    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    public static function toOptionArray()
    {
        $res = array();

        foreach(self::toShortOptionArray() as $key => $value)
            $res[] = array('value' => $key, 'label' => $value);

        return $res;
    }

    public static function toShortOptionArray()
    {
        return array(
            self::BY_DATE           => Mage::helper('productquestions')->__('Date'),
            self::BY_HELPFULLNESS   => Mage::helper('productquestions')->__('Helpfulness'),
        );
    }

    public static function getSortDirDescription($dir)
    {
        $helper = Mage::helper('productquestions');
        switch($dir)
        {
            case self::SORT_ASC:  return $helper->__('Ascending'); break;
            case self::SORT_DESC: return $helper->__('Descending'); break;
        }
        return 'Unknown';
    }
}
