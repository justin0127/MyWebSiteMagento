<?php

class J2t_RememberMe_Model_Cookie extends Mage_Core_Model_Cookie
{
    const XML_PATH_COOKIE_LIFETIME_LONG  = 'web/cookie2/cookie_lifetime2';

    public function getLifetime()
    {
    if (isset($_POST['rememberme'])){
    $lifetime = Mage::getStoreConfig(self::XML_PATH_COOKIE_LIFETIME_LONG, $this->getStore());
    return $lifetime;
    }else{
        if (null !== $this->_lifetime) {
            $lifetime = $this->_lifetime;
        }
        else {
            $lifetime = Mage::getStoreConfig(self::XML_PATH_COOKIE_LIFETIME, $this->getStore());
        }
        if (!is_numeric($lifetime)) {
            $lifetime = 3600;
        }
        return $lifetime;
    }
    }


}

?>
