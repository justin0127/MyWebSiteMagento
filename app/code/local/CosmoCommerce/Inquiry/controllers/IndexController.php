<?php
class CosmoCommerce_Inquiry_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
     $this->loadLayout(array('default'));
     $this->renderLayout();
    }
	
	public function thanksAction()
    {
		 $this->loadLayout(array('default'));
		 $this->renderLayout();
		 $fname =  $this->getRequest()->getParam("fname");
		 $lname =  $this->getRequest()->getParam("lname");
		 $company =  $this->getRequest()->getParam("company");
		 $address =  $this->getRequest()->getParam("address");
		 $address .=  $this->getRequest()->getParam("address2");
		 $city = implode(",",$this->getRequest()->getParam("city")) ;
		 $state =  $this->getRequest()->getParam("state_id");
		 $zip =  $this->getRequest()->getParam("zip");
		 $phone =  $this->getRequest()->getParam("phone");
		 $email =  $this->getRequest()->getParam("email");
		 $website =  $this->getRequest()->getParam("website");
		 $bdesc =  addslashes($this->getRequest()->getParam("bdesc"));
		 $headers = "";
		 $insertArr = array("firstname"=>$fname,"lastname"=>$lname,"company"=>$company,"address"=>$address,"ciry"=>$city,"state"=>$state,"zip"=>$zip,"phone"=>$phone,"email"=>$email,"website"=>$website,"desc"=>$bdesc,"iscustcreated"=>0,"status"=>1,"createddt"=>date('Y-m-d H:i:s'));
		 
		 /*
		 
	 	$adminContent = '新的反馈提交了<br /><table border="0">
							<tr>
									<td>
										<table border="0">
											<tr>
										<td>问题1</td>
										<td>'.$fname.'</td>
									</tr>
									<tr>
										<td>问题2</td>
										<td>'.$lname.'</td>
									</tr>
									<tr>
										<td>问题3</td>
										<td>'.$company.'</td>
									</tr>
									<tr>
										<td>问题4</td>
										<td>'.$address.'</td>
									</tr>
									<tr>
										<td>问题5</td>
										<td>'.$city.'</td>
									</tr>
									<tr>
										<td>问题6</td>
										<td>'.$state.'</td>
									</tr>
									<tr>
										<td>问题7</td>
										<td>'.$zip.'</td>
									</tr>
									<tr>
										<td>问题8</td>
										<td>'.$phone.'</td>
									</tr>
									<tr>
										<td>问题9</td>
										<td>'.$email.'</td>
									</tr>
									</table>
								</td>
							</tr>
						</table>';
		$adminSubject = "新反馈提交";
		$adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		$headers .= 'From: Admin <'.$adminEmail.'>';
		$headers  .= 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		mail($adminEmail,$adminSubject,$adminContent,$headers);
		
		$customerContent = '<table border="0">
									<tr>
										<Td><p>谢谢提交反馈</p></Td>
									</tr>
								</table>';
		$headers = "";
		$custSubject = "谢谢反馈.";
		$headers .= 'From: Admin <'.$adminEmail.'>';
		$headers  .= 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		mail($email,$custSubject,$customerContent,$headers);
		
		*/
		
		$collection = Mage::getModel("inquiry/inquiry");
		$collection->setData($insertArr);
		$collection->save();
		
    }
}
?>
