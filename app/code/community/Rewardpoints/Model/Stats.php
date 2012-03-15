<?php
/**
 * J2T RewardsPoint2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@j2t-design.com so we can send you a copy immediately.
 *
 * @category   Magento extension
 * @package    RewardsPoint2
 * @copyright  Copyright (c) 2009 J2T DESIGN. (http://www.j2t-design.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Rewardpoints_Model_Stats extends Mage_Core_Model_Abstract
{
    const TARGET_PER_ORDER     = 1;
    const TARGET_FREE   = 2;
    const APPLY_ALL_ORDERS  = '-1';

    const TYPE_POINTS_ADMIN  = '-1';
    const TYPE_POINTS_REGISTRATION  = '-2';

    protected $_targets;

    protected $_eventPrefix = 'rewardpoints_account';
    protected $_eventObject = 'stats';

    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/stats ');

        $this->_targets = array(
            self::TARGET_PER_ORDER     => Mage::helper('rewardpoints')->__('Related to Order ID'),
            self::TARGET_FREE   => Mage::helper('rewardpoints')->__('Not related to Order ID'),
        );

    }

    public function getTargetsArray()
    {
        return $this->_targets;
    }

    public function targetsToOptionArray()
    {
        return $this->_toOptionArray($this->_targets);
    }

    protected function _toOptionArray($array)
    {
        $res = array();
        foreach ($array as $value => $label) {
        	$res[] = array('value' => $value, 'label' => $label);
        }
        return $res;
    }
}

