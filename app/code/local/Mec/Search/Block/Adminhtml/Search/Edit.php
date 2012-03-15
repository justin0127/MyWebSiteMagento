<?php

class Mec_Search_Block_Adminhtml_Search_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'search';
        $this->_controller = 'adminhtml_search';
        
        $this->_updateButton('save', 'label', Mage::helper('search')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('search')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('search_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'search_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'search_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('search_data') && Mage::registry('search_data')->getId() ) {
            return Mage::helper('search')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('search_data')->getTitle()));
        } else {
            return Mage::helper('search')->__('Add Item');
        }
    }
}