<?php

$installer = $this;

$installer->startSetup();

$installer->run("


CREATE TABLE IF NOT EXISTS `dealerinquiry` (
  `dealerid` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(25) NOT NULL,
  `lastname` varchar(25) NOT NULL,
  `company` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `ciry` varchar(25) NOT NULL,
  `state` varchar(25) NOT NULL,
  `zip` varchar(6) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `iscustcreated` enum('0','1') NOT NULL,
  `status` enum('0','1') NOT NULL,
  `createddt` datetime NOT NULL,
  `updateddt` datetime NOT NULL,
  PRIMARY KEY (`dealerid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;


");

$installer->endSetup(); 