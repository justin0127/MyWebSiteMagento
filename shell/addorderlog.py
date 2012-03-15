#!/usr/bin/env python
# -*- coding: utf-8 -*- 
#author : wang yaofeng <yaofeng.wang@gmail.com>

from suds.client import Client
import getopt
import sys
import logging
from types import *
from datetime import  date

today = str(date.today())
logger = logging.getLogger()
hdlr = logging.FileHandler(today+ '.log')
formatter = logging.Formatter('%(asctime)s %(levelname)s %(message)s')
hdlr.setFormatter(formatter)
logger.addHandler(hdlr)
logger.setLevel(logging.INFO)

#url = 'http://210.22.130.92/SisleyWebService/Service.asmx?wsdl'
url = 'http://10.4.58.147/SisleyWebWS/Service.asmx?wsdl'

client = Client(url)
#print client
orderInformation = client.factory.create('OrderInformation')
deliveryInformation = client.factory.create('DeliveryInformation')
#orderItemsInformation = client.factory.create('OrderItemsInformation')
arrayOfOrderItemsInformation = client.factory.create('ArrayOfOrderItemsInformation')
'''
(OrderInformation){
   OnlineOrderID = None
   DateOfOrder = None
   OrderSource = None
   OnlineCustomerID = None
   RetailAmountOfOrder = None
   ActualAmountOfOrder = None
   DiscountAmountOfOrder = None
   Payment = None
   PaymentChannel = None
   PaymentCost = None
   TotalProductAmount = None
   InvoiceTitle = None
   ShippingCost = None
   UsedPoints = None
   Package = None
   PackageCost = None
   PromotionCode = None
   Remark = None
   UsedItemPoints = None
 }
 
 
(DeliveryInformation){
   OnlineOrderID = None
   DeliveryType = None
   ReceiverName = None
   ReceiverProvince = None
   ReceiverCity = None
   ReceiverAddress = None
   ReceiverZip = None
   ReceiverAreaCode = None
   ReceiverTelePhone = None
   ReceiverMobile = None
 }
 
 
 (OrderItemsInformation){
   OnlineOrderID = None
   ProductID = None
   ProductType = None
   Quantity = None
   BasePrice = None
   Price = None
   UsedItemPoints = None
 }
 
(ArrayOfOrderItemsInformation){
   OrderItemsInformation[] = <empty>
 }
 
/var/www/sisley/shell$ /var/www/sisley/shell/addorder.py -o \{\'OnlineOrderID\':\'1000000192\',\'DateOfOrder\':\'2011-05-04\',\'OrderSource\':\'10\',\'OnlineCustomerID\':\'4\',\'RetailAmountOfOrder\':\'510\.00\',\'ActualAmountOfOrder\':\'510\.00\',\'DiscountAmountOfOrder\':\'0\',\'Payment\':0,\'PaymentChannel\':99,\'PaymentCost\':\'50\.00\',\'TotalProductAmount\':\'450\',\'InvoiceTitle\':\'0\',\'ShippingCost\':\'10\.00\',\'UsedPoints\':0,\'Package\':\'Normal\',\'PackageCost\':\'0\',\'PromotionCode\':\'0\',\'Remark\':\'0\',\'UsedItemPoints\':0\} -i [\{\'OnlineOrderID\':\'1000000192\',\'ProductID\':\'27AA030FL\',\'ProductType\':0,\'Quantity\':1,\'BasePrice\':\'450\',\'Price\':\'450\',\'UsedItemPoints\':0\}] -d \{\'OnlineOrderID\':\'1000000192\',\'DeliveryType\':\'EMS_COD1\',\'ReceiverName\':\'asdasd\',\'ReceiverProvince\':\'上海\',\'ReceiverCity\':\'asdf\',\'ReceiverAddress\':\'adsdfasdf\',\'ReceiverZip\':\'293932480\',\'ReceiverAreaCode\':\'293932480\',\'ReceiverTelePhone\':\'asdfasdf\',\'ReceiverMobile\':\'asddf\'\}

    
((OrderInformation){
   OnlineOrderID = "1022"
   DateOfOrder = "2011-05-03"
   OrderSource = "10"
   OnlineCustomerID = "4"
   RetailAmountOfOrder = "452"
   ActualAmountOfOrder = "452"
   DiscountAmountOfOrder = "0"
   Payment = 1
   PaymentChannel = 1
   PaymentCost = "1"
   TotalProductAmount = "450"
   InvoiceTitle = "123"
   ShippingCost = "1"
   UsedPoints = 0
   Package = "Normal"
   PackageCost = "0"
   PromotionCode = "123"
   Remark = "123"
   UsedItemPoints = 0
 }, (ArrayOfOrderItemsInformation){
   OrderItemsInformation[] = 
      (OrderItemsInformation){
         OnlineOrderID = "1022"
         ProductID = "27AA030FL"
         ProductType = 1
         Quantity = 1
         BasePrice = "450"
         Price = "450"
         UsedItemPoints = 0
      },
 }, (DeliveryInformation){
   OnlineOrderID = "1022"
   DeliveryType = "EMS_COD1"
   ReceiverName = "123"
   ReceiverProvince = "123"
   ReceiverCity = "123"
   ReceiverAddress = "123"
   ReceiverZip = "123"
   ReceiverAreaCode = "123"
   ReceiverTelePhone = "123"
   ReceiverMobile = "123"
 })
0
 
'''



try:
    #print sys.argv
    

    opts,args=getopt.getopt(sys.argv[1:],'o:i:d:')

    orderlog = ' '.join(sys.argv)
    logger.info(orderlog)    
        
    #opts 是带-选项的参数
    #args 是没有选项的参数
    #print opts
    #print '------------'
    #print args
    #print '------------'
    #h表示使用-h,h选项没有对应的值
    #x:表示你要使用-xValue,x选项必须有对应的值.
except getopt.GetoptError:
    #打印帮助信息并退出
    print 'cmd error'
    sys.exit(2)
#处理命令行参数
for o,a in opts:
    if o=='-o':
        dicta = eval(a)
        print '---------------- orderInformation-----------------'
        print dicta
        for key in dicta:
            if type(dicta[key]) is StringType:
                setattr(orderInformation, key, dicta[key].decode('UTF-8'))
            else:
                setattr(orderInformation, key, dicta[key])            
        
    if o=='-i':
        #print '-----------'
        #print a
        listorderItems = eval(a)
        print '---------------- order items-----------------'
        print listorderItems        
        for item in listorderItems:
            orderItemsInformation = client.factory.create('OrderItemsInformation')
            for key in item:
                if type(item[key]) is StringType:
                    setattr(orderItemsInformation, key, item[key].decode('UTF-8'))
                else:
                    setattr(orderItemsInformation, key, item[key])
                
            #print orderItemsInformation      
            arrayOfOrderItemsInformation.OrderItemsInformation.append(orderItemsInformation)
        #print eval(a)
        #print arrayOfOrderItemsInformation
    if o=='-d':
        dicta = eval(a)
        print '---------------- dilivery Information-----------------'
        print dicta
        for key in dicta:
            if type(dicta[key]) is StringType:
                setattr(deliveryInformation, key, dicta[key].decode('UTF-8'))
            else:
                setattr(deliveryInformation, key, dicta[key])


print '---------------------summary-----------------------------------------------------'

#print (orderInformation, arrayOfOrderItemsInformation, deliveryInformation)
try:
    ret = client.service.addOrderInformationOnlineToCRMDatabase(orderInformation, arrayOfOrderItemsInformation, deliveryInformation)
    if ret=='1':
		logger.info( "接口数据不完整")
	else if ret=='2': 
		logger.info("接口数据中订单号各有不同")
	else if ret=='3': 
		logger.info("接口数据中订单号已存在")
	else if ret=='4': 
		logger.info("订单中的客户编号不存在")
	else if ret=='5': 
		logger.info("接口数据单号不完整")
	else if ret=='6': 
		logger.info("数据操作失败")
	else if ret=='7': 
		logger.info("产品编号不存在")
	else if ret=='8': 
		logger.info("配送信息不完整")
	else if ret=='9': 
		logger.info("订单金额不匹配")
	else:
		logger.info("下发成功")
	logger.info(ret) 
    pass
except:
    logger.error(sys.exc_info())    
#ret = client.service.MemberInformationOnlineToCRMDatabase(None)
