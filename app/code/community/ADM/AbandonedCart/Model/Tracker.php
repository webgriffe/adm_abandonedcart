<?php

/**
 * Class ADM_AbandonedCart_Model_Tracker
 *
 * @method int getTrackerId()
 * @method $this setTrackerId(int $value)
 * @method int getFollowupId()
 * @method $this setFollowupId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method int getOffset()
 * @method $this setOffset(int $value)
 * @method string getTrackCode()
 * @method $this setTrackCode(string $value)
 * @method string getTrackMessage()
 * @method $this setTrackMessage(string $value)
 * @method string getRemoteIp()
 * @method $this setRemoteIp(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 */
class ADM_AbandonedCart_Model_Tracker extends Mage_Core_Model_Abstract
{
    const ERROR                           = -1;
    const PENDING                         =  0;
    const SUCCESS                         =  1;

    const ERR_WRONG_CODE                  = 'err_wrong_secret';
    const ERR_NO_INFO                     = 'err_nor_customer_nor_quote';
    const ERR_USER_LOGGED                 = 'err_user_already_logged';
    const ERR_WRONG_USER_LOGGED           = 'err_wrong_user_logged';

    const ERR_SEND_DATA                   = 'mail_err_sending';
    const ERR_NO_TEMPLATE                 = 'mail_err_sending_no_template';
    const ERR_NO_QUOTE                    = 'mail_err_sending_no_quote';
    const ERR_NO_QUOTE_ACTIVE             = 'mail_err_sending_no_quote_active';
    const OK_SEND_MAIL                    = 'mail_ok_sent';

    const OK_LOG_USER                     = 'ok_restore_log_user';
    const OK_LOG_USER_WITH_CART           = 'ok_logged_with_cart';
    const OK_RESTORE_CART                 = 'ok_restore_cart';

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

    /**
     * @param ADM_AbandonedCart_Model_Followup $followUp
     *
     * @return $this
     */
    public function setFollowup($followUp)
    {
        $this->setFollowupId($followUp->getId());
        $this->setOffset($followUp->getOffset());
        $this->setStoreId($followUp->getStoreId());

        return $this;
    }

    /**
     * @param string $trackCode
     *
     * @return $this
     */
    public function setEventSuccess($trackCode)
    {
        return $this->setEvent($trackCode, self::SUCCESS);
    }

    /**
     * @param string $trackCode
     *
     * @return $this
     */
    public function setEventError($trackCode)
    {
        return $this->setEvent($trackCode, self::ERROR);
    }

    /**
     * @param string $trackCode
     * @param int $status
     *
     * @return $this
     */
    public function setEvent($trackCode, $status = 0)
    {
        $this->setTrackCode($trackCode);
        $this->setStatus($status);  //Is this used anywhere?

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
            $this->setCreatedAt(Mage::getModel('core/date')->gmtDate());
            $this->setRemoteIp(Mage::helper('core/http')->getRemoteAddr());
        }

        return parent::_beforeSave();
    }
}
