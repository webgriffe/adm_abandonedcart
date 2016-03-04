<?php

class ADM_AbandonedCart_Model_Resource_Tracker_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('adm_abandonedcart/tracker');
        return parent::_construct();
    }
}