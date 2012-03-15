<?php
/**
 * Magento
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
class Rewardpoints_Block_Points extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('referafriend/points.phtml');
        $points = Mage::getResourceModel('rewardpoints/stats_collection')
            ->addClientFilter(Mage::getSingleton('customer/session')->getCustomer()->getId());
        $this->setPoints($points);
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'rewardpoints.points')
            ->setCollection($this->getPoints());
        $this->setChild('pager', $pager);
        $this->getPoints()->load();

        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}