#!/usr/bin/env python
# -*- coding: utf-8 -*- 
#author : wang yaofeng <yaofeng.wang@gmail.com>

from suds.client import Client
import getopt
import sys
from datetime import datetime
from datetime import date
import logging
logging.basicConfig(level=logging.INFO)
#logging.getLogger('suds.client').setLevel(logging.DEBUG)
#logging.getLogger('suds.transport').setLevel(logging.DEBUG)
#logging.getLogger('suds.xsd.schema').setLevel(logging.DEBUG)
#logging.getLogger('suds.wsdl').setLevel(logging.DEBUG)
'''
./sendsms.py -m '13818252514' -t '测试4'

ps:
Server raised fault: '服务器无法读取请求。 ---> XML 文档(1, 657)中有错误。 ---> 字符串“”不是有效的 AllXsd 值。

datetime,调用时通过soap传递xml(非.net程序调用),发生如下错误
字符串“2008-04-10 11:11:11”不是有效的 AllXsd 值
解决:参数类型改为string,在web method里面用Datetime.Parse转换

最便捷方法 from datetime import datetime    datetime.now()
'''
url = 'http://210.22.130.88:8080/SMSWebservice.asmx?wsdl'
client = Client(url)
#print client
arraymdlDetail = client.factory.create('ArrayOfSMS_INTERFACE_DETAIL')
mdlDetail = client.factory.create('SMS_INTERFACE_DETAIL')
mdlMaster = client.factory.create('SMS_INTERFACE_MASTER')

now = datetime.now()
nowstr = now.strftime('%Y%m%d%H%M%S%f')

try:
    #print sys.argv[1:]
    opts,args=getopt.getopt(sys.argv[1:],'m:t:')
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
    if o=='-m':
        mdlDetail.BATCH_ID = nowstr
        mdlDetail.CustomerID = ''
        mdlDetail.MemberID = ''
        mdlDetail.Moblie = a
        mdlDetail.Name = ''
        mdlDetail.AvablePoints = 0
        mdlDetail.TerminteDate = now
        mdlDetail.MemberTier = ''
        mdlDetail.TierTerDate = now
        mdlDetail.Birthday = now
        mdlDetail.JoinDate = now
        mdlDetail.MemberTerDate = now
        mdlDetail.TransactionDate = now
        mdlDetail.ProductName = ''
        mdlDetail.ProductNum = 0
        mdlDetail.CounterName = ''
        mdlDetail.RedemptionChannel = ''
        mdlDetail.RedemptionPoints = 0
        mdlDetail.ReCounterName = ''
        mdlDetail.ReProductName = ''
        mdlDetail.ReProductNum = 0
        
    if o=='-t':
        mdlMaster.SSRCTERMID = '11'
        mdlMaster.BATCH_ID = nowstr
        mdlMaster.Brand_ID = '000'
        mdlMaster.PRIORITY = 0
        mdlMaster.SMS_TYPE = 1
        mdlMaster.Campaign_Code = ''
        mdlMaster.SEND_NUMBER = 1
        mdlMaster.SEND_CHANNEL = 1
        mdlMaster.SMSGCONTENT = a.decode('UTF-8')
        mdlMaster.SendDate = now
        arraymdlDetail.SMS_INTERFACE_DETAIL.append(mdlDetail)   


#print    arrayOfOrderItemsInformation
#cus.**** = ****
#print (mdlMaster,arraymdlDetail)
try:
    ret = client.service.GetSMSInfo(mdlMaster,arraymdlDetail)
    print ret
    pass
except:
    print "Unexpected error:", sys.exc_info()
