<?php

class ADM_AbandonedCart_Model_Resource_Tracker extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        // Note that the id refers to the key field in your database table.
        $this->_init('adm_abandonedcart/tracker', 'tracker_id');
    }

}