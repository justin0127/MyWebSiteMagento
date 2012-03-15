<?php
class CosmoCommerce_Campaign_Block_Campaign extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCampaign()     
     { 
        if (!$this->hasData('campaign')) {
            $this->setData('campaign', Mage::registry('campaign'));
        }
        return $this->getData('campaign');
        
    }
}