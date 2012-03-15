<?php

class Mec_Search_Block_Adminhtml_Search_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('search_form', array('legend'=>Mage::helper('search')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('search')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('search')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('search')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('search')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('search')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('search')->__('Content'),
          'title'     => Mage::helper('search')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getSearchData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSearchData());
          Mage::getSingleton('adminhtml/session')->setSearchData(null);
      } elseif ( Mage::registry('search_data') ) {
          $form->setValues(Mage::registry('search_data')->getData());
      }
      return parent::_prepareForm();
  }
}