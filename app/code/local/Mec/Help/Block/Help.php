<?php
class Mec_Help_Block_Help extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getHelp()     
     { 
        if (!$this->hasData('help')) {
            $this->setData('help', Mage::registry('help'));
        }
        return $this->getData('help');
        
    }
}