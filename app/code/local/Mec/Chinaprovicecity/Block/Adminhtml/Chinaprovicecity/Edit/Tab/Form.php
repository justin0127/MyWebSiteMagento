<?php

class Mec_Chinaprovicecity_Block_Adminhtml_Chinaprovicecity_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('chinaprovicecity_form', array('legend'=>Mage::helper('chinaprovicecity')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('chinaprovicecity')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('chinaprovicecity')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('chinaprovicecity')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('chinaprovicecity')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('chinaprovicecity')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('chinaprovicecity')->__('Content'),
          'title'     => Mage::helper('chinaprovicecity')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getChinaprovicecityData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getChinaprovicecityData());
          Mage::getSingleton('adminhtml/session')->setChinaprovicecityData(null);
      } elseif ( Mage::registry('chinaprovicecity_data') ) {
          $form->setValues(Mage::registry('chinaprovicecity_data')->getData());
      }
      return parent::_prepareForm();
  }
}