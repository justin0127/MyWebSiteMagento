<?php

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer->startSetup();
// Get ID of the entity model 'sales/order'.
$sql = 'SELECT entity_type_id FROM '.$this->getTable('eav_entity_type').' WHERE entity_type_code="order"';
$row = Mage::getSingleton('core/resource')
         ->getConnection('core_read')
	     ->fetchRow($sql);
// Create EAV-attribute for the order .
$c = array (
  'entity_type_id'  => $row['entity_type_id'],
  'attribute_code'  => 'shipping_time',
  'backend_type'    => 'varchar',     // MySQL-Datatype
  'frontend_input'  => '',    // Type of the HTML form element
  'is_global'       => '1',
  'is_visible'      => '0',
  'is_required'     => '0',
  'is_user_defined' => '0',
  'frontend_label'  => 'shipping_time',
);
$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($c['entity_type_id'], $c['attribute_code'])
          ->setStoreId(0)
          ->addData($c);
$attribute->save();

// Create EAV-attribute for the order .
$c = array (
  'entity_type_id'  => $row['entity_type_id'],
  'attribute_code'  => 'fapiao_title',
  'backend_type'    => 'varchar',     // MySQL-Datatype
  'frontend_input'  => '',    // Type of the HTML form element
  'is_global'       => '1',
  'is_visible'      => '0',
  'is_required'     => '0',
  'is_user_defined' => '0',
  'frontend_label'  => 'fapiao',
);
$attribute = new Mage_Eav_Model_Entity_Attribute();
$attribute->loadByCode($c['entity_type_id'], $c['attribute_code'])
          ->setStoreId(0)
          ->addData($c);
$attribute->save();


$flatOrderTable = $installer->getTable('sales_flat_order');

if ($flatOrderTable) {
    $installer->getConnection()->addColumn($flatOrderTable, 'shipping_time', 'varchar(100) DEFAULT NULL');
    $installer->getConnection()->addColumn($flatOrderTable, 'fapiao_title', 'varchar(100) DEFAULT NULL');
}

$installer->endSetup();
