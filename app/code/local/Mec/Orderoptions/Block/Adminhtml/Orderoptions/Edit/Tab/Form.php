<?php

class Mec_Orderoptions_Block_Adminhtml_Orderoptions_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('orderoptions_form', array('legend'=>Mage::helper('orderoptions')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('orderoptions')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('orderoptions')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('orderoptions')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('orderoptions')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('orderoptions')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('orderoptions')->__('Content'),
          'title'     => Mage::helper('orderoptions')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getOrderoptionsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getOrderoptionsData());
          Mage::getSingleton('adminhtml/session')->setOrderoptionsData(null);
      } elseif ( Mage::registry('orderoptions_data') ) {
          $form->setValues(Mage::registry('orderoptions_data')->getData());
      }
      return parent::_prepareForm();
  }
}