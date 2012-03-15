<?php

class Mec_Onepage_Block_Adminhtml_Onepage_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('onepage_form', array('legend'=>Mage::helper('onepage')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('onepage')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('onepage')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('onepage')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('onepage')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('onepage')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('onepage')->__('Content'),
          'title'     => Mage::helper('onepage')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getOnepageData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getOnepageData());
          Mage::getSingleton('adminhtml/session')->setOnepageData(null);
      } elseif ( Mage::registry('onepage_data') ) {
          $form->setValues(Mage::registry('onepage_data')->getData());
      }
      return parent::_prepareForm();
  }
}