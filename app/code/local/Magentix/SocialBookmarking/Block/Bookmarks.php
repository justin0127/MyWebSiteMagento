<?php

class Magentix_SocialBookmarking_Block_Bookmarks extends Mage_Core_Block_Template {
    
    public function getBookmarks() {
            $bookmarks = Mage::getModel('socialbookmarking/bookmarks')->getCollection()->addFieldToFilter('status',1);
            $bookmarks->getSelect()->order("position","ASC");
            return $bookmarks;
    }
        
}