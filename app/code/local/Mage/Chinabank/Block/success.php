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
 * Redirect to Alipay
 *
 * @category    Mage
 * @package     Chinabank
 * @name        Chinabank_Block_Standard_Redirect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Chinabank_Block_Success extends Mage_Core_Block_Abstract
{

	protected function _toHtml()
	{
    $html = '<html><body><p>';
    $html.= $this->__('Success! five seconds after turn to home page.');
    $html.= '</p><script type="text/javascript">setTimeout(function(){ location.href = "'.Mage::getBaseurl().'"},5000);</script>';
    $html.= '</body></html>';
    echo $html;
    }
}