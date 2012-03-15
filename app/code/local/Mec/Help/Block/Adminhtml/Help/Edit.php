<?php

class Mec_Help_Block_Adminhtml_Help_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'help';
        $this->_controller = 'adminhtml_help';
        
        $this->_updateButton('save', 'label', Mage::helper('help')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('help')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('help_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'help_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'help_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('help_data') && Mage::registry('help_data')->getId() ) {
            return Mage::helper('help')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('help_data')->getTitle()));
        } else {
            return Mage::helper('help')->__('Add Item');
        }
    }
}
