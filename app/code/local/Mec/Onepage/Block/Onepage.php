<?php
class Mec_Onepage_Block_Onepage extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getOnepage()     
     { 
        if (!$this->hasData('onepage')) {
            $this->setData('onepage', Mage::registry('onepage'));
        }
        return $this->getData('onepage');
        
    }
}