<?php
class Mec_Pro_Block_Pro extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPro()     
     { 
        if (!$this->hasData('pro')) {
            $this->setData('pro', Mage::registry('pro'));
        }
        return $this->getData('pro');
        
    }
}