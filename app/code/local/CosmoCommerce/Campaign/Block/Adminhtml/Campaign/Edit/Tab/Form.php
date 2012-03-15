<?php

class CosmoCommerce_Campaign_Block_Adminhtml_Campaign_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('campaign_form', array('legend'=>Mage::helper('campaign')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('campaign')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('campaign')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('campaign')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('campaign')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('campaign')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('campaign')->__('Content'),
          'title'     => Mage::helper('campaign')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCampaignData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCampaignData());
          Mage::getSingleton('adminhtml/session')->setCampaignData(null);
      } elseif ( Mage::registry('campaign_data') ) {
          $form->setValues(Mage::registry('campaign_data')->getData());
      }
      return parent::_prepareForm();
  }
}