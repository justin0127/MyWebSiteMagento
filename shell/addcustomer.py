#!/usr/bin/env python
# -*- coding: utf-8 -*- 
#author : wang yaofeng <yaofeng.wang@gmail.com>

from suds.client import Client
import getopt
import sys
import logging
from datetime import  date
from types import *

today = str(date.today())
logger = logging.getLogger()
hdlr = logging.FileHandler('/var/www/var/log/'+today+ '-customer.log')
formatter = logging.Formatter('%(asctime)s %(levelname)s %(message)s')
hdlr.setFormatter(formatter)
logger.addHandler(hdlr)
logger.setLevel(logging.INFO)



#url = 'http://210.22.130.92/SisleyWebService/Service.asmx?wsdl'
#url = 'http://10.4.58.147/SisleyWebWS/Service.asmx?wsdl'
url = 'http://10.4.58.147/SisleyWebService/Service.asmx?wsdl'

client = Client(url)
#print client
customerInformation = client.factory.create('tns:CustomerInformation')

'''
./addcustomer.py -c \{\'OnlineCustomerID\':\'1231d\',\'CustomerName\':\'客户名称\'\} 
    
(CustomerInformation){
   OnlineCustomerID = None
   CustomerName = None
   CustomerTitle = None
   CustomerBirthday = None
   CustomerProvince = None
   CustomerCity = None
   CustomerAddress = None
   CustomerZip = None
   CustomerEmail = None
   CustomerAreaCode = None
   CustomerTele = None
   CustomerMobile = None
   ParentID = None
   Userrank = None
   Msn = None
   Qq = None
   Officephone = None
   Alias = None
 }

'''
try:
    orderlog = ' '.join(sys.argv)
    logger.info(orderlog)    
    #print sys.argv[1:]
    opts,args=getopt.getopt(sys.argv[1:],'c:')
     
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
    if o=='-c':
        dicta = eval(a)
        for key in dicta:
            if type(dicta[key]) is StringType:
                setattr(customerInformation, key, dicta[key].decode('UTF-8'))
            else:
                setattr(customerInformation, key, dicta[key])            


print '---------------------summary-----------------------------------------------------'
#print arrayOfOrderItemsInformation.OrderItemsInformation
print (customerInformation)

try:
    ret = client.service.MemberInformationOnlineToCRMDatabase(customerInformation)
    logger.info(ret) 
    print ret
    pass
except:
    logger.error(sys.exc_info())    
    print sys.exc_info()
