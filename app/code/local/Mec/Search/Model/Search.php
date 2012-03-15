<?php

class Mec_Search_Model_Search extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('search/search');
    }
}