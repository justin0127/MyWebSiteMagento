<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Model_Mysql4_Urls extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {
        $this->_init('socialbookmarking/urls','id');
    }

    public function loadByUrl($urls,$url) {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('socialbookmarking/urls'))
            ->where('url=:current_url');

        if ($id = $this->_getReadAdapter()->fetchOne($select, array('current_url' => $url))) {
            $this->load($urls, $id);
        } else {
            $urls->setData(array());
        }
        return $this;
    }

}