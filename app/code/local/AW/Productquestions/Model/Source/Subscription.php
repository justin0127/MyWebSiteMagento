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

class AW_Productquestions_Model_Source_Subscription
{
    const NONE      = 1;
    const STANDARD  = 2;
    const USING_ANL = 3;

    public static function toOptionArray()
    {
        $helper = Mage::helper('productquestions');
        return array(
            self::NONE      => $helper->__('Don\'t display subscription'),
            self::STANDARD  => $helper->__('Standard newsletter'),
            self::USING_ANL => $helper->__('Advanced newsletter'),
        );
    }
}
