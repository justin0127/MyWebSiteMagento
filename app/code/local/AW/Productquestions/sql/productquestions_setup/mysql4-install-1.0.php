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

DROP TABLE IF EXISTS {$this->getTable('productquestions')};
CREATE TABLE {$this->getTable('productquestions')} (
  `question_id` int(10) unsigned NOT NULL auto_increment,
  `question_status` TINYINT( 2 ) NOT NULL DEFAULT '1',
  `question_product_id` int(10) unsigned NOT NULL default '0',
  `question_author_name` varchar(255) NOT NULL default '',
  `question_author_email` varchar(255) NOT NULL default '',
  `question_date` datetime NOT NULL default '0000-00-00 00:00:00', 
  `question_text` TEXT NOT NULL default '',
  `question_reply_text` TEXT NOT NULL default '',
  PRIMARY KEY  (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('productquestions')} ADD INDEX ( `question_status` ) ;

");

$installer->endSetup();
?>
