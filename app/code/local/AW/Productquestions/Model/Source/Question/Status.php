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

class AW_Productquestions_Model_Source_Question_Status
{
    const STATUS_PUBLIC     = 1;
    const STATUS_PRIVATE    = 2;

    public static function toShortOptionArray()
    {
        return array(
            self::STATUS_PUBLIC    => Mage::helper('productquestions')->__('Public'),
            self::STATUS_PRIVATE   => Mage::helper('productquestions')->__('Private')
        );
    }

    public static function toOptionArray()
    {
        $res = array();

        foreach(self::toShortOptionArray() as $key => $value)
            $res[] = array(
                        'value' => $key,
                        'label' => $value);

        return $res;
    }
}
