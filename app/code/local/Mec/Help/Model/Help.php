<?php

class Mec_Help_Model_Help extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('help/help');
    }
}