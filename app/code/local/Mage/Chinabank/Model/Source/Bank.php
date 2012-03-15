<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Chinabank
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Alipay Allowed languages Resource
 *
 * @category   Mage
 * @package    Chinabank
 * @name       Chinabank_Model_Source_Bank
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Chinabank_Model_Source_Bank
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'mutuel', 'label' => Mage::helper('chinabank')->__('Credit Mutuel')),
            array('value' => 'cic', 'label' => Mage::helper('chinabank')->__('Groupe CIC')),
            array('value' => 'obc', 'label' => Mage::helper('chinabank')->__('OBC')),
        );
    }
}



