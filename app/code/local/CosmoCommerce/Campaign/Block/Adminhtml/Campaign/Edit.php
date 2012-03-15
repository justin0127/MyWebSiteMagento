<?php

class CosmoCommerce_Campaign_Block_Adminhtml_Campaign_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'campaign';
        $this->_controller = 'adminhtml_campaign';
        
        $this->_updateButton('save', 'label', Mage::helper('campaign')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('campaign')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('campaign_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'campaign_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'campaign_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('campaign_data') && Mage::registry('campaign_data')->getId() ) {
            return Mage::helper('campaign')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('campaign_data')->getTitle()));
        } else {
            return Mage::helper('campaign')->__('Add Item');
        }
    }
}