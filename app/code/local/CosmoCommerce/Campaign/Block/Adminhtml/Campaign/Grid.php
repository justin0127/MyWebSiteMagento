<?php

class CosmoCommerce_Campaign_Block_Adminhtml_Campaign_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('campaignGrid');
      $this->setDefaultSort('campaign_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('campaign/campaign')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('campaign_id', array(
          'header'    => Mage::helper('campaign')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'campaign_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('campaign')->__('Email'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('content', array(
			'header'    => Mage::helper('campaign')->__('来源'),
			'width'     => '150px',
			'index'     => 'content',
      ));
      $this->addColumn('created_time', array(
			'header'    => Mage::helper('campaign')->__('创建时间'),
			'width'     => '150px',
			'index'     => 'created_time',
      ));
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('campaign')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('campaign')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('campaign')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('campaign')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('campaign_id');
        $this->getMassactionBlock()->setFormFieldName('campaign');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('campaign')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('campaign')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('campaign/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('campaign')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('campaign')->__('Status'),
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