<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Block_Adminhtml_Bookmarks_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('socialbookmarking_form', array('legend'=>Mage::helper('socialbookmarking')->__('Bookmark information')));
		
		$fieldset->addField('name', 'text', array(
			'label'     => Mage::helper('socialbookmarking')->__('Title'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'name',
		));
		
		$fieldset->addField('url', 'text', array(
			'label'     => Mage::helper('socialbookmarking')->__('Url'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'url',
			'note'		=> '<strong>&lt;url&gt; :</strong> '.Mage::helper('socialbookmarking')->__('Current page URL').' <br /> <strong>&lt;title&gt; :</strong> '.Mage::helper('socialbookmarking')->__('Current page Meta Title').' <br /> <strong>&lt;bitly&gt; :</strong> '.Mage::helper('socialbookmarking')->__('Current page Short URL (http://bit.ly)').''
		));
		
		$fieldset->addField('image', 'image', array(
			'label'     => Mage::helper('socialbookmarking')->__('Image'),
			'required'  => false,
			'name'      => 'bookmarkimage',
		));
		
		$fieldset->addField('position', 'text', array(
			'label'     => Mage::helper('socialbookmarking')->__('Position'),
			'required'  => false,
			'name'      => 'position',
		));
		
		$fieldset->addField('target', 'select', array(
			'label'     => Mage::helper('socialbookmarking')->__('Open in new window'),
			'name'      => 'target',
			'values'	=> array(
				array(
					'value' => 1,
					'label'     => Mage::helper('socialbookmarking')->__('Yes'),
				),
				
				array(
					'value' => 2,
					'label'     => Mage::helper('socialbookmarking')->__('No'),
				),
			),
		));
		
		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('socialbookmarking')->__('Status'),
			'name'      => 'status',
			'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('socialbookmarking')->__('Enabled'),
			  ),
			
			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('socialbookmarking')->__('Disabled'),
			  ),
			),
		));
		
		if ( Mage::getSingleton('adminhtml/session')->getSocialBookmarkingData() ) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getSocialBookmarkingData());
			Mage::getSingleton('adminhtml/session')->setSocialBookmarkingData(null);
		} elseif ( Mage::registry('socialbookmarking_data') ) {
			$form->setValues(Mage::registry('socialbookmarking_data')->getData());
		}
		return parent::_prepareForm();
	}

}