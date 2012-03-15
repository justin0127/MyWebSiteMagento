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
hdlr = logging.FileHandler('/var/www/var/log/cancel_'+today+ '.log')
formatter = logging.Formatter('%(asctime)s %(levelname)s %(message)s')
hdlr.setFormatter(formatter)
logger.addHandler(hdlr)
logger.setLevel(logging.INFO)

#url = 'http://210.22.130.92/SisleyWebService/Service.asmx?wsdl'
#url = 'http://10.4.58.147/SisleyWebWS/Service.asmx?wsdl'
url = 'http://10.4.58.147/SisleyWebService/Service.asmx?wsdl'

client = Client(url)
#print client
orderCanncellationInformation = client.factory.create('OrderCancellationInformation')
 

#orderCanncellationInformation = {'OnlineOrderID':'100001249','CancelDate':'2011-10-03','CancelOption':'asddf','CancelReason':'asddf'}
'''
(orderCanncellationInformation){
   OnlineOrderID = None
   CancelDate = None
   CancelOption = None
   CancelReason = None
 }
 
/var/www/sisley/shell$ /var/www/sisley/shell/cancelorder.py -o \{\'OnlineOrderID\':\'100001244\',\'CancelDate\':\'2011-05-03\',\'CancelOption\':\'asddf\',\'CancelReason\':\'asddf\'\}

((orderCanncellationInformation){
   OnlineOrderID = "100001058"
   CancelDate = "2011-05-03"
   CancelOption = "10"
   CancelReason = "4"
 })
0
 
'''



try:
    #print sys.argv
    

    opts,args=getopt.getopt(sys.argv[1:],'o')

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
#
print args   
        


for a in args:
	dicta = eval(a)
	print '---------------- orderCanncellationInformation-----------------'
	print dicta
	for key in dicta:
		if type(dicta[key]) is StringType:
			setattr(orderCanncellationInformation, key, dicta[key].decode('UTF-8'))
		else:
			setattr(orderCanncellationInformation, key, dicta[key])         
			
			
print '---------------------summary-----------------------------------------------------'

print (orderCanncellationInformation)
try:
    ret = client.service.orderCancellationOnlineToCRMDatabase(orderCanncellationInformation)
    logger.info(ret)
    print ret
    pass
except:
    logger.error(sys.exc_info())    
    print sys.exc_info()
#ret = client.service.MemberInformationOnlineToCRMDatabase(None)
