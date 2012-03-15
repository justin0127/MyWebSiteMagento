<?php
class Mec_Orderoptions_Block_Orderoptions extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getOrderoptions()     
     { 
        if (!$this->hasData('orderoptions')) {
            $this->setData('orderoptions', Mage::registry('orderoptions'));
        }
        return $this->getData('orderoptions');
        
    }
}