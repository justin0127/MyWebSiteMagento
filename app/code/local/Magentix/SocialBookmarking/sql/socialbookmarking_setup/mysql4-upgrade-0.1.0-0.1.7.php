<?php
/** http://www.magentix.fr **/

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('social_bookmarking_urls')};
CREATE TABLE IF NOT EXISTS {$this->getTable('social_bookmarking_urls')} (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '',
  `bitly` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$installer->endSetup();