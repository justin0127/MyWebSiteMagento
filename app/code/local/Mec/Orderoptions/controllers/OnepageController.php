<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';
class Mec_Orderoptions_OnepageController extends Mage_Checkout_OnepageController
{

    public function saveOrderAction()
    {
    
         $this->_expireAjax();
        
        $result = array();
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all Terms and Conditions before placing the order.');
                    $this->getResponse()->setBody(Zend_Json::encode($result));
                    return;
                }
            }
            if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }
            
            //yaozer
            $shipping_time = $this->getRequest()->getPost('deliveryTime', false);
            $fapiao = $this->getRequest()->getPost('fapiao', '^^^');
            //Mage::log('in saveOrderAction : '. $shipping_time. $fapiao);
            
            $shipping_time_chinese = array('all'=>'送货时间：不限制', 'holidays'=>'送货时间：限节假日','workdays'=>'送货时间：限工作日','nights'=>'送货时间：学校地址');
            
            if (array_key_exists($shipping_time, $shipping_time_chinese)) {
    		 $shipping_time = $shipping_time_chinese[$shipping_time];  //  translate into chinese,  or false for no translation.
	   }
            
            $this->getOnepage()->saveOrder($shipping_time, $fapiao);
            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error']   = false;
            
            
        }
        catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }

            $this->getOnepage()->getQuote()->save();
        }
        catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
            $this->getOnepage()->getQuote()->save();
        }

        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

}
