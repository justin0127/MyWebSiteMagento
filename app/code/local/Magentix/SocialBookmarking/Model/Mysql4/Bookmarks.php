<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Model_Mysql4_Bookmarks extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {
        $this->_init('socialbookmarking/bookmarks', 'bookmark_id');
    }
}