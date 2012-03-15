<?php

include("MaxMind/GeoIP/geoip.inc");
include("MaxMind/GeoIP/geoipcity.inc");
include("MaxMind/GeoIP/geoipregionvars.php");

class TM_FireCheckout_Model_Type_Standard
{
    /**
     * Checkout types: Checkout as Guest, Register, Logged In Customer
     */
    const METHOD_GUEST    = 'guest';
    const METHOD_REGISTER = 'register';
    const METHOD_CUSTOMER = 'customer';

    /**
     * Error message of "customer already exists"
     *
     * @var string
     */
    private $_customerEmailExistsMessage = '';

    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Mage_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * @var Mage_Checkout_Helper_Data
     */
    protected $_helper;

    /**
     * Class constructor
     * Set customer already exists message
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('checkout');
        $this->_customerEmailExistsMessage = $this->_helper->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.');
        $this->_checkoutSession = Mage::getSingleton('checkout/session');
        $this->_quote = $this->_checkoutSession->getQuote();
        $this->_customerSession = Mage::getSingleton('customer/session');
    }

    /**
     * Get frontend checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return $this->_checkoutSession;
    }

    /**
     * Quote object getter
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Get customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }


    /**
     * Retrieve shipping and billing addresses,
     * and boolean flag about their equality
     *
     * For the Registerd customer with available addresses returns
     * appropriate address.
     * For the Guest trying to detect country with geo-ip technology
     *
     * @return array
     */
    private function _getDefaultAddress()
    {
        $result = array(
            'shipping' => array(
                'country_id'   => null,
                'city'      => null,
                'region_id' => null,
                'postcode'  => null
            ),
            'billing' => array(
                'country_id'   => null,
                'city'      => null,
                'region_id' => null,
                'postcode'  => null
            ),
            'equal' => true
        );
        if (($customer = Mage::getSingleton('customer/session')->getCustomer())
            && ($addresses = $customer->getAddresses())) {

            if (!$shippingAddress = $customer->getPrimaryShippingAddress()) {
                foreach ($addresses as $address) {
                    $shippingAddress = $address;
                    break;
                }
            }
            if (!$billingAddress = $customer->getPrimaryBillingAddress()) {
                foreach ($addresses as $address) {
                    $billingAddress = $address;
                    break;
                }
            }

            $result['shipping']['country_id'] = $shippingAddress->getCountryId();
            $result['billing']['country_id'] = $billingAddress->getCountryId();
            $result['equal'] = $shippingAddress->getId() === $billingAddress->getId();
        } else {
            $result['equal'] = true;

            if (Mage::getStoreConfig('firecheckout/geo_ip/country')) {
                $gi = geoip_open(
                    Mage::getBaseDir('lib')
                        . DS
                        . "MaxMind/GeoIP/data/"
                        . Mage::getStoreConfig('firecheckout/geo_ip/country_file'),
                    GEOIP_STANDARD
                );

                $result['shipping']['country_id'] =
                    $result['billing']['country_id'] = geoip_country_code_by_addr(
                        $gi, Mage::helper('core/http')->getRemoteAddr()
                    );

                geoip_close($gi);
            }
            if (Mage::getStoreConfig('firecheckout/geo_ip/city')) {
                $gi = geoip_open(
                    Mage::getBaseDir('lib')
                        . DS
                        . "MaxMind/GeoIP/data/"
                        . Mage::getStoreConfig('firecheckout/geo_ip/city_file'),
                    GEOIP_STANDARD
                );

                $record = geoip_record_by_addr($gi, Mage::helper('core/http')->getRemoteAddr());
                $result['shipping']['city'] =
                    $result['billing']['city'] = $record->city;
                $result['shipping']['postcode'] =
                    $result['billing']['postcode'] = $record->postal_code;

                geoip_close($gi);
            }
            if (empty($result['shipping']['country_id'])) {
                $result['shipping']['country_id'] =
                    $result['billing']['country_id'] = Mage::getStoreConfig('firecheckout/general/country');
            }
        }

        return $result;
    }

    /**
     * @param object $method
     * @return boolean
     */
    protected function _canUsePaymentMethod($method)
    {
        if (!$method->canUseForCountry($this->getQuote()->getBillingAddress()->getCountry())) {
            return false;
        }

        /*if (!$method->canUseForCurrency(Mage::app()->getStore()->getBaseCurrencyCode())) {
            return false;
        }*/

        $total = $this->getQuote()->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
    }

    /**
     * Set the default values at the start of payment process
     *
     * @return TM_FireCheckout_Model_Type_Standard
     */
    public function applyDefaults()
    {
        $addressInfo = $this->_getDefaultAddress();

        //if (!$this->getQuote()->getBillingAddress()->getCountryId()) {
            $result = $this->saveBilling(array(
                'country_id'        => $addressInfo['billing']['country_id'],
                'city'              => $addressInfo['billing']['city'],
                'region_id'         => $addressInfo['billing']['region_id'],
                'postcode'          => $addressInfo['billing']['postcode'],
                'use_for_shipping'  => $addressInfo['equal'],
                'register_account'  => 0
            ), false, false);
        //}

        //if (!$this->getQuote()->getShippingAddress()->getCountryId()) {
            if (!$addressInfo['equal']) {
                $result = $this->saveShipping(array(
                    'country_id'        => $addressInfo['shipping']['country_id'],
                    'city'              => $addressInfo['shipping']['city'],
                    'region_id'         => $addressInfo['shipping']['region_id'],
                    'postcode'          => $addressInfo['shipping']['postcode']
                ), false, false);
            }
        //}

        $this->getQuote()->collectTotals()->save();

        $this->applyShippingMethod();
        $this->applyPaymentMethod();

        //$this->getQuote()->setTotalsCollectedFlag(false);

        return $this;
    }

    /**
     * Update payment method information
     * Removes previously selected method if none is available,
     * set available if only one is available,
     * set previously selected payment,
     * set default from config if possible
     *
     * @param string $methodCode Default method code
     * @return TM_FireCheckout_Model_Type_Standard
     */
    public function applyPaymentMethod($methodCode = null)
    {
        //$this->getQuote()->collectTotals()->save();

        $store = $this->getQuote() ? $this->getQuote()->getStoreId() : null;
        $methods = Mage::helper('payment')->getStoreMethods($store, $this->getQuote());
        $availablePayments = array();
        foreach ($methods as $key => $method) {
            if ($this->_canUsePaymentMethod($method)) {
                $availablePayments[] = $method;
            }
        }

        if (!$count = count($availablePayments)) {
            $this->getQuote()->removePayment();
        } elseif (1 === $count) {
            $payment = $this->getQuote()->getPayment();
            $payment->setMethod($availablePayments[0]->getCode());
            $method = $payment->getMethodInstance();
            $method->assignData(array('method' => $availablePayments[0]->getCode()));
        } else {
            $found = false;
            if (!$methodCode) {
                if ($this->getQuote()->isVirtual()) {
                    $methodCode = $this->getQuote()->getBillingAddress()->getPaymentMethod();
                } else {
                    $methodCode = $this->getQuote()->getShippingAddress()->getPaymentMethod();
                }
            }
            if ($methodCode) {
                foreach ($availablePayments as $payment) {
                    if ($methodCode !== $payment->getCode()) {
                        continue;
                    }

                    $payment = $this->getQuote()->getPayment();
                    $payment->setMethod($methodCode);
                    $method = $payment->getMethodInstance();
                    $method->assignData(array('method' => $methodCode));
                    $found = true;

                    break;
                }
            }
            if (!$found || !$methodCode) {
                $methodCode = Mage::getStoreConfig('firecheckout/general/payment_method');
                foreach ($availablePayments as $payment) {
                    if ($methodCode !== $payment->getCode()) {
                        continue;
                    }

                    $payment = $this->getQuote()->getPayment();
                    $payment->setMethod($methodCode);
                    $method = $payment->getMethodInstance();
                    $method->assignData(array('method' => $methodCode));
                    $found = true;

                    break;
                }
            }
            if (!$found) {
                 $this->getQuote()->removePayment();
            }
        }

        //$this->getQuote()->setTotalsCollectedFlag(false);
        return $this;
    }

    /**
     * Update shipping method information
     * Removes previously selected method if none is available,
     * set available if only one is available,
     * set previously selected payment,
     * set default from config if possible
     *
     * @param string $methodCode Default method code
     * @return TM_FireCheckout_Model_Type_Standard
     */
    public function applyShippingMethod($methodCode = null)
    {
        $rates = Mage::getModel('sales/quote_address_rate')->getCollection()
            ->setAddressFilter($this->getQuote()->getShippingAddress()->getId())
            ->toArray();

        if (!$count = count($rates['items'])) {
            $this->getQuote()->getShippingAddress()->setShippingMethod(false);
        } elseif (1 === $count) {
            $this->getQuote()->getShippingAddress()->setShippingMethod($rates['items'][0]['code']);
        } else {
            $found = false;
            if (!$methodCode) {
                $methodCode = $this->getQuote()->getShippingAddress()->getShippingMethod();
            }
            if ($methodCode) {
                foreach ($rates['items'] as $rate) {
                    if ($methodCode === $rate['code']) {
                        $this->getQuote()->getShippingAddress()->setShippingMethod($methodCode);
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found || !$methodCode) {
                $methodCode = Mage::getStoreConfig('firecheckout/general/shipping_method');
                foreach ($rates['items'] as $rate) {
                    if ($methodCode === $rate['code']) {
                        $this->getQuote()->getShippingAddress()->setShippingMethod($methodCode);
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) {
                $this->getQuote()->getShippingAddress()->setShippingMethod(false);
            }
        }
        return $this;
    }

    /**
     * Initialize quote state to be valid for one page checkout
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function initCheckout()
    {
        $checkout = $this->getCheckout();
        $customerSession = $this->getCustomerSession();

        /**
         * Reset multishipping flag before any manipulations with quote address
         * addAddress method for quote object related on this flag
         */
        if ($this->getQuote()->getIsMultiShipping()) {
            $this->getQuote()->setIsMultiShipping(false);
            $this->getQuote()->save();
        }

        /*
        * want to laod the correct customer information by assiging to address
        * instead of just loading from sales/quote_address
        */
        $customer = $customerSession->getCustomer();
        if ($customer) {
            $this->getQuote()->assignCustomer($customer);
        }
        return $this;
    }

    /**
     * Get quote checkout method
     *
     * @return string
     */
    public function getCheckoutMethod()
    {
        if ($this->getCustomerSession()->isLoggedIn()) {
            return self::METHOD_CUSTOMER;
        }
        if (!$this->getQuote()->getCheckoutMethod()) {
            if (Mage::helper('firecheckout')->isAllowedGuestCheckout()) {
                $this->getQuote()->setCheckoutMethod(self::METHOD_GUEST);
            } else {
                $this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
            }
        }
        return $this->getQuote()->getCheckoutMethod();
    }

    /**
     * Specify checkout method
     *
     * @param   string $method
     * @return  array
     */
    public function saveCheckoutMethod($method)
    {
        if (empty($method)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }

        $this->getQuote()->setCheckoutMethod($method)->save();
        return array();
    }

    /**
     * Get customer address by identifier
     *
     * @param   int $addressId
     * @return  Mage_Customer_Model_Address
     */
    public function getAddress($addressId)
    {
        $address = Mage::getModel('customer/address')->load((int)$addressId);
        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $address->setRegion($address->getRegionId());
        }
        return $address;
    }

    /**
     * Save billing address information to quote
     * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveBilling($data, $customerAddressId, $validate = true)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }

        $address = $this->getQuote()->getBillingAddress();
        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => $this->_helper->__('Customer Address is not valid.')
                    );
                }
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            unset($data['address_id']);
            $address->addData($data);
        }

        if ($validate && (($validateRes = $this->validateAddress($address)) !== true)) {
            return array('error' => 1, 'message' => $validateRes);
        }

        if (isset($data['register_account']) && $data['register_account']) {
            $this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
        } else if ($this->getCustomerSession()->isLoggedIn()) {
            $this->getQuote()->setCheckoutMethod(self::METHOD_CUSTOMER);
        } else {
            $this->getQuote()->setCheckoutMethod(self::METHOD_GUEST);
        }

        if ($validate && !$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
                return array('error' => 1, 'message' => $this->_customerEmailExistsMessage);
            }
        }

        $address->implodeStreetAddress();

        if (!$this->getQuote()->isVirtual()) {
            /**
             * Billing address using otions
             */
            $usingCase = isset($data['use_for_shipping']) ? (int) $data['use_for_shipping'] : 0;

            switch($usingCase) {
                case 0:
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shipping->setSameAsBilling(0);
                    break;
                case 1:
                    $billing = clone $address;
                    $billing->unsAddressId()->unsAddressType()->unsBaseSubtotal();
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shippingMethod = $shipping->getShippingMethod();
                    $shipping->addData($billing->getData())
                        ->setSameAsBilling(1)
                        ->setShippingMethod($shippingMethod)
                        ->setCollectShippingRates(true);
                    break;
            }
        }

        if ($validate && (true !== $result = $this->_processValidateCustomer($address))) {
            return $result;
        }

        return array();
    }

    /**
     * Validate customer data and set some its data for further usage in quote
     * Will return either true or array with error messages
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return true|array
     */
    protected function _processValidateCustomer(Mage_Sales_Model_Quote_Address $address)
    {
        // set customer date of birth for further usage
        $dob = '';
        if ($address->getDob()) {
            $dob = Mage::app()->getLocale()->date($address->getDob(), null, null, false)->toString('yyyy-MM-dd');
            $this->getQuote()->setCustomerDob($dob);
        }

        // set customer tax/vat number for further usage
        if ($address->getTaxvat()) {
            $this->getQuote()->setCustomerTaxvat($address->getTaxvat());
        }

        // set customer gender for further usage
        if ($address->getGender()) {
            $this->getQuote()->setCustomerGender($address->getGender());
        }

        // invoke customer model, if it is registering
        if (self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            // set customer password hash for further usage
            $customer = Mage::getModel('customer/customer');
            $password = $address->getCustomerPassword();
            if (empty($password)) {
                $password = $customer->generatePassword();
                $address->setCustomerPassword($password);
                $address->setConfirmPassword($password);
            }
            $this->getQuote()->setPasswordHash($customer->encryptPassword($password));

            // validate customer
            foreach (array(
                'firstname'    => 'firstname',
                'lastname'     => 'lastname',
                'email'        => 'email',
                'password'     => 'customer_password',
                'confirmation' => 'confirm_password',
                'taxvat'       => 'taxvat',
                'gender'       => 'gender',
            ) as $key => $dataKey) {
                $customer->setData($key, $address->getData($dataKey));
            }
            if ($dob) {
                $customer->setDob($dob);
            }
            $validationResult = $customer->validate();
            if (true !== $validationResult && is_array($validationResult)) {
                return array(
                    'error'   => -1,
                    'message' => implode(', ', $validationResult)
                );
            }
        } elseif(self::METHOD_GUEST == $this->getQuote()->getCheckoutMethod()) {
            $email = $address->getData('email');
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                return array(
                    'error'   => -1,
                    'message' => $this->_helper->__('Invalid email address "%s"', $email)
                );
            }
        }

        return true;
    }

    /**
     * Save checkout shipping address
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveShipping($data, $customerAddressId, $validate = true)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        $address = $this->getQuote()->getShippingAddress();

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => $this->_helper->__('Customer Address is not valid.')
                    );
                }
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            unset($data['address_id']);
            $address->addData($data);
        }
        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        if ($validate && (($validateRes = $this->validateAddress($address)) !== true)) {
            return array('error' => 1, 'message' => $validateRes);
        }

        return array();
    }

    /**
     * Specify quote shipping method
     *
     * @param   string $shippingMethod
     * @return  array
     */
    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid shipping method.'));
        }
        $rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid shipping method.'));
        }
        $this->getQuote()->getShippingAddress()
            ->setShippingMethod($shippingMethod);
        $this->getQuote()->collectTotals()
            ->save();

        return array();
    }

    /**
     * Specify quote payment method
     *
     * @param   array $data
     * @return  array
     */
    public function savePayment($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        if ($this->getQuote()->isVirtual()) {
            $this->getQuote()->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $this->getQuote()->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        $payment = $this->getQuote()->getPayment();
        $payment->importData($data);

        $this->getQuote()->save();

        return array();
    }

    /**
     * Validate quote state to be integrated with obe page checkout process
     */
    public function validate()
    {
        $helper = Mage::helper('checkout');
        $quote  = $this->getQuote();
        if ($quote->getIsMultiShipping()) {
            Mage::throwException($helper->__('Invalid checkout type.'));
        }

        if ($quote->getCheckoutMethod() == self::METHOD_GUEST && !Mage::helper('firecheckout')->isAllowedGuestCheckout()) {
            Mage::throwException($this->_helper->__('Sorry, guest checkout is not enabled. Please try again or contact store owner.'));
        }
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _prepareGuestQuote()
    {
        $quote = $this->getQuote();
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * Prepare quote for customer registration and customer order submit
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _prepareNewCustomerQuote()
    {
        $quote      = $this->getQuote();
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        //$customer = Mage::getModel('customer/customer');
        $customer = $quote->getCustomer();
        /* @var $customer Mage_Customer_Model_Customer */
        $customerBilling = $billing->exportCustomerAddress();
        $customer->addAddress($customerBilling);
        $billing->setCustomerAddress($customerBilling);
        $customerBilling->setIsDefaultBilling(true);
        if ($shipping && !$shipping->getSameAsBilling()) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
            $customerShipping->setIsDefaultShipping(true);
        } elseif ($shipping) {
            $customerBilling->setIsDefaultShipping(true);
        }
        /**
         * @todo integration with dynamica attributes customer_dob, customer_taxvat, customer_gender
         */
        if ($quote->getCustomerDob() && !$billing->getCustomerDob()) {
            $billing->setCustomerDob($quote->getCustomerDob());
        }

        if ($quote->getCustomerTaxvat() && !$billing->getCustomerTaxvat()) {
            $billing->setCustomerTaxvat($quote->getCustomerTaxvat());
        }

        if ($quote->getCustomerGender() && !$billing->getCustomerGender()) {
            $billing->setCustomerGender($quote->getCustomerGender());
        }

        Mage::helper('core')->copyFieldset('checkout_onepage_billing', 'to_customer', $billing, $customer);
        $customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
        $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
        $quote->setCustomer($customer)
            ->setCustomerId(true);
    }

    /**
     * Prepare quote for customer order submit
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _prepareCustomerQuote()
    {
        $quote      = $this->getQuote();
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $this->getCustomerSession()->getCustomer();
        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $customerBilling = $billing->exportCustomerAddress();
            $customer->addAddress($customerBilling);
            $billing->setCustomerAddress($customerBilling);
        }
        if ($shipping && ((!$shipping->getCustomerId() && !$shipping->getSameAsBilling())
            || (!$shipping->getSameAsBilling() && $shipping->getSaveInAddressBook()))) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
        }

        if (isset($customerBilling) && !$customer->getDefaultBilling()) {
            $customerBilling->setIsDefaultBilling(true);
        }
        if ($shipping && isset($customerShipping) && !$customer->getDefaultShipping()) {
            $customerShipping->setIsDefaultShipping(true);
        } elseif (isset($customerBilling) && !$customer->getDefaultShipping()) {
            $customerBilling->setIsDefaultShipping(true);
        }
        $quote->setCustomer($customer);
    }

    /**
     * Involve new customer to system
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _involveNewCustomer()
    {
        $customer = $this->getQuote()->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation');
            $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
            $this->getCustomerSession()->addSuccess(
                Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url)
            );
        } else {
            $customer->sendNewAccountEmail();
            $this->getCustomerSession()->loginById($customer->getId());
        }
        return $this;
    }

    /**
     * Create order based on checkout type. Create customer if necessary.
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function saveOrder($shipping_time='', $fapiao='')
    {
        $this->validate();
        $isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case self::METHOD_GUEST:
                $this->_prepareGuestQuote();
                break;
            case self::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }

        /**
         * @var TM_FireCheckout_Model_Service_Quote
         */
        $service = Mage::getModel('firecheckout/service_quote', $this->getQuote());
        $service->submitAll();

        if ($isNewCustomer) {
            try {
                $this->_involveNewCustomer();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
            ->setLastSuccessQuoteId($this->getQuote()->getId())
            ->clearHelperData()
        ;

        $order = $service->getOrder();
        
        
        if ($order) {
        
        $order->setShippingTime($shipping_time);
        $order->setFapiao_title($fapiao);
        $order->save();
        
            Mage::dispatchEvent('checkout_type_onepage_save_order_after', array('order'=>$order, 'quote'=>$this->getQuote()));

            /**
             * a flag to set that there will be redirect to third party after confirmation
             * eg: paypal standard ipn
             */
            $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
            /**
             * we only want to send to customer about new order when there is no redirect to third party
             */
            if(!$redirectUrl){
                try {
                    $order->sendNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            // add order information to the session
            $this->_checkoutSession->setLastOrderId($order->getId())
                ->setRedirectUrl($redirectUrl)
                ->setLastRealOrderId($order->getIncrementId());

            // as well a billing agreement can be created
            $agreement = $order->getPayment()->getBillingAgreement();
            if ($agreement) {
                $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
            }
        }

        // add recurring profiles information to the session
        $profiles = $service->getRecurringPaymentProfiles();
        if ($profiles) {
            $ids = array();
            foreach($profiles as $profile) {
                $ids[] = $profile->getId();
            }
            $this->_checkoutSession->setLastRecurringProfileIds($ids);
            // TODO: send recurring profile emails
        }

        return $this;
    }

    /**
     * Validate quote state to be able submited from one page checkout page
     *
     * @deprecated after 1.4 - service model doing quote validation
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function validateOrder()
    {
        $helper = Mage::helper('checkout');
        if ($this->getQuote()->getIsMultiShipping()) {
            Mage::throwException($helper->__('Invalid checkout type.'));
        }

        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addressValidation = $this->validateAddress($address);
            if ($addressValidation !== true) {
                Mage::throwException($helper->__('Please check shipping address information.'));
            }
            $method= $address->getShippingMethod();
            $rate  = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException($helper->__('Please specify shipping method.'));
            }
        }

        $addressValidation = $this->validateAddress($this->getQuote()->getBillingAddress());
        if ($addressValidation !== true) {
            Mage::throwException($helper->__('Please check billing address information.'));
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException($helper->__('Please select valid payment method.'));
        }
    }

    /**
     * Check if customer email exists
     *
     * @param string $email
     * @param int $websiteId
     * @return false|Mage_Customer_Model_Customer
     */
    protected function _customerEmailExists($email, $websiteId = null)
    {
        $customer = Mage::getModel('customer/customer');
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    /**
     * Get last order increment id by order id
     *
     * @return string
     */
    public function getLastOrderId()
    {
        $lastId  = $this->getCheckout()->getLastOrderId();
        $orderId = false;
        if ($lastId) {
            $order = Mage::getModel('sales/order');
            $order->load($lastId);
            $orderId = $order->getIncrementId();
        }
        return $orderId;
    }

    public function validateAddress($address)
    {
        $errors = array();
        $helper = Mage::helper('customer');
        $address->implodeStreetAddress();
        $formConfig = Mage::getStoreConfig('firecheckout/address_form');

        if (!Zend_Validate::is($address->getFirstname(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the first name.');
        }
        if (!Zend_Validate::is($address->getLastname(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the last name.');
        }

        if ('required' === $formConfig['company']
            && !Zend_Validate::is($address->getCompany(), 'NotEmpty')) {

            $errors[] = $helper->__('Please enter the company.'); // translate
        }

        if ('required' === $formConfig['address']
            && !Zend_Validate::is($address->getStreet(1), 'NotEmpty')) {

            $errors[] = $helper->__('Please enter the street.');
        }

        if ('required' === $formConfig['city']
            && !Zend_Validate::is($address->getCity(), 'NotEmpty')) {

            $errors[] = $helper->__('Please enter the city.');
        }

        if ('required' === $formConfig['phone']
            && !Zend_Validate::is($address->getTelephone(), 'NotEmpty')) {

            $errors[] = $helper->__('Please enter the telephone number.');
        }

        if ('required' === $formConfig['fax']
            && !Zend_Validate::is($address->getFax(), 'NotEmpty')) {

            $errors[] = $helper->__('Please enter the fax.'); // translate
        }

        $_havingOptionalZip = Mage::helper('directory')->getCountriesWithOptionalZip();
        if ('required' === $formConfig['zip']
            && !in_array($address->getCountryId(), $_havingOptionalZip)
            && !Zend_Validate::is($address->getPostcode(), 'NotEmpty')) {

            $errors[] = $helper->__('Please enter the zip/postal code.');
        }

        if ('required' === $formConfig['country']
            && !Zend_Validate::is($address->getCountryId(), 'NotEmpty')) {

            $errors[] = $helper->__('Please enter the country.');
        }

        if ('required' === $formConfig['state']
            && $address->getCountryModel()->getRegionCollection()->getSize()
            && !Zend_Validate::is($address->getRegionId(), 'NotEmpty')) {

            $errors[] = $helper->__('Please enter the state/province.');
        }

        if (empty($errors) || $address->getShouldIgnoreValidation()) {
            return true;
        }
        return $errors;
    }
}
