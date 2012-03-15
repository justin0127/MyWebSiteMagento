<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Model_Mysql4_Urls_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('socialbookmarking/urls');
    }
}