<?php

class Mec_Orderoptions_Block_Adminhtml_Orderoptions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'orderoptions';
        $this->_controller = 'adminhtml_orderoptions';
        
        $this->_updateButton('save', 'label', Mage::helper('orderoptions')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('orderoptions')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('orderoptions_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'orderoptions_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'orderoptions_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('orderoptions_data') && Mage::registry('orderoptions_data')->getId() ) {
            return Mage::helper('orderoptions')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('orderoptions_data')->getTitle()));
        } else {
            return Mage::helper('orderoptions')->__('Add Item');
        }
    }
}