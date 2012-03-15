<?php
class Mec_Reviews_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function encrypt($str){
        //return base64_encode($str);
        return Mage::helper('core')->encrypt($str);
    }
    
    public function decrypt($str){
        //return base64_decode($str);
        return Mage::helper('core')->decrypt($str);
    }
} 
