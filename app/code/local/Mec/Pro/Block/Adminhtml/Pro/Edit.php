<?php

class Mec_Pro_Block_Adminhtml_Pro_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'pro';
        $this->_controller = 'adminhtml_pro';
        
        $this->_updateButton('save', 'label', Mage::helper('pro')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('pro')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('pro_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'pro_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'pro_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('pro_data') && Mage::registry('pro_data')->getId() ) {
            return Mage::helper('pro')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('pro_data')->getTitle()));
        } else {
            return Mage::helper('pro')->__('Add Item');
        }
    }
}