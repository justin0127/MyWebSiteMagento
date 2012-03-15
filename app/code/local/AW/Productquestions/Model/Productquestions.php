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

class AW_Productquestions_Model_Productquestions extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productquestions/productquestions');
        $this->setIdFieldName('question_id');
    }

    public function getProductId()
    {
        return $this->getData('question_product_id');
    }

    public function getQuestionText()
    {
        return preg_replace('/<br[^>]*>/i', '', $this->_data['question_text']);
    }

    public function getQuestionReplyText()
    {
        return preg_replace('/<br[^>]*>/i', '', $this->_data['question_reply_text']);
    }

    public function validate()
    {
        $errors = array();
        $helper = Mage::helper('productquestions');

        if (!Zend_Validate::is($this->getQuestionAuthorEmail(), 'EmailAddress'))
            $errors[] = $helper->__('Please specify valid email address');

        if (!Zend_Validate::is($this->getQuestionAuthorName(), 'NotEmpty'))
            $errors[] = $helper->__('Nickname can\'t be empty');

        if (!Zend_Validate::is($this->getQuestionText(), 'NotEmpty'))
            $errors[] = $helper->__('Question text can\'t be empty');

        if( Mage::getSingleton('core/session')->getAWProductQuestionsAntiSpamCode()
            !== $this->getQuestionAntispamCode()
        )   $errors[] = $helper->__('Antispam code is invalid. Please, check if JavaScript is enabled in your browser settings.');

        if(empty($errors)) return true;
        else return $errors;
    }

    public function vote($value)
    {
        $this->getResource()->vote($this->getId(), $value);
        return $this;
    }

    public function getAdminUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl(
            'productquestions/adminhtml_index/reply', array('id' => $this->getQuestionId()));
    }
}
