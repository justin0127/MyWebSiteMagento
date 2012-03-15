<?php

class Mec_Pro_Model_Pro extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pro/pro');
    }
}