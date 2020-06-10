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
     * @return $this
     */
    public function filterAbandonedCartByOffset($store)
    {
        $this
            ->addFieldToFilter('store_id', $store->getId())
            ->addFieldToFilter('status', ['lteq' => 0])
            ->addFieldToFilter('offset', ['lt' => Mage::helper('adm_abandonedcart')->getMaxOffset($store)])
            ->addFieldToFilter('mail_scheduled_at', ['lteq' => Mage::getModel('core/date')->gmtDate()]);

        $this->getSelect()->join(
            ['quote' => $this->getTable('sales/quote')],
            'main_table.quote_id = quote.entity_id',
            ['quote_still_active' => 'is_active']
        );

        return $this;
    }
}
