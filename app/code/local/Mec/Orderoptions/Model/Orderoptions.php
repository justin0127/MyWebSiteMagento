<?php

class Mec_Orderoptions_Model_Orderoptions extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('orderoptions/orderoptions');
    }
}