<?php

class Mec_Onepage_Model_Onepage extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('onepage/onepage');
    }
}