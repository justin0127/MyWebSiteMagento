<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Model_Urls extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('socialbookmarking/urls');
    }
    
    protected function _getResource() {
            if (is_null($this->_resource)) {
                    $this->_resource = Mage::getResourceModel('socialbookmarking/urls');
            }
            return $this->_resource;
    }

    public function getCurrentUrl() {
        return preg_replace('/\?___SID=U/','',Mage::helper('core/url')->getCurrentUrl());
    }

    public function loadByUrl() {
        $this->_getResource()->loadByUrl($this,$this->getCurrentUrl());
        return $this;
    }

    public function saveBitlyUrl($url) {

        if(!$this->getBitlyConfig()) return $url;

        $config = Mage::getStoreConfig('socialbookmarking/bitly');

        $bitly = 'http://api.bit.ly/shorten?version='.$config['version'].'&longUrl='.urlencode($url).'&login='.$config['login'].'&apiKey='.$config['key'];

        if($response = @file_get_contents($bitly)) {
                $json = json_decode($response,true);
                if(isset($json['results']) && isset($json['results'][$url]['shortUrl'])) {
                        $this->setUrl($url);
                        $this->setBitly($json['results'][$url]['shortUrl']);
                        $this->save();

                        return $this->getBitly();
                }
        }
        
        return $url;
    }

    public function getBitlyUrl() {
        $url = $this->getCurrentUrl();

        if(!$this->getBitlyConfig()) return $url;

        $_url = $this->loadByUrl($url);

        if($_url->getBitly()) {
            return $_url->getBitly();
        } else {
            return $this->saveBitlyUrl($url);
        }
    }

    public function getBitlyConfig() {
        $config = Mage::getStoreConfig('socialbookmarking/bitly');
        foreach($config as $c => $i) if(empty($i)) return false;

        return true;
    }

}