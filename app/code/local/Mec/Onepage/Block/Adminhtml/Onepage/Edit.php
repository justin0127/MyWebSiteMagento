<?php

class Mec_Onepage_Block_Adminhtml_Onepage_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'onepage';
        $this->_controller = 'adminhtml_onepage';
        
        $this->_updateButton('save', 'label', Mage::helper('onepage')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('onepage')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('onepage_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'onepage_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'onepage_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('onepage_data') && Mage::registry('onepage_data')->getId() ) {
            return Mage::helper('onepage')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('onepage_data')->getTitle()));
        } else {
            return Mage::helper('onepage')->__('Add Item');
        }
    }
}