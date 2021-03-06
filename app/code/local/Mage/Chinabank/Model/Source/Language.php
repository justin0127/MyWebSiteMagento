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
 * @name       Chinabank_Model_Source_Language
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Chinabank_Model_Source_Language
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'EN', 'label' => Mage::helper('chinabank')->__('English')),
            array('value' => 'FR', 'label' => Mage::helper('chinabank')->__('French')),
            array('value' => 'DE', 'label' => Mage::helper('chinabank')->__('German')),
            array('value' => 'IT', 'label' => Mage::helper('chinabank')->__('Italian')),
            array('value' => 'ES', 'label' => Mage::helper('chinabank')->__('Spain')),
            array('value' => 'NL', 'label' => Mage::helper('chinabank')->__('Dutch')),
        );
    }
}



