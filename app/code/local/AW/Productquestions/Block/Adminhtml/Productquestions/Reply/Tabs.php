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

class AW_Productquestions_Block_Adminhtml_Productquestions_Reply_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('productquestions_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle($this->__('Question'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => $this->__('Details'),
          'title'     => $this->__('Details'),
          'content'   => $this->getLayout()->createBlock('productquestions/adminhtml_productquestions_reply_tab_form')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}
