<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Block_Adminhtml_Widget_Grid_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $row) {
		return ($this->_getImage($row));
	}
	
	protected function _getImage(Varien_Object $row) {
		$img = $row->image != '' ? '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$row->image.'" alt="" />' : '';
		return $img;
	}
	
}