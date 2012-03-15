<?php
class MEC_Chinapay_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('chinapay/form.phtml');
        parent::_construct();
    }

}
