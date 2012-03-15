<?php

class Mec_Chinaprovicecity_Block_Adminhtml_Chinaprovicecity_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'chinaprovicecity';
        $this->_controller = 'adminhtml_chinaprovicecity';
        
        $this->_updateButton('save', 'label', Mage::helper('chinaprovicecity')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('chinaprovicecity')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('chinaprovicecity_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'chinaprovicecity_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'chinaprovicecity_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('chinaprovicecity_data') && Mage::registry('chinaprovicecity_data')->getId() ) {
            return Mage::helper('chinaprovicecity')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('chinaprovicecity_data')->getTitle()));
        } else {
            return Mage::helper('chinaprovicecity')->__('Add Item');
        }
    }
}