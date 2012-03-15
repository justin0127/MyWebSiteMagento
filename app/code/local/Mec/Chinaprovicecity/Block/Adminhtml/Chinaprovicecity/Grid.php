<?php

class Mec_Chinaprovicecity_Block_Adminhtml_Chinaprovicecity_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('chinaprovicecityGrid');
      $this->setDefaultSort('chinaprovicecity_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('chinaprovicecity/chinaprovicecity')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('chinaprovicecity_id', array(
          'header'    => Mage::helper('chinaprovicecity')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'chinaprovicecity_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('chinaprovicecity')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('chinaprovicecity')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('chinaprovicecity')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('chinaprovicecity')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('chinaprovicecity')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('chinaprovicecity')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('chinaprovicecity')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('chinaprovicecity_id');
        $this->getMassactionBlock()->setFormFieldName('chinaprovicecity');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('chinaprovicecity')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('chinaprovicecity')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('chinaprovicecity/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('chinaprovicecity')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('chinaprovicecity')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}