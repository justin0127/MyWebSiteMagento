<?php

class TM_FireCheckout_Model_Observer
{
    public function setCustomerComment($data)
    {
        $comment = trim(Mage::getSingleton('customer/session')->getOrderCustomerComment());
        if (!empty($comment)) {
            $data['order']->addStatusHistoryComment($comment)
                ->setIsVisibleOnFront(true)
                ->setIsCustomerNotified(false);
        }
    }

    public function unsetCustomerComment()
    {
        Mage::getSingleton('customer/session')->setOrderCustomerComment(null);
    }
}
