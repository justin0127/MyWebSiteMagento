<?php
class Mec_Search_Block_Search extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getSearch()     
     { 
        if (!$this->hasData('search')) {
            $this->setData('search', Mage::registry('search'));
        }
        return $this->getData('search');
        
    }
}