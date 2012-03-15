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

class AW_Productquestions_Block_Summary extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('productquestions/summary.phtml');
    }

    protected function _toHtml()
    {
        if(AW_Productquestions_Helper_Data::isModuleOutputDisabled()) return '';

        $product = Mage::helper('productquestions')->getCurrentProduct(true);
        if(!($product instanceof Mage_Catalog_Model_Product)) return '';

        $productId = $product->getId();

        $category = Mage::registry('current_category');
        if($category instanceof Mage_Catalog_Model_Category)
            $categoryId = $category->getId();
        else $categoryId = false;

        $questionCount = Mage::getResourceModel('productquestions/productquestions_collection')
            ->addProductFilter($productId)
            ->addVisibilityFilter()
            ->addAnsweredFilter()
            ->addStoreFilter()
            ->getSize();

        $params = array('id' => $productId);
        if($categoryId) $params['category'] = $categoryId;

        if($urlKey = $product->getUrlKey())
        {
            $requestString = ltrim(Mage::app()->getFrontController()->getRequest()->getRequestString(), '/');
            $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
            $pqSuffix = $urlKey.$suffix;
            if($pqSuffix == substr($requestString, strlen($requestString)-strlen($pqSuffix)))
            {
                $requestString = substr($requestString, 0, strlen($requestString)-strlen($suffix));
                $this->setQuestionsPageUrl($this->getBaseUrl().$requestString.AW_Productquestions_Model_Urlrewrite::SEO_SUFFIX.$suffix);
            }
        }
        if(!$this->getQuestionsPageUrl())
            $this->setQuestionsPageUrl(Mage::getUrl('productquestions/index/index/', $params));

        $this->setQuestionCount($questionCount);

        return parent::_toHtml();
    }
}
