<?php
class Mec_Chinaprovicecity_Block_Chinaprovicecity extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getChinaprovicecity()     
     { 
        if (!$this->hasData('chinaprovicecity')) {
            $this->setData('chinaprovicecity', Mage::registry('chinaprovicecity'));
        }
        return $this->getData('chinaprovicecity');
        
    }
}