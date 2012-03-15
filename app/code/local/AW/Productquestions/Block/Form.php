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

class AW_Productquestions_Block_Form extends Mage_Core_Block_Template
{
    protected $_hasParent = false;

    public function __construct()
    {
        parent::__construct();

        //add antispam code value
        if(!$this->getAntiSpamCode())
        {
            $antiSpamCode = rand();
            $this->setAntiSpamCode($antiSpamCode);
            Mage::getSingleton('core/session')->setAWProductQuestionsAntiSpamCode(md5($antiSpamCode));
        }
        $this->setTemplate('productquestions/form.phtml');
    }

    protected function _prepareLayout()
    {
        $this->_hasParent = (bool) $this->getLayout()->getBlock('productquestions');

        if(!$this->_hasParent)
            $this->getLayout()->getBlock('head')->addCss('css/productquestions.css');

        return parent::_prepareLayout();
    }

    public function getAction()
    {
        $productId = Mage::app()->getRequest()->getParam('id', false);
        return Mage::getUrl('productquestions/index/post', array('id' => $productId));
    }


    protected function _toHtml()
    {
        $storeId = Mage::app()->getStore()->getId();

        if(AW_Productquestions_Helper_Data::isModuleOutputDisabled($storeId)) return '';
        if(!AW_Productquestions_Helper_Data::checkIfGuestsAllowed($storeId))
            return Mage::helper('productquestions')->getPleaseRegisterMessage($storeId);

        if( !$this->getQuestionAuthorName()
        ||  !$this->getQuestionAuthorEmail()
        ) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if($customer && $customer->getId())
            {
                if(!$this->getQuestionAuthorName())     // add logged in customer name as nickname
                    $this->setQuestionAuthorName($customer->getName());

                if(!$this->getQuestionAuthorEmail())    // add logged in customer email
                    $this->setQuestionAuthorEmail($customer->getEmail());
            }
        }

        $product = Mage::helper('productquestions')->getCurrentProduct();
        if(!($product instanceof Mage_Catalog_Model_Product)) return '';

        $this->setProduct($product);

        $data = Mage::getSingleton('core/session')->getProductquestionsData(true);

        if(is_array($data))
            $this->setData(array_merge($this->getData(), $data));

        return parent::_toHtml();
    }

}
