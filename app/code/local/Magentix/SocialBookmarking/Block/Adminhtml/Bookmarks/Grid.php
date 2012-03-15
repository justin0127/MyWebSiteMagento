<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Block_Adminhtml_Bookmarks_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		parent::__construct();
		$this->setId('socialbookmarkingGrid');
		$this->setDefaultSort('position');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _prepareCollection() {
		$collection = Mage::getModel('socialbookmarking/bookmarks')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this->addColumn('bookmark_id', array(
			'header'    => Mage::helper('socialbookmarking')->__('ID'),
			'align'     =>'left',
			'width'     => '25px',
			'filter'    => false,
			'sortable'  => false,
			'index'     => 'bookmark_id',
		));
		
		$this->addColumn('image', array(
			'header'    => Mage::helper('socialbookmarking')->__('Image'),
			'align'     =>'center',
			'width'     => '50px',
			'filter'    => false,
			'sortable'  => false,
			'renderer'  => 'socialbookmarking/adminhtml_widget_grid_renderer',
		));

		$this->addColumn('name', array(
			'header'    => Mage::helper('socialbookmarking')->__('Title'),
			'align'     =>'left',
			'index'     => 'name',
		));
	  
		$this->addColumn('url', array(
			'header'    => Mage::helper('socialbookmarking')->__('Url'),
			'align'     =>'left',
			'index'     => 'url',
		));
		
		$this->addColumn('position', array(
			'header'    => Mage::helper('socialbookmarking')->__('Position'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'position',
		));

		$this->addColumn('status', array(
			'header'    => Mage::helper('socialbookmarking')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
			  1 => Mage::helper('socialbookmarking')->__('Enabled'),
			  2 => Mage::helper('socialbookmarking')->__('Disabled'),
			),
		));
	  
		$this->addColumn('action',
		array(
			'header'    =>  Mage::helper('socialbookmarking')->__('Action'),
			'width'     => '100',
			'type'      => 'action',
			'getter'    => 'getId',
			'actions'   => array(
				array(
					'caption'   => Mage::helper('socialbookmarking')->__('Edit'),
					'url'       => array('base'=> '*/*/edit'),
					'field'     => 'id'
				)
			),
			'filter'    => false,
			'sortable'  => false,
			'index'     => 'stores',
			'is_system' => true,
		));
		
		return parent::_prepareColumns();
	}

    protected function _prepareMassaction() {
	
		$this->setMassactionIdField('bookmark_id');
		$this->getMassactionBlock()->setFormFieldName('socialbookmarking');
		
		$this->getMassactionBlock()->addItem('delete', array(
			'label'    => Mage::helper('socialbookmarking')->__('Delete'),
			'url'      => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('socialbookmarking')->__('Are you sure?')
		));
		
		$statuses = Mage::getSingleton('socialbookmarking/status')->getOptionArray();
		
		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('socialbookmarking')->__('Change status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name' => 'status',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => Mage::helper('socialbookmarking')->__('Status'),
					'values' => $statuses
				 )
			)
		));
		return $this;
    }

	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}