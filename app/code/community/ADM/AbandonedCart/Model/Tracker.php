<?php

class ADM_AbandonedCart_Model_Tracker extends Mage_Core_Model_Abstract
{
    const SUCCESS                         = 0;
    const WARNING                         = 1;
    const ERROR                           = 2;

    const ERR_NO_INFO                     = 'err_nor_customer_nor_quote';
    const ERR_USER_LOGGED                 = 'err_user_already_logged';
    const ERR_WRONG_USER_LOGGED           = 'err_wrong_user_logged';

    const ERR_SEND_DATA                   = 'err_send_mail_error';
    const ERR_NO_TEMPLATE                 = 'err_send_no_template';
    const ERR_NO_QUOTE                    = 'err_send_no_quote';
    const ERR_NO_QUOTE_ACTIVE             = 'err_send_no_quote_active';

    const OK_LOG_USER                     = 'ok_restore_log_user';
    const OK_RESTORE_CART                 = 'ok_restore_cart';
    const OK_SEND_MAIL                    = 'ok_mail_sent';


    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'abandoned_cart_tracker';

    protected function _construct()
    {
        $this->_init('adm_abandonedcart/tracker');
        return parent::_construct();
    }


    public function setFollowup($followUp)
    {
        $this->setFollowupId($followUp->getId());
        $this->setOffset($followUp->getCurrentOffset());
        $this->setStoreId($followUp->getStoreId());

        return $this;
    }

    public function setEvent($track_code, $status = 0)
    {
        $this->setTrackCode($track_code);
        $this->setSatus($status);

        return $this;
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {

        if (!$this->hasTrackCode()) {
            $this->_dataSaveAllowed = false;
        } else {
            $this->setCreatedAt(Varien_Date::now());
            $this->setRemoteIp(Mage::helper('core/http')->getRemoteAddr());
        }

        return parent::_beforeSave();
    }

}