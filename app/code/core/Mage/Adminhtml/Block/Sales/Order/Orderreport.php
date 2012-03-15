<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Adminhtml sales orders block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Orderreport extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_items');
        $this->setUseAjax();
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */

    protected function _prepareCollection()
    {
	
	
//$orderTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
//$collection= Mage::getResourceModel('sales/order_item_collection')
//    ->join('order','order_id = order.entity_id');	
	
        $collection = Mage::getResourceModel("sales/order_item_collection");
		
		//$collection->AddFieldToFilter('main_table.parent_item_id',array('neq'=>'NULL'));

 
		$collection->AddFieldToFilter('main_table.parent_item_id',array('null'=>true));
		
		//$collection->AddFieldToFilter('main_table.product_type',array('neq'=>'configurable'));

		
        //$collection->getSelect()->joinLeft(array('product'=>'catalog_product_entity'),'main_table.sku = product.sku',array('item.price'=>'price','item.row_total'=>'row_total'));
					
		
        //$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
		//$collection->join($this->getTable('sales/order'), "sales_order.entity_id = main_table.order_id", array("*"));

		/*
        $collection->getSelect()
        			->joinLeft(array('e'=>'catalog_product_entity','eav'=>'eav_attribute','var'=>'catalog_product_entity_varchar'),"main_table.product_id = e.entity_id AND e.entity_type_id = eav.entity_type_id AND eav.attribute_code = 'brand'
   AND eav.attribute_id = var.attribute_id
   AND var.entity_id = e.entity_id
   AND var.store_id = 0",array('brand'));
   8*/
        $collection->getSelect()
        			->joinLeft(array('o'=>'sales_flat_order'),'main_table.order_id = o.entity_id',array('grand_total','state','status','customer_id','customer_id','subtotal','billing_address_id','shipping_address_id','increment_id','customer_email','shipping_amount','customer_firstname','total_item_count','omcstatus','obflag'));
        $collection->getSelect()
        			->joinLeft(array('payment'=>'sales_flat_order_payment'),'o.entity_id  = payment.parent_id',array('method'));
        $collection->getSelect()
        			->joinLeft(array('sa'=>'sales_flat_order_address'),'o.shipping_address_id = sa.entity_id',array('sa.region'=>'region' ,'sa.postcode'=>'postcode','sa.firstname'=>'firstname','sa.street'=>'street','sa.city'=>'city','sa.telephone'=>'telephone','sa.fax'=>'fax'));
        $collection->getSelect()
        			->joinLeft(array('ba'=>'sales_flat_order_address'),'o.billing_address_id  = ba.entity_id',array('ba.region'=>'region','ba.postcode'=>'postcode','ba.firstname'=>'firstname','ba.street'=>'street','ba.city'=>'city','ba.telephone'=>'telephone','ba.fax'=>'fax','ba.company'=>'company'));
					
		 $collection->addExpressionFieldToSelect(
                    'bfullname',
                    "CONCAT({{bfirstname}}, ' ', {{blastname}})",
                    array('bfirstname'=>"ba.firstname", 'blastname'=>"ba.lastname")
					);		
		 $collection->addExpressionFieldToSelect(
                    'sfullname',
                    "CONCAT({{sfirstname}}, ' ', {{slastname}})",
                    array('sfirstname'=>"sa.firstname", 'slastname'=>"sa.lastname")
					);		
		 $collection->addExpressionFieldToSelect(
                    'sfullstreet',
                    "CONCAT(CONCAT({{sregion}}, ' ', {{scity}}), ' ', {{sstreet}})",
                    array('sregion'=>"sa.region", 'scity'=>"sa.city", 'sstreet'=>"sa.street")
					);		
					
		
 //echo $collection->getSelect()->assemble();
	//	exit();
 	/* cosmo  
        $collection->getSelect()
        			->joinLeft(array('evar'=>'catalog_product_entity_int'),"main_table.sku = evar.entity_id AND evar.attribute_id='70' ",array('brand'=>'value'));
   
        $collection->getSelect()
        			->joinLeft(array('evar2'=>'catalog_product_entity_varchar'),"main_table.product_id = evar2.entity_id AND evar2.attribute_id='160' ",array('pono'=>'value'));
   
        $collection->getSelect()
        			->joinLeft(array('evar3'=>'catalog_product_entity_varchar'),"main_table.product_id = evar3.entity_id AND evar3.attribute_id='151' ",array('evar3.style_number'=>'value'));
   
   
        $collection->getSelect()
        			->joinLeft(array('evar5'=>'catalog_product_entity_varchar'),"main_table.product_id = evar5.entity_id AND evar5.attribute_id='149' ",array('color'=>'value'));
   
   
   
        $collection->getSelect()
       			->joinLeft(array('evar4'=>'view_products_option_size'),"main_table.product_id = evar4.entity_id AND evar4.attribute_id='155'  ",array('size'=>'value'));
cosmo	*/				
   
 //echo $collection->getSelect()->assemble();
	//	exit();
//configurable

     // $collection->getSelect()
      //                 ->join(
					   
     ////               array('sales_flat_order'=>'sales/order_collection'),
     //               'sales_flat_order.entity_id = sales_flat_order.entity_id',
    //                array('sales_flat_order.entity_id')
	//			 	   
	//				   " sales_flat_order ON sales_flat_order.entity_id = fi.order_id   ");

		//$collection->joinAttribute('order_id', 'sales/order_collection', 'order_id', null, 'inner');
        $this->setCollection($collection);

		//foreach($collection as $item){
			//print_r($item);
			//exit();
		//}
		
		
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header' => '订单生成时间',
            'index' => 'created_at',
			'filter_index' =>'main_table.created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));  
        $this->addColumn('created_at2', array(
            'header' => '订单出库时间',
            'type' => 'datetime',
            'width' => '100px',
        ));  
		
        $this->addColumn('sku001', array(
            'header' => '承运商名称',
            'type' => 'text',
            'width' => '100px',
        ));  
        $this->addColumn('sku002', array(
            'header' => '承运商订单编号',
            'type' => 'text',
            'width' => '100px',
        ));  
        $this->addColumn('increment_id', array(
            'header' => '订单号',
            'index' => 'increment_id',
			'filter_index' =>'o.increment_id',
            'type' => 'text',
            'width' => '100px',
        ));  
        $this->addColumn('sku003', array(
            'header' => '促销编号',
            'type' => 'text',
            'width' => '100px',
        ));  
		
        $this->addColumn('sku', array(
            'header'=>'商品代码',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sku',
        ));
        $this->addColumn('name', array(
            'header'=>'商品名称',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'name',
        ));
		//todo
        $this->addColumn('brand', array(
            'header'=>'品牌',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'brand',
        ));
		
		
        $this->addColumn('qty_ordered', array(
            'header'=>'数量',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'qty_ordered',
        ));
        $this->addColumn('price', array(
            'header'=>'单价',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'price',
        ));
        $this->addColumn('row_total', array(
            'header'=>'小计',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'row_total',
        ));
        $this->addColumn('grand_total', array(
            'header'=>'订单总计',
            'width' => '80px',
            'type'  => 'text',
			'filter_index' =>'o.grand_total',
            'index' => 'grand_total',
        ));
		
		//todo
        $this->addColumn('shipping_amount', array(
            'header'=>'配送费用',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'shipping_amount',
        ));
		//todo
        $this->addColumn('subtotal', array(
            'header'=>'总产品金额',
            'width' => '80px',
            'type'  => 'text',
			'filter_index' =>'o.subtotal',
            'index' => 'subtotal',
        ));
        $this->addColumn('bfullname', array(
            'header'=>'客户名',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'bfullname',
        ));
        $this->addColumn('sfullname', array(
            'header'=>'收货人姓名',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sfullname',
        ));
		
		
	


	


	


	


	


	



        $this->addColumn('saregion', array(
            'header'=>'收货人省（直辖市）',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sa.region',
        ));
        $this->addColumn('sacity', array(
            'header'=>'收货人市（区）',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sa.city',
        ));
		
        $this->addColumn('sfullstreet', array(
            'header'=>'收货人地址',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sfullstreet',
        ));
        $this->addColumn('sapostcode', array(
            'header'=>'收货人邮编',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sa.postcode',
        ));
		
		
	


	


	


	


        $this->addColumn('satelephone', array(
            'header'=>'Ship To Contact Phone',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sa.telephone',
        ));
		
		
		
        $this->addColumn('sku009', array(
            'header'=>'订单来源（Tele/online）',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sku009',
        ));
		
        $this->addColumn('state', array(
            'header'=>'订单状态',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'state',
        ));
			
		
        $this->addColumn('status', array(
            'header'=>'订单状态',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'status',
        ));
			
        $this->addColumn('method', array(
            'header'=>'支付方式（COD/Alipay/Chinapay）',
            'width' => '80px',
            'type'  => 'text',
			'filter_index' =>'payment.method',
            'index' => 'method',
        ));
		

	


        $this->addColumn('sku011', array(
            'header'=>'扣单原因',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sku011',
        ));
        $this->addColumn('sku012', array(
            'header'=>'取消原因',
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sku012',
        ));
		
        $this->addColumn('obflag', array(
            'header'=>'OB Flag',
            'width' => '80px',
            'type'  => 'text',
			'filter_index' =>'o.obflag',
            'index' => 'obflag',
        ));
		
		
        $this->addColumn('omcstatus', array(
            'header'=>'OMC Status',
            'width' => '80px',
            'type'  => 'text',
			'filter_index' =>'o.omcstatus',
            'index' => 'omcstatus',
        ));
		
		
        $this->addExportType('*/*/exportOrderreport', Mage::helper('sales')->__('CSV'));

        return parent::_prepareColumns();  
    }
}
