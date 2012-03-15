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

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('productquestions')}  DEFAULT CHARACTER SET utf8;

ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_author_name` `question_author_name` VARCHAR( 255 ) CHARACTER SET utf8 NOT NULL;
ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_text` `question_text` TEXT CHARACTER SET utf8 NOT NULL;
ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_reply_text` `question_reply_text` TEXT CHARACTER SET utf8 NOT NULL;
ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_product_name` `question_product_name` VARCHAR( 255 ) CHARACTER SET utf8 NOT NULL;

");

$installer->endSetup();
?>
