<?php

class ADM_AbandonedCart_Model_Resource_Followup_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('adm_abandonedcart/followup');
        return parent::_construct();
    }

    /**
     * @param Mage_Core_Model_Store $store
     *
     * @return $this
     */
    public function filterAbandonedCartByOffset($store)
    {
        $this
            ->addFieldToFilter('main_table.store_id', $store->getId())
            ->addFieldToFilter('main_table.status', ['lteq' => 0])
            ->addFieldToFilter('main_table.offset', ['lt' => Mage::helper('adm_abandonedcart')->getMaxOffset($store)])
            ->addFieldToFilter('main_table.mail_scheduled_at', ['lteq' => Mage::getModel('core/date')->gmtDate()]);

        //Is this still needed after the deleteInvalidFollowups() method has been added to the observer?
        $this->getSelect()->join(
            ['quote' => $this->getTable('sales/quote')],
            'main_table.quote_id = quote.entity_id',
            ['quote_still_active' => 'is_active']
        );

        return $this;
    }
}
