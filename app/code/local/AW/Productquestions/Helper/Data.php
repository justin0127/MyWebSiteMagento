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

class AW_Productquestions_Helper_Data extends Mage_Core_Helper_Abstract
{

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - getCurrentProduct
    public function getCurrentProduct($inspectRegistry = false)
    {
        if($inspectRegistry)
        {
            $product = Mage::registry('product');
            if(!($product instanceof Mage_Catalog_Model_Product))
                $product = Mage::registry('current_product');

            if($product instanceof Mage_Catalog_Model_Product)
                return $product;
        }

        $productId = (int) Mage::app()->getRequest()->getParam('id');
        if(!$productId) return $this->__('No product ID');

        $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);

        if(!$product
        || !($product instanceof Mage_Catalog_Model_Product)
        ||  $productId != $product->getId()
        || !Mage::helper('catalog/product')->canShow($product)
        || !in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())
        )   return $this->__('No such product');

        return $product;
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  isModuleOutputDisabled
    public static function isModuleOutputDisabled()
    {
        return (bool) Mage::getStoreConfig('advanced/modules_disable_output/AW_Productquestions');
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  getPleaseRegisterMessage
    public function getPleaseRegisterMessage()
    {
        return Mage::getStoreConfig('productquestions/question_form/please_register');
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  getSummaryHtml
    public function getSummaryHtml()
    {
        return Mage::app()->getLayout()->createBlock('productquestions/summary')->toHtml();
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  checkIfGuestsAllowed
    public static function checkIfGuestsAllowed()
    {
        return      Mage::getStoreConfig('productquestions/question_form/guests_allowed')
                ||  Mage::getSingleton('customer/session')->isLoggedIn();
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - isAdvancedNewsletterInstalled
    public static function isAdvancedNewsletterInstalled()
    {
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        return in_array('AW_Advancednewsletter', $modules);
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  getAdvancedNewsletterVersion
    public static function getAdvancedNewsletterVersion()
    {
        if(!$anVersion = Mage::getConfig()->getModuleConfig('AW_Advancednewsletter')->version)
            return false;

        $parts = explode('.', $anVersion);
        while(count($parts) < 3) $parts[] = 0;
        $ver = 0;
        foreach($parts as $p) $ver = $ver*100 + $p;

        return $ver;
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  subscribeAdvancedNewsletterSegment
    public function subscribeAdvancedNewsletterSegment($email, $name, $segments)
    {
        if(!is_array($segments)) $segments = array($segments);

        $anVersion = self::getAdvancedNewsletterVersion();

        $anModel = Mage::getModel('advancednewsletter/subscriptions');

        if($anVersion < 10200) // 1.0 & 1.0.2
            foreach($segments as $segment)
                $anModel->subscribe( // public function subscribe($email, $firstname, $lastname, $segment)
                    $email,
                    '',     // $firstname
                    $name,  // $lastname
                    $segment);
        else  // 1.2.0 and above
            foreach($segments as $segment)
                $anModel->subscribe( // public function subscribe($email, $firstname, $lastname, $salutation, $phone, $segment)
                    $email,
                    '',     // $firstname
                    $name,  // $lastname
                    null,   // $salutation,
                    null,   // $phone,
                    $segment);
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - subscribeCustomer
    public function subscribeCustomer($email)
    {
        $subscriber = Mage::getModel('newsletter/subscriber');
        $session = Mage::getSingleton('core/session');

        try
        {
            $subscriber->subscribe($email);
            if($subscriber->getIsStatusChanged())
                $session->addSuccess($this->__('You have been subscribed to newsletters'));
        }
        catch (Exception $e) {
            $session->addException($e, $this->__('There was a problem with the newsletter subscription')
                            .($e instanceof Mage_Core_Exception) ? ': '.$e->getMessage() : '');
        }
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  parseURLsIntoLinks
    public static function parseURLsIntoLinks($text)
    {
        if(!Mage::getStoreConfig('productquestions/interface/parse_urls_into_links'))
            return nl2br(htmlentities($text, null, 'UTF-8'));

        $parts = preg_split('#\s((?:https?|ftp)://\S+)\s#', ' '.$text.' ', -1, PREG_SPLIT_DELIM_CAPTURE);
        $isHref = true;
        $res = '';
        foreach($parts as $part)
            $res .= ($isHref = !$isHref)
                    ? '<a href="'.$part.'">'.$part.'</a>'
                    : ' '.nl2br(htmlentities($part, null, 'UTF-8')).' ';
        return $res;
    }
}
