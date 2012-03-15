<?php
/** http://www.magentix.fr **/

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('social_bookmarking')};
CREATE TABLE {$this->getTable('social_bookmarking')} (
  `bookmark_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '0',
  `image` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `target` tinyint(1) NOT NULL DEFAULT '1',
  `position` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bookmark_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('social_bookmarking')} (`bookmark_id`, `name`, `image`, `url`, `target`, `position`, `status`) VALUES
(1, 'Twitter', '', 'http://twitter.com/home/?status=<title> : <bitly>', 1, 1, 1),
(2, 'Facebook', '', 'http://www.facebook.com/share.php?u=<url>', 1, 2, 1),
(3, 'Digg', '', 'http://digg.com/submit?phase=2&url=<url>', 1, 5, 1),
(4, 'Technorati', '', 'http://www.technorati.com/faves?add=<url>', 1, 6, 1),
(5, 'MySpace', '', 'http://www.myspace.com/Modules/PostTo/Pages/?u=<url>', 1, 4, 1),
(6, 'Del.icio.us', '', 'http://del.icio.us/post?url=<url>', 1, 7, 1),
(7, 'Linkdin', '', 'http://www.linkedin.com/shareArticle?mini=true&url=<url>&title=<title>', 1, 3, 1),
(8, 'Live', '', 'https://favorites.live.com/quickadd.aspx?marklet=1&url=<url>&title=<title>', 1, 8, 1),
(9, 'Reddit', '', 'http://reddit.com/submit?url=<url>&title=<title>', 1, 9, 1),
(10, 'StumbleUpon', '', 'http://www.stumbleupon.com/submit?url=<url>&title=<title>', 1, 10, 1);"

);

$installer->endSetup();