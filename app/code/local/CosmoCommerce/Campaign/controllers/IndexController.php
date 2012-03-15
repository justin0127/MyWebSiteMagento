<?php
class CosmoCommerce_Campaign_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$popupemail = strtolower($this->getRequest()->getPost('popupemail'));

		$YzNum = $this->getRequest()->getPost('verif_box');   //post value
		if($popupemail && $YzNum){
			$MdNum = md5($YzNum).'a4xn';    //md5
			$MdCookie = $_COOKIE['tntcon'];   //get cookie
			
			if($MdNum != $MdCookie){
				$this->_redirect('registration?error=code');
				return;
			}else{
			
				
				$_customers = Mage::getModel('customer/customer')->getCollection()
					->addAttributeToSelect('*')
					->addFieldToFilter('email', $popupemail);
				$_campaigns = Mage::getModel('campaign/campaign')->getCollection();
				
				foreach($_campaigns as $_campaign){
					if( strtolower($_campaign->getTitle())==trim(strtolower($popupemail))){
						$this->_redirect('registerfail');
						return;
						break;
					}
				}
					
				if (count($_customers) > 0) {  //same email
				
					$this->_redirect('registerfail');
					return;
				}else{
				
			
					$campaign = Mage::getModel('campaign/campaign');
					$campaign->setTitle($popupemail);
					$campaign->setCreatedTime(date('Y-m-d H:i:s',time()+8*3600));
					
					$campaign->setContent('reg')->save();
					$this->_redirect('registersuccess');
					return;
				}
			}
		}else{
				$this->_redirect('registration');
			return;
		}
		$this->_redirect('registersuccess');
		return;
		$this->loadLayout();     
		$this->renderLayout();
    }
    public function popupAction()
    {

        if ($this->getRequest()->isPost()) {
			$popupemail = strtolower($this->getRequest()->getPost('popupemail'));
			$YzNum = $this->getRequest()->getPost('verif_box');   //post value
			if($popupemail && $YzNum){
				$MdNum = md5($YzNum).'a4xn';    //md5
				$MdCookie = $_COOKIE['tntcon'];   //get cookie
				
				//print_r($popupemail); 
				//print_r($YzNum);
			
				if($MdNum != $MdCookie){
					return $this->getResponse()->setBody('fail');
				}else{
				
					
					$_customers = Mage::getModel('customer/customer')->getCollection()
						->addAttributeToSelect('*')
						->addFieldToFilter('email', $popupemail);
					$_campaigns = Mage::getModel('campaign/campaign')->getCollection();
				
					foreach($_campaigns as $_campaign){
						if( strtolower($_campaign->getTitle())==trim(strtolower($popupemail))){
						
						
							$container='<div style="background:url(/media/popup/popupfail.png);width:676px;height:445px;display:block;text-align:center; ">
<div style="margin-top:320px;float:left;margin-left:293px;"><a class="popupsclosebtn" id="popupsclosebtn" style="cursor:pointer;" ><img src="/media/popup/popupclosebtn.png" width="97" height="30" /></a></div></div>';
							$container.='<script>jQuery("#popupsclosebtn").click( jQuery.unblockUI );</script>';
							$container.='	<script>jQuery("#popupsclosebtn").hover(function(){jQuery("#popupsclosebtn img").attr("src","/media/popup/popupclosebtna.png");},function(){jQuery("#popupsclosebtn img").attr("src","/media/popup/popupclosebtn.png");});</script>';
							return $this->getResponse()->setBody($container);
							return $this->getResponse()->setBody($container);
						}
					}
						
					if (count($_customers) > 0) {  //same email
					
							$container='<div style="background:url(/media/popup/popupfail.png);width:676px;height:445px;display:block;text-align:center; ">
<div style="margin-top:320px;float:left;margin-left:293px;"><a class="popupsclosebtn" id="popupsclosebtn" style="cursor:pointer;" ><img src="/media/popup/popupclosebtna.png" width="97" height="30" /></a></div></div>';
							$container.='<script>jQuery("#popupsclosebtn").click( jQuery.unblockUI );</script>';
							$container.='	<script>jQuery("#popupsclosebtn").hover(function(){jQuery("#popupsclosebtn img").attr("src","/media/popup/popupclosebtna.png");},function(){jQuery("#popupsclosebtn img").attr("src","/media/popup/popupclosebtn.png");});</script>';
							return $this->getResponse()->setBody($container);
							return $this->getResponse()->setBody($container);
					}else{
					
				
						$campaign = Mage::getModel('campaign/campaign');
						$campaign->setTitle($popupemail);
						$campaign->setCreatedTime(date('Y-m-d H:i:s',time()+8*3600));
						$campaign->setContent('popup')->save();
			
						$container='<div style="background:url(/media/popup/popupsuccess.png);width:676px;height:445px;display:block;text-align:center; ">
<div style="margin-top:320px;float:left;margin-left:293px;"><a class="popupsclosebtn" id="popupsclosebtn" style="cursor:pointer;" ><img src="/media/popup/popupclosebtn.png" width="97" height="30" /></a></div></div>';
						$container.='<script>jQuery("#popupsclosebtn").click( jQuery.unblockUI );</script>';
						$container.='	<script>jQuery("#popupsclosebtn").hover(function(){jQuery("#popupsclosebtn img").attr("src","/media/popup/popupclosebtna.png");},function(){jQuery("#popupsclosebtn img").attr("src","/media/popup/popupclosebtn.png");});</script>';
						return $this->getResponse()->setBody($container);
					}
				}
			}else{
				return $this->getResponse()->setBody('fail');
			}
		}
		return $this->getResponse()->setBody('fail');
			
    }
	
    public function verifyajaxAction()
    {

        if ($this->getRequest()->isPost()) {
			$YzNum = $this->getRequest()->getPost('verif_box');   //post value
			
			$popupemail = strtolower($this->getRequest()->getPost('popupemail'));
			if($popupemail && $YzNum){
				$MdNum = md5($YzNum).'a4xn';    //md5
				$MdCookie = $_COOKIE['tntcon'];   //get cookie
				
				if($MdNum != $MdCookie){
					return $this->getResponse()->setBody('codeerror');
				}else{
				
					$_customers = Mage::getModel('customer/customer')->getCollection()
						->addAttributeToSelect('*')
						->addFieldToFilter('email', $popupemail);
					$_campaigns = Mage::getModel('campaign/campaign')->getCollection();
					
					foreach($_campaigns as $_campaign){
						if( strtolower($_campaign->getTitle())==trim(strtolower($popupemail))){
							return $this->getResponse()->setBody('fail');
							break;
						}
					}
						
					if (count($_customers) > 0) {  //same email
					
						return $this->getResponse()->setBody('fail');
					}else{
					
				
						$campaign = Mage::getModel('campaign/campaign');
						$campaign->setTitle($popupemail);
						$campaign->setCreatedTime(date('Y-m-d H:i:s',time()+8*3600));
						$campaign->setContent('reg')->save();
						return $this->getResponse()->setBody('');
						//return $this->getResponse()->setBody('ok');
					}
				}
			
			}
		}
		return $this->getResponse()->setBody('fail');
			
			
			
			
			
			
			
			
			

		$YzNum = $this->getRequest()->getPost('verif_box');   //post value
		if($popupemail && $YzNum){
			$MdNum = md5($YzNum).'a4xn';    //md5
			$MdCookie = $_COOKIE['tntcon'];   //get cookie
			
			if($MdNum != $MdCookie){
				$this->_redirect('registration?error=code');
				return;
			}else{
			
				
				$_customers = Mage::getModel('customer/customer')->getCollection()
					->addAttributeToSelect('*')
					->addFieldToFilter('email', $popupemail);
				$_campaigns = Mage::getModel('campaign/campaign')->getCollection();
				
				foreach($_campaigns as $_campaign){
					if( strtolower($_campaign->getTitle())==trim(strtolower($popupemail))){
						$this->_redirect('registerfail');
						return;
						break;
					}
				}
					
				if (count($_customers) > 0) {  //same email
				
					$this->_redirect('registerfail');
					return;
				}else{
				
			
					$campaign = Mage::getModel('campaign/campaign');
					$campaign->setTitle($popupemail);
					$campaign->setCreatedTime(date('Y-m-d H:i:s',time()+8*3600));
					$campaign->setContent('reg')->save();
					$this->_redirect('registersuccess');
					return;
				}
			}
		}else{
				$this->_redirect('registration');
			return;
		}
		$this->_redirect('registersuccess');			
			
			
			
			
			
			
			
			
			
			
			
    }
}