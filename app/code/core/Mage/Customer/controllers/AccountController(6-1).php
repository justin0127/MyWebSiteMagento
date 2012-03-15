<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_AccountController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('loginPost', 'create');

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        if (!preg_match('/^(create|login|logoutSuccess|forgotpassword|forgotpasswordpost|confirm|confirmation)/i', $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    /**
     * Action postdispatch
     *
     * Remove No-referer flag from customer session after each action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        $this->_getSession()->unsNoReferer(false);
    }

    /**
     * Default customer account page
     */
    public function passAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('customer/form_passedit')
        );
        $this->getLayout()->getBlock('head')->setTitle($this->__('Forget Password'));
        $this->renderLayout();
    } 
     
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('customer/account_dashboard')
        );
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();
    }

    /**
     * Customer login form page
     */
    public function loginAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    /**
     * Login post action
     */
    public function loginPostAction()
    {

 	
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
        
        //confirm info 
        $PostInfo = $this->getRequest()->getPost();
	if (array_key_exists('message', $PostInfo)) {
        	if(!$PostInfo['message']) {
		$session->addError($this->__('Confirm Message Error.'));
		}else{
        	$CurrentMessage = $PostInfo['message'];
        	$CurrentId = $PostInfo['id'];
        	$customer = Mage::getModel('customer/customer')->load($CurrentId);
        	$IsConfirm = $customer->getConfirmation();
		$BackendMessage = $customer->getMessage();
		//var_dump($PostInfo['message']);
		//exit;
			if ($CurrentMessage != $BackendMessage) {
			$session->addError($this->__('Confirm Message Error.'));
			}else{
			$customer->setConfirmation('');
			$customer->save();
			/*** insert offline customer to CRM   ***/
			$OnlineCustomerID = $CurrentId;
			$CustomerName = $customer->getFirstname().$customer->getLastname();
			$CustomerTitle = '0';
			$CustomerBirthday = '1900-01-01';
			$CustomerProvince = '0';
			$CustomerCity = '0';
			$CustomerAddress = '0';
			$CustomerZip = '0';
			
			$CustomerEmail = $customer->getEmail();
			
			$CustomerAreaCode = '0';
			$CustomerTele = '0';
			
			if($customer->getMobile()) {
			$CustomerMobile = $customer->getMobile();
			}else{
			$CustomerMobile = '0';
			}
			
			$ParentID = '0';
			$Userrank = '0';
			$Msn = '0';
			$Qq = '0';
			$Officephone = '0';
			$Alias = $customer->getAlias();
			$offlineAdd = "/var/www/sisley/shell/addcustomer.py -c \{\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'CustomerName\':\'".$CustomerName."\',\'CustomerTitle\':\'".$CustomerTitle."\',\'CustomerBirthday\':\'".$CustomerBirthday."\',\'CustomerProvince\':\'".$CustomerProvince."\',\'CustomerCity\':\'".$CustomerCity."\',\'CustomerAddress\':\'".$CustomerAddress."\',\'CustomerZip\':\'".$CustomerZip."\',\'CustomerEmail\':\'".$CustomerEmail."\',\'CustomerAreaCode\':\'".$CustomerAreaCode."\',\'CustomerTele\':\'".$CustomerTele."\',\'CustomerMobile\':\'".$CustomerMobile."\',\'ParentID\':\'".$ParentID."\',\'Userrank\':\'".$Userrank."\',\'Msn\':\'".$Msn."\',\'Qq\':\'".$Qq."\',\'Officephone\':\'".$Officephone."\',\'Alias\':\'".$Alias."\'\}";
			//Mage::log($offlineAdd);
			shell_exec($offlineAdd);
			
			/***  end insert  ***/
			$login['username'] = $customer->getEmail();
			$login['password'] = 'asdasd';
			$login['id'] = $customer->getId();
			//$session->loginHash($login['username'], $login['password'], $login['id']);
			$customer->sendNewAccountEmail('confirmed');
			$this->_getSession()->clear();
          		//$this->_getSession()->addSuccess($this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName()));
          		$this->_getSession()->setCustomerAsLoggedIn($customer);
          		//$successUrl = $this->_getSession()->getBeforeAuthUrl(true);
               		$successUrl = $this->_welcomeCustomer($customer, true);
               		$this->_redirectSuccess($successUrl);
               		//$this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
			}
				
		}
        }else{
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', Mage::helper('customer')->getEmailConfirmationUrl($login['username']));
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        
        }
        }

        $this->_loginPostRedirect();
    }

    /**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect()
    {
        $session = $this->_getSession();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl() ) {

            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());

            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn())
            {
                if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) {
                    if ($referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) {
                        $referer = Mage::helper('core')->urlDecode($referer);
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                }
                else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }
        else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
        $this->_redirectUrl($session->getBeforeAuthUrl(true));
    }

    /**
     * Customer logout action
     */
    public function logoutAction()
    {
        $this->_getSession()->logout()
            ->setBeforeAuthUrl(Mage::getUrl());

        $this->_redirect('*/*/logoutSuccess');
    }

    /**
     * Logout success page
     */
    public function logoutSuccessAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer register form page
     */
    public function createAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
    
    $CustomerName = $this->getRequest()->getPost('firstname').$this->getRequest()->getPost('lastname');
    $CustomerMobile = $this->getRequest()->getPost('mobile');
    $Type = $this->getRequest()->getPost('regist_type');
	//echo $Type;
	//exit;
	
	
    $dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
    $username = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/username');
    $password = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/password');
    Mage::app();
	$link = mysql_connect('localhost', $username, $password);
	mysql_query("SET NAMES 'utf8'");
       	if (!$link) {
         die('Not connected : ' . mysql_error());
       	}//end if
       	$db_selected = mysql_select_db($dbname, $link);
       	if (!$db_selected) {
         die ('Can\'t use foo : ' . mysql_error());
       	}else{
       	       $tel = $CustomerMobile;
       	       $query = sprintf("SELECT name FROM offline WHERE tel='%s'",
               mysql_real_escape_string($tel));
               
               $result = mysql_query($query); 
               if (!$result) {
               $message  = 'Invalid query: ' . mysql_error() . "\n";
               $message .= 'Whole query: ' . $query;
               die($message);
               }//end if 
               while ($row = mysql_fetch_assoc($result)) {
               $value = $row['name'];
               }
        $BackendName = $value;       
       	}
       	mysql_close($link);
       	//var_dump($BackendName);
       	//var_dump($CustomerName);
       	//exit;
		
		//修改认证错误说明
    if(($CustomerMobile != NULL)&&($BackendName != $CustomerName)&&($Type == 'offline')) {
    $message = $this->__('会员姓名与手机号不符，请再次确认！');
    $session->setEscapeMessages(false);
    $session->addError($message);
    $this->_redirectError(Mage::getUrl('*/*/create?regist_type=offline', array('_secure' => true)));
    }else{    
        
        
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            $data = $this->_filterPostData($this->getRequest()->getPost());

            foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
                if ($node->is('create') && isset($data[$code])) {
                    if ($code == 'email') {
                        $data[$code] = trim($data[$code]);
                    }
                    $customer->setData($code, $data[$code]);
                }
            }

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                $address = Mage::getModel('customer/address')
                    ->setData($this->getRequest()->getPost())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false))
                    ->setId(null);
                $customer->addAddress($address);

                $errors = $address->validate();
                if (!is_array($errors)) {
                    $errors = array();
                }
            }

            try {
                $validationCustomer = $customer->validate();
                if (is_array($validationCustomer)) {
                    $errors = array_merge($validationCustomer, $errors);
                }
                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->save();

 		    $PostInfo = $this->getRequest()->getPost();
 		    
                    if ($customer->isConfirmationRequired()) {
                    if($PostInfo['regist_type'] == 'online') {
 		          $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl());
 		    }else{
                        //$customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl());
						
			  /*** start edm ***/
			  
                       	  $SoapClient = new SoapClient("http://app.focussend.com/webservice/FocusSendWebService.asmx?WSDL",array('trace' => 1));
			  $FocusUser   = new StdClass;
			  $FocusUser->Email="arvatoservices@bertelsmann.com.cn";
			  $FocusUser->Password=sha1("arvatoli");
			  
			  $FocusEmail=new StdClass;
			  $FocusEmail->Body="<table style='font:12px/1em Verdana, Arial, Helvetica, sans-serif;color:#7f7f7f;' ><table cellspacing='0' cellpadding='0' border='0' width='635px' style='margin-top:10px; font:12px/1em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;'><tr><td align='center' valign='top'><!-- [ header starts here] --><table cellspacing='0' cellpadding='0' border='0' width='635px'><tr><td valign='top' align='center' ><img src='http://232.magentoecommerce.cn/sisley/skin/frontend/default/default/images/logo_email.gif' alt='sisley_banner'  style='margin-bottom:10px;' border='0'/></a></td></tr><table  style='text-align: center;' cellspacing='0' cellpadding='0' border='0' width='635px'><td><tr style='background:#AAAAAA; color:white; ' height='1px'><td style='width:16.5%; border-right:1px solid white;'><a style='color:#fff; text-decoration:none;' href='http://232.magentoecommerce.cn/sisley/news.html' >希思黎最新动态</a></td><td style='width:16.5%; border-right:1px solid white;'><a style='color:#fff; text-decoration:none;' href='http://232.magentoecommerce.cn/sisley/about-sisley.html'>关于希思黎</a></td><td style='width:16.5%; border-right:1px solid white;'><a style='color:#fff; text-decoration:none;' href='http://232.magentoecommerce.cn/sisley/skin.html'>护肤系列</a></td><td style='width:16.5%; border-right:1px solid white;'><a style='color:#fff; text-decoration:none;' href='http://232.magentoecommerce.cn/sisley/color.html'>彩妆系列</a></td><td style='width:16.5%; border-right:1px solid white;'><a style='color:#fff; text-decoration:none;' href='http://232.magentoecommerce.cn/sisley/perfume.html'>香水系列</a></td><td style='width:16.5%; '><a style='color:#fff; text-decoration:none;' href='http://232.magentoecommerce.cn/sisley/man.html'>男士系列</a></td></tr></table><!-- [ middle starts here] --><table    width='635px' cellspacing='0' cellpadding='0' border='0' style='background:url('http://232.magentoecommerce.cn/sisley/skin/frontend/default/default/images/email_rep_bg.gif') repeat-y scroll right top transparent;'><tbody   width='635px'   style=' background: url('http://232.magentoecommerce.cn/sisley/skin/frontend/default/default/images/emailbg.gif') no-repeat scroll right top transparent;' ><tr height='1px'><td><p align='left'><strong>亲爱的：".$CustomerName = $customer->getFirstname().$customer->getLastname()."</strong></p><p align='left'>非常感谢您在希思黎网站注册。您的账户信息已经成功创建如下，请妥善保留此邮件供参考！</p><p align='left'>您的昵称:".$CustomerName = $customer->getFirstname().$customer->getLastname()."</br>您的邮箱为:".$customer->getEmail()."</p><p align='left'>您的邮箱还没有通过验证，请点击以下链接或者粘贴到您的<br />浏览器地址栏登录，验证邮箱激活您的账号，请务必在两天内<br />通过此链接完成您的账户注册，过期无效.(此链接只能使用一次)<br /><a href=".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)."checkregister>http://www.sisley.com.cn/active</a></p><p align='left'>您将会在第一时间收到希思黎品牌最新资讯：网上独享服务,<br />线下活动,以及当季的美容小贴士等。</p><p align='left'>作为希思黎尊贵的注册客人，您在希思黎官方购物网站将<a href='http://232.magentoecommerce.cn/sisley/sis_reason.htm'>尊享</a>：</p><ul align='left' text-align='left'><li ><p align='left'>100%正品保证</p></li><li ><p align='left'>全场免运费</p></li><li ><p align='left'>度身定制专属美肌护肤方案</p></li><li ><p align='left'>尊贵独享限量版礼遇</p></li><li ><p align='left'>尊享体验礼包</p></li><li ><p align='left'>免费尊享高级精美礼盒包装</p></li><li ><p align='left'>免费尊享个性礼品卡随单赠送</p></li><li ><p align='left'>24小时一键订购方便快捷</p></li><li ><p align='left'>第一时间掌握最新资讯</p></li><li ><p align='left'>一对一贴心客户服务</p></li></ul><p align='left'>现在就来轻松开启希思黎精彩网上臻享购物:<a href='http://www.sisley.com.cn/'>http://www.sisley.com.cn/</a></p><p align='left'>同时，您也可以前往希思黎专柜，我们的美容顾问将会为您提供一对一的臻享服务，<br />解答您的护肤疑问，度身定制您专属的植物美容护肤方案。</p><p align='left'>目前，希思黎美容坊已入驻国内各大顶尖购物中心，静候您的独家尊贵私享。</p><p align='left'>我们期待您的光临！请点击柜台查询网址：<a href='http://www.sisley.com.cn/'>http://www.sisley.com.cn/?page=sisley_store_locator</a></p><table style='background:#fff; float: left;' cellspacing='0' cellpadding='0' border='0' width='635' ><h3 style='color:#797f7f; border-bottom:1px solid #d0d0d0; padding-bottom:4px;' align='left'><strong>希思黎热销推荐</strong></h3><table style='width:100%;float:left;'><ul style='width:635px;margin:0 auto;'><li style='float:left; width:120px;list-style:none outside none;'><a href='http://232.magentoecommerce.cn/sisley/114100.html'><img src='http://232.magentoecommerce.cn/sisley/skin/frontend/default/default/images/全能乳液.jpg'  alt='全能乳液'  style='border:1px solid #e1e1e1; margin-left=32px; margin-right:32px;width:100px;height:100px;'/></a><a style='color:#6f6f6f;text-underline:none; float: left;' href='http://232.magentoecommerce.cn/sisley/114100.html'><p align='left' style='width:120px;'>全能乳液</p></a><p align='left' style='font-family:microsoft yahei;'>125ml - ￥1,480.00</p></li><li style='float:left; width:145px;list-style:none outside none;'><a href='http://232.magentoecommerce.cn/sisley/154000.html'><img src='http://232.magentoecommerce.cn/sisley/skin/frontend/default/default/images/致臻夜间修复精华露.jpg'  alt='致臻夜间修复精华露'  style='border:1px solid #e1e1e1; margin-left=32px; margin-right:32px;width:100px;height:100px;'/></a><a style='color:#6f6f6f;text-underline:none; float: left;' href='http://232.magentoecommerce.cn/sisley/154000.html'><p align='left' style='width:120px;'>致臻夜间修复精华露</p></a><p align='left' style='font-family:microsoft yahei;'>50ml - ￥5,000.00</p></li><li style='float:left; width:140px;list-style:none outside none;'><a href='http://232.magentoecommerce.cn/sisley/162300.html'><img src='http://232.magentoecommerce.cn/sisley/skin/frontend/default/default/images/全日呵护精华乳.jpg' alt='全日呵护精华乳'  style='border:1px solid #e1e1e1; margin-left=32px; margin-right:32px;width:100px;height:100px;'/></a><a style='color:#6f6f6f;text-underline:none;float: left; ' href='http://232.magentoecommerce.cn/sisley/162300.html'><p align='left' style='width:120px;'>全日呵护精华乳</p></a><p align='left' style='font-family:microsoft yahei;'>50ml - ￥2,500.00</p></li><li style='float:left; width:140px;list-style:none outside none;'><a href='http://232.magentoecommerce.cn/sisley/150000.html'><img src='http://232.magentoecommerce.cn/sisley/skin/frontend/default/default/images/抗皱活肤驻颜霜.jpg' alt='抗皱活肤驻颜霜'  style='border:1px solid #e1e1e1; margin-left=32px; margin-right:32px;width:100px;height:100px;'/></a><a style='color:#6f6f6f;text-underline:none;float: left; ' href='http://232.magentoecommerce.cn/sisley/150000.html'><p align='left' style='width:120px;'>抗皱活肤驻颜霜</p></a><p align='left' style='font-family:microsoft yahei;'>50ml - ￥2,850.00</p></li></ul></table><br /><p align='left'>在希思黎官网您可以尊享：100%正品保证</p><p align='left'>希思黎官方网站暨网上商城会员热线：4008-208-139(9:00-21:00国定节假日除外)</p><p align='left'>此为系统邮件，请勿回复！</p><p align='left'>请保管好您的邮箱，以免他人盗用，如有任何疑问，请查看希思黎会员<a href='http://232.magentoecommerce.cn/sisley/rules' >条款和条件</a></p></table></td></tr></tbody></table></td></tr></table></table></table></div>";  //内容,链接不能删
			  $FocusEmail->IsBodyHtml=true;
			  
			  $FocusTask=new StdClass;
			  $FocusTask->TaskName="batch send php";
			  $FocusTask->Subject="first subject";
			  $FocusTask->SenderName="abc";
			  $FocusTask->SenderEmail="abc@focussend.com";
			  $FocusTask->ReplyName="reply";
			  $FocusTask->ReplyEmail="zcz_wn@163.com";
			  //$FocusTask->SendDate=date("Y-m-d\TH-m-s");
			  $FocusTask->SendDate="2010-06-04T00:00:00";    
			  
			  //echo $FocusTask->SendDate;

			  $subject="感谢您注册【希思黎官方网站暨网上商城】会员，请激活您的账号";
			  
			  //$FocusReceiver=new StdClass;
			  $FocusReceiver->Email=$customer->getEmail();
			  
			  //send one email
			  $result= $SoapClient->SendOne(array("user"=>$FocusUser,"email"=>$FocusEmail,"subject"=>$subject,"receiver"=>$FocusReceiver));
			  
			  //set email cookie
			  //var_dump($customer['firstname'].$customer['lastname'].'####'.$customer['mobile']);
			  //var_dump($customer);
			  //exit;
			  $value0 = $customer['entity_id'];
			  Mage::getSingleton("core/cookie")->set("id",$value0);
			  
			  $value1 = $customer['firstname'].$customer['lastname'];
			  Mage::getSingleton("core/cookie")->set("name",$value1);
			  
			  $value2 = $customer['mobile'];
			  Mage::getSingleton("core/cookie")->set("mobile",$value2);
			  /*** end edm ***/
			  
			/****  insert online user to CRM  ***/
                
                	$OnlineCustomerID = $customer->getId();
			$CustomerName = $customer->getFirstname().$customer->getLastname();
			$CustomerTitle = '0';
			$CustomerBirthday = '1900-01-01';
			$CustomerProvince = '0';
			$CustomerCity = '0';
			$CustomerAddress = '0';
			$CustomerZip = '0';
			$CustomerEmail = $customer->getEmail();
			$CustomerAreaCode = '0';
			$CustomerTele = '0';
			
			if($customer->getMobile()) {
			$CustomerMobile = $customer->getMobile();
			}else{
			$CustomerMobile = '0';
			}
			
			$ParentID = '0';
			$Userrank = '0';
			$Msn = '0';
			$Qq = '0';
			$Officephone = '0';
			$Alias = $customer->getAlias();
			$onlineAdd = "/var/www/sisley/shell/addcustomer.py -c \{\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'CustomerName\':\'".$CustomerName."\',\'CustomerTitle\':\'".$CustomerTitle."\',\'CustomerBirthday\':\'".$CustomerBirthday."\',\'CustomerProvince\':\'".$CustomerProvince."\',\'CustomerCity\':\'".$CustomerCity."\',\'CustomerAddress\':\'".$CustomerAddress."\',\'CustomerZip\':\'".$CustomerZip."\',\'CustomerEmail\':\'".$CustomerEmail."\',\'CustomerAreaCode\':\'".$CustomerAreaCode."\',\'CustomerTele\':\'".$CustomerTele."\',\'CustomerMobile\':\'".$CustomerMobile."\',\'ParentID\':\'".$ParentID."\',\'Userrank\':\'".$Userrank."\',\'Msn\':\'".$Msn."\',\'Qq\':\'".$Qq."\',\'Officephone\':\'".$Officephone."\',\'Alias\':\'".$Alias."\'\}";
			//Mage::log($offlineAdd);
			shell_exec($onlineAdd);
                
               		/***  end insert  ***/
			  
		    }   //end check regist type  
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->_redirectSuccess(Mage::getUrl('registtips?id='.$customer->getId(), array('_secure'=>true)));
                        return;
                    
                    }
                    
                    else {
                        $session->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        $this->_redirectSuccess($url);
                        return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    }
                    else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    //$url = Mage::getUrl('customer/account/forgotpassword');
                    
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                }
                else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            }
            catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }
        if($this->getRequest()->getPost('regist_type') == 'offline') {
        $this->_redirectError(Mage::getUrl('*/*/create?regist_type=offline', array('_secure' => true)));
        }else{
         $this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
        }
    }
    }

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess($this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName()));

        $customer->sendNewAccountEmail($isJustConfirmed ? 'confirmed' : 'registered');

        $successUrl = Mage::getUrl('*/*/index', array('_secure'=>true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }

    /**
     * Confirm customer account by id and confirmation key
     */
    public function confirmAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            $id      = $this->getRequest()->getParam('id', false);
            $key     = $this->getRequest()->getParam('key', false);
            $backUrl = $this->getRequest()->getParam('back_url', false);
            if (empty($id) || empty($key)) {
                throw new Exception($this->__('Bad request.'));
            }

            // load customer by id (try/catch in case if it throws exceptions)
            try {
                $customer = Mage::getModel('customer/customer')->load($id);
                if ((!$customer) || (!$customer->getId())) {
                    throw new Exception('Failed to load customer by id.');
                }
            }
            catch (Exception $e) {
                throw new Exception($this->__('Wrong customer account specified.'));
            }

            // check if it is inactive
            if ($customer->getConfirmation()) {
                if ($customer->getConfirmation() !== $key) {
                    throw new Exception($this->__('Wrong confirmation key.'));
                }

                // activate customer
                try {
                    $customer->setConfirmation(null);
                    $customer->save();
                    
                }
                catch (Exception $e) {
                    throw new Exception($this->__('Failed to confirm customer account.'));
                }

                // log in and send greeting email, then die happy
                $this->_getSession()->setCustomerAsLoggedIn($customer);
                $successUrl = $this->_welcomeCustomer($customer, true);
                $this->_redirectSuccess($backUrl ? $backUrl : $successUrl);
                
                //Mage::log('welcome back to magento and your login info has been confirmed!');    //back to magento and confirm user
                
                	/****  insert online user to CRM  ***/
                
                	$OnlineCustomerID = $customer->getId();
			$CustomerName = $customer->getFirstname().$customer->getLastname();
			$CustomerTitle = '0';
			$CustomerBirthday = '1900-01-01';
			$CustomerProvince = '0';
			$CustomerCity = '0';
			$CustomerAddress = '0';
			$CustomerZip = '0';
			$CustomerEmail = $customer->getEmail();
			$CustomerAreaCode = '0';
			$CustomerTele = '0';
			
			if($customer->getMobile()) {
			$CustomerMobile = $customer->getMobile();
			}else{
			$CustomerMobile = '0';
			}
			
			$ParentID = '0';
			$Userrank = '0';
			$Msn = '0';
			$Qq = '0';
			$Officephone = '0';
			$Alias = $customer->getAlias();
			$onlineAdd = "/var/www/sisley/shell/addcustomer.py -c \{\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'CustomerName\':\'".$CustomerName."\',\'CustomerTitle\':\'".$CustomerTitle."\',\'CustomerBirthday\':\'".$CustomerBirthday."\',\'CustomerProvince\':\'".$CustomerProvince."\',\'CustomerCity\':\'".$CustomerCity."\',\'CustomerAddress\':\'".$CustomerAddress."\',\'CustomerZip\':\'".$CustomerZip."\',\'CustomerEmail\':\'".$CustomerEmail."\',\'CustomerAreaCode\':\'".$CustomerAreaCode."\',\'CustomerTele\':\'".$CustomerTele."\',\'CustomerMobile\':\'".$CustomerMobile."\',\'ParentID\':\'".$ParentID."\',\'Userrank\':\'".$Userrank."\',\'Msn\':\'".$Msn."\',\'Qq\':\'".$Qq."\',\'Officephone\':\'".$Officephone."\',\'Alias\':\'".$Alias."\'\}";
			//Mage::log($offlineAdd);
			shell_exec($onlineAdd);
                
               		/***  end insert  ***/
                
                
                return;
            }

            // die happy
            $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
            return;
        }
        catch (Exception $e) {
            // die unhappy
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectError(Mage::getUrl('*/*/index', array('_secure'=>true)));
            return;
        }
    }

    /**
     * Send confirmation link to specified email
     */
    public function confirmationAction()
    {
        $customer = Mage::getModel('customer/customer');
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        // try to confirm by email
        $email = $this->getRequest()->getPost('email');
        if ($email) {
            try {
                $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
                if (!$customer->getId()) {
                    throw new Exception('');
                }
                if ($customer->getConfirmation()) {
                    $customer->sendNewAccountEmail('confirmation');
                    $this->_getSession()->addSuccess($this->__('Please, check your email for confirmation key.'));
                }
                else {
                    $this->_getSession()->addSuccess($this->__('This email does not require confirmation.'));
                }
                $this->_getSession()->setUsername($email);
                $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure' => true)));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('Wrong email.'));
                $this->_redirectError(Mage::getUrl('*/*/*', array('email' => $email, '_secure' => true)));
            }
            return;
        }

        // output form
        $this->loadLayout();

        $this->getLayout()->getBlock('accountConfirmation')
            ->setEmail($this->getRequest()->getParam('email', $email));

        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Forgot customer password page
     */
    public function forgotPasswordAction()
    {
        $this->loadLayout();

        $this->getLayout()->getBlock('forgotPassword')->setEmailValue(
            $this->_getSession()->getForgottenEmail()
        );
        $this->_getSession()->unsForgottenEmail();

        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Forgot customer password action
     */
    public function forgotPasswordPostAction()
    {
        $email = $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $this->_getSession()->addError($this->__('Invalid email address.'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
                return;
            }
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newPassword = $customer->generatePassword();
                    $customer->changePassword($newPassword, false);
                    $customer->setNewpass('初始密码');
                    $customer->save();
                    $customer->sendPasswordReminderEmail();

                    $this->_getSession()->addSuccess($this->__('A new password has been sent.'));

                    $this->getResponse()->setRedirect(Mage::getUrl('*/*'));
                    return;
                }
                catch (Exception $e){
                    $this->_getSession()->addError($e->getMessage());
                }
            }
            else {
                $this->_getSession()->addError($this->__('This email address was not found in our records.'));
                $this->_getSession()->setForgottenEmail($email);
            }
        } else {
            $this->_getSession()->addError($this->__('Please enter your email.'));
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
            return;
        }

        $this->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
    }

    /**
     * Forgot customer account information page
     */
    public function editAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        if ($block = $this->getLayout()->getBlock('customer_edit')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $data = $this->_getSession()->getCustomerFormData(true);
        $customer = $this->_getSession()->getCustomer();
        if (!empty($data)) {
            $customer->addData($data);
        }
        if($this->getRequest()->getParam('changepass')==1){
            $customer->setChangePassword(1);
            $customer->setNewpass('已修改密码');
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('Account Information'));

        $this->renderLayout();
    }

    /**
     * Change customer password action
     */
    public function editPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer/customer')
                ->setId($this->_getSession()->getCustomerId())
                ->setWebsiteId($this->_getSession()->getCustomer()->getWebsiteId());

            $fields = Mage::getConfig()->getFieldset('customer_account');
            $data = $this->_filterPostData($this->getRequest()->getPost());

            foreach ($fields as $code=>$node) {
                if ($node->is('update') && isset($data[$code])) {
                    $customer->setData($code, $data[$code]);
                }
            }

            $errors = $customer->validate();
            if (!is_array($errors)) {
                $errors = array();
            }

            /**
             * we would like to preserver the existing group id
             */
            if ($this->_getSession()->getCustomerGroupId()) {
                $customer->setGroupId($this->_getSession()->getCustomerGroupId());
            }

            if ($this->getRequest()->getParam('change_password')) {
                $currPass = $this->getRequest()->getPost('current_password');
                $newPass  = $this->getRequest()->getPost('password');
                $confPass  = $this->getRequest()->getPost('confirmation');

                if (empty($currPass) || empty($newPass) || empty($confPass)) {
                    $errors[] = $this->__('The password fields cannot be empty.');
                }

                if ($newPass != $confPass) {
                    $errors[] = $this->__('Please make sure your passwords match.');
                }

                $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                if (strpos($oldPass, ':')) {
                    list($_salt, $salt) = explode(':', $oldPass);
                } else {
                    $salt = false;
                }

                if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                    $customer->setPassword($newPass);
                } else {
                    $errors[] = $this->__('Invalid current password');
                }
                
                $customer->setNewpass('已修改');
                    $customer->save();
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;
            }

            try {
                $customer->save();
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('The account information has been saved.'));

					$info = $this->getRequest()->getPost();
					//var_dump($info);
					//exit;
					
					/****  insert online user to CRM  ***/
               
                $OnlineCustomerID = $customer->getId();
				$CustomerName = $customer->getFirstname().$customer->getLastname();
				//$CustomerTitle = '0';
				//$CustomerBirthday = '1900-01-01';
				$CustomerProvince = '0';
				$CustomerCity = '0';
				$CustomerAddress = '0';
				$CustomerZip = '0';
				$CustomerEmail = $customer->getEmail();
				$CustomerAreaCode = '0';
				$CustomerTele = '0';
				/*if($customer->getMobile()) {
				$CustomerMobile = $customer->getMobile();
				}else{
				$CustomerMobile = '0';
				}*/
				$ParentID = '0';
				$Userrank = '0';
				//$Msn = '0';
				//$Qq = '0';
				//$Officephone = '0';
				
				$customerTitle = $info['suffix'];
				$CustomerBirthday = $info['year'].'-'.$info['month'].'-'.$info['day'];
				$CustomerMobile = $info['mobile'];
				
			
				$Msn = $info['MSN'];
				$Qq = $info['QQ'];
				$Officephone = $info['office_phone'];
				
				$Alias = $customer->getAlias();
				$onlineAdd = "/var/www/sisley/shell/addcustomer.py -c \{\'OnlineCustomerID\':\'".$OnlineCustomerID."\',\'CustomerName\':\'".$CustomerName."\',\'CustomerTitle\':\'".$CustomerTitle."\',\'CustomerBirthday\':\'".$CustomerBirthday."\',\'CustomerProvince\':\'".$CustomerProvince."\',\'CustomerCity\':\'".$CustomerCity."\',\'CustomerAddress\':\'".$CustomerAddress."\',\'CustomerZip\':\'".$CustomerZip."\',\'CustomerEmail\':\'".$CustomerEmail."\',\'CustomerAreaCode\':\'".$CustomerAreaCode."\',\'CustomerTele\':\'".$CustomerTele."\',\'CustomerMobile\':\'".$CustomerMobile."\',\'ParentID\':\'".$ParentID."\',\'Userrank\':\'".$Userrank."\',\'Msn\':\'".$Msn."\',\'Qq\':\'".$Qq."\',\'Officephone\':\'".$Officephone."\',\'Alias\':\'".$Alias."\'\}";
				//Mage::log($offlineAdd);
				shell_exec($onlineAdd);
               
                /***  end insert  ***/
					
					
                $this->_redirect('customer/account');
                return;
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

		
		
        $this->_redirect('*/*/edit');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }
}
