<?php

/**
 * Class ADM_AbandonedCart_Model_Followup
 *
 * @method int getFollowupId()
 * @method $this setFollowupId(int $value)
 * @method int getQuoteId()
 * @method $this setQuoteId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getAbandonedAt()
 * @method $this setAbandonedAd(string $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getCustomerEmail()
 * @method $this setCustomerEmail(string $value)
 * @method float getBaseGrandTotal()
 * @method $this setBaseGrandTotal(float $value)
 * @method string getCurrency()
 * @method $this setCurrency(string $value)
 * @method string getCouponCode()
 * @method $this setCouponCode(string $value)
 * @method string getSecretCode()
 * @method $this setSecretCode(string $value)
 * @method int getStatus()
 * @method $this setStatus(int $value)
 * @method int getOffset()
 * @method $this setOffset(int $value)
 * @method string getMailScheduledAt()
 * @method $this setMailScheduledAt(string $value)
 * @method int getOrderId()
 * @method $this setOrderId(int $value)
 * @method string getOrderCouponCode()
 * @method $this setOrderCouponCode(string $value)
 * @method bool getQuoteStillActive() Virtual field added by the collection load code
 */
class ADM_AbandonedCart_Model_Followup extends Mage_Core_Model_Abstract
{
    const XML_PATH_EMAIL_IDENTITY        = 'abandonedcart/general/email_identity';
    const XML_PATH_EMAIL_COPY_TO         = 'abandonedcart/general/email_copy_to';

    const XML_PATH_TEST_MODE             = 'abandonedcart/general/test_mode';
    const XML_PATH_TEST_EMAIL            = 'abandonedcart/general/test_email';

    const ENTITY                         = 'abandonedcart';

    const EMAIL_EVENT_NAME               = 'send_followup';

    /**
     * @var ADM_AbandonedCart_Model_Tracker|null
     */
    protected $trackerObject = null;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'abandoned_cart_followup';

    protected function _construct()
    {
        $this->_init('adm_abandonedcart/followup');
        return parent::_construct();
    }

    public function tryToRestoreCart($restoreCode = '')
    {
        $tracker = $this->getTrackerObject();
        $return = [];

        if (!$this->getId()) {
            $return['error'] = true;
            $return['message'] = 'Unknown followup';
            return $return;
        } elseif (empty($restoreCode) || $this->getSecretCode() != $restoreCode) {
            $return['error'] = true;
            $return['message'] = 'Restore code is invalid';
            $tracker->setEventError(ADM_AbandonedCart_Model_Tracker::ERR_WRONG_CODE);
        } elseif ($this->getSession()->isLoggedIn()) {
            if ($this->getCustomerId() == $this->getSession()->getCustomerId()) {
                if ($this->getCurrentQuoteId() == $this->getQuoteId()) {
                    $tracker->setEventSuccess(ADM_AbandonedCart_Model_Tracker::OK_LOG_USER_WITH_CART);
                } else {
                    $return['error'] = true;
                    $return['message'] = 'Cannot restore current cart';
                    $tracker->setEventError(ADM_AbandonedCart_Model_Tracker::ERR_USER_LOGGED);
                }
            } else {
                $return['error'] = true;
                $return['message'] = 'Cart to restore do not belong to current user';
                $tracker->setEventError(ADM_AbandonedCart_Model_Tracker::ERR_WRONG_USER_LOGGED);
            }
        } else {
            if ($this->getCustomerId()) {
                $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
                //TODO: Check config to know what to do
                if ($customer->getId()) {
                    //TODO: Detect if cart is still the same
                    Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
                    $tracker->setEventSuccess(ADM_AbandonedCart_Model_Tracker::OK_LOG_USER);
                }
            } elseif ($this->getQuoteId()) {
                //TODO: Detect if cart is still the same
                $quote = Mage::getModel('sales/quote')->load($this->getQuoteId());
                if ($quote) {
                    Mage::getSingleton('checkout/session')->replaceQuote($quote);
                    $tracker->setEventSuccess(ADM_AbandonedCart_Model_Tracker::OK_RESTORE_CART);
                }
            } else {
                $tracker->setEventError(ADM_AbandonedCart_Model_Tracker::ERR_NO_INFO);
                $return['error'] = true;
                $return['message'] = 'No info';
            }
        }

        $this->save();

        return $return;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        if (!$this->hasData('redirect_url') && $this->getCurrentQuoteId()) {
            return 'checkout/cart';
        } else {
            return $this->getData('redirecturl');
        }
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getRestoreUrl()
    {
        /** @var $store Mage_Core_Model_Store */
        $store = Mage::app()->getStore($this->getStoreId());
        $params = [
            'id' => $this->getId(),
            'offset' => $this->getOffset()+1,
            'restore_code' => $this->getSecretCode(),
            '_nosid' => true,
        ];
        $params = array_merge($params, Mage::helper('adm_abandonedcart')->getUtmParams($this->getStoreId()));

        return $store->getUrl('abandonedcart/restore/cart', $params);
    }

    /**
     * @param Mage_Core_Model_Store $store
     *
     * @return int
     */
    public function registerAbandonedCart($store)
    {
        $delays = Mage::helper('adm_abandonedcart')->getOffsetDelays($store);
        if (!$delays) {
            return 0;
        }

        $minDelay = 100000;
        $maxDelay = 0;
        foreach ($delays as $delay) {
            $minDelay = min($minDelay, $delay);
            $maxDelay = max($maxDelay, $delay);
        }

        //If there is a single delay set there is an issue: the query to load all abandoned carts would look for quotes
        //having an updated_at timestamp that is EXACTLY equal to the current timestamp minus the set delay. In most
        //circumstances this will prevent any quote from being found.
        //To solve this, artificially widen the interval in this case, so that quotes can be found.
        if ($maxDelay == $minDelay) {
            $maxDelay = 2 * $minDelay;
        }

        return $this->getResource()->registerAbandonedCart($store, $minDelay, $maxDelay);
    }

    /**
     * @param bool $force
     *
     * @return $this
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    public function sendMail($force = false)
    {
        $mailSent = false;
        $error    = false;

        $quote = Mage::getModel('sales/quote')
            ->setStore(Mage::app()->getStore($this->getStoreId()))
            ->load($this->getQuoteId());

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($quote->getStoreId());

        $templateId = $force ?
            Mage::getStoreConfig('abandonedcart/admin/template', $this->getStoreId()) :
            $this->getTemplate();

        if (empty($templateId)) {
            $error = ADM_AbandonedCart_Model_Tracker::ERR_NO_TEMPLATE;
        }

        if (!$quote->getId()) {
            $error = ADM_AbandonedCart_Model_Tracker::ERR_NO_QUOTE;
        }

        if (!$force && !$this->getQuoteStillActive()) {
            $error = ADM_AbandonedCart_Model_Tracker::ERR_NO_QUOTE_ACTIVE;
        }

        if (!$error) {
            try {
                //Start environment emulation of the specified store
                $mailSent = $this->sendMailInner($templateId, $quote);
            } catch (Exception $e) {
                Mage::logException($e);
            } finally {
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            }
        }

        $this->setMailSent($mailSent, $error);

        return $this->save();
    }

    /**
     * @param int|string $templateId
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function sendMailInner($templateId, $quote)
    {
        $tpl = Mage::getModel('core/email_template');

        //Get the destination email addresses to send copies to
        $bcc = $this->getEmails(self::XML_PATH_EMAIL_COPY_TO);
        if ($bcc) {
            $tpl->addBcc($bcc);
        }

        //Start store emulation process
        /** @var $appEmulation Mage_Core_Model_App_Emulation */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->getStoreId());

        $tplVars = [
            'followup'  => $this,
            'quote' => $quote
        ];

        try {
            $mailTo = $this->getMailTo();
            if (empty($mailTo)) {
                throw new Exception('Empty mail');
            }

            $tpl->setDesignConfig(['area'=>'frontend', 'store'=>$this->getStoreId()])
                ->sendTransactional(
                    $templateId,
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $this->getStoreId()),
                    $mailTo,
                    Mage::helper('customer')->getFullCustomerName($quote),
                    $tplVars
                );
        } catch (Exception $exception) {
            //Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        $mailSent = $tpl->getSentSuccess();

        //Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $mailSent;
    }

    protected function getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function getCurrentQuoteId()
    {
        $checkoutSession = Mage::getSingleton('checkout/session');
        $quoteId = $checkoutSession->getQuoteId();
        if ($quoteId) {
            return $quoteId;
        }

        if ($checkoutSession->hasQuote()) {
            return $checkoutSession->getQuote()->getId();
        }

        return false;
    }

    protected function getMailTo()
    {
        $mailTo = $this->getCustomerEmail();
        if (Mage::getStoreConfigFlag(self::XML_PATH_TEST_MODE, $this->getStoreId())) {
            $mailTest = trim(Mage::getStoreConfig(self::XML_PATH_TEST_EMAIL, $this->getStoreId()));
            if (!empty($mailTest)) {
                return explode(';', $mailTest);
            } else {
                return false;
            }
        }

        return $mailTo;
    }

    protected function getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(';', $data);
        }

        return false;
    }

    protected function getTemplate()
    {
        return Mage::helper('adm_abandonedcart')->getConfigByOffset(
            'template',
            $this->getOffset() + 1,
            $this->getStoreId()
        );
    }

    protected function setMailSent($sent = false, $error = false)
    {
        $this->setData('mail_sent', $sent);
        if ($sent) {
            $this->getTrackerObject()->setEvent(ADM_AbandonedCart_Model_Tracker::OK_SEND_MAIL);
        } else {
            if (!$error) {
                $error = ADM_AbandonedCart_Model_Tracker::ERR_SEND_DATA;
            }
            $this->getTrackerObject()->setEventError($error);
        }

        return $this;
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     *
     * @throws Zend_Date_Exception
     * @throws Exception
     */
    protected function _beforeSave()
    {
        $storeId = $this->getStoreId();

        if ($this->getData('mail_sent')) {
            //Update the offset with that of the mail that was just sent
            $this->setOffset($this->getOffset() + 1);

            //Is there another mail to send after this one?
            if ($this->getOffset() < Mage::helper('adm_abandonedcart')->getMaxOffset($storeId)) {
                $this->setMailScheduledAt($this->getNextDate());
            }
        }

        //Track the sent mail (or the attempt at sending the mail)
        $tracker = $this->getTrackerObject();
        $tracker->save();

        if ($tracker->getTrackCode()) {
            $this->setStatus($tracker->getStatus());    //What's the purpose of this assignment?
        }

        return parent::_beforeSave();
    }

    /**
     * @return string
     *
     * @throws Zend_Date_Exception
     */
    protected function getNextDate()
    {
        $helper = Mage::helper('adm_abandonedcart');
        $delayCurrent = $helper->getConfigByOffset('delay', $this->getOffset(), $this->getStoreId());
        $delayNext = $helper->getConfigByOffset('delay', $this->getOffset() + 1, $this->getStoreId());

        $delayReal = $delayNext - $delayCurrent;

        //Do not mess with the timezone
        return Mage::app()->getLocale()->date($this->getMailScheduledAt(), null, null, false)
            ->add(max($delayReal, 0), Zend_Date::HOUR)  //Make sure not to add negative delays
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }

    /**
     * @return ADM_AbandonedCart_Model_Tracker
     */
    protected function getTrackerObject()
    {
        if (empty($this->trackerObject)) {
            $this->trackerObject = Mage::getModel('adm_abandonedcart/tracker');
            $this->trackerObject->setFollowup($this);
            $this->trackerObject->setStatus(ADM_AbandonedCart_Model_Tracker::PENDING);
        }

        return $this->trackerObject;
    }
}
