<?php
class ADM_AbandonedCart_Model_Observer
{
    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function registerAbandonedCartAndSendFirstMail(Mage_Cron_Model_Schedule $schedule)
    {
        foreach (Mage::getModel('core/store')->getCollection() as $store) {
            if (!Mage::helper('adm_abandonedcart')->isEnabled($store)) {
                continue;
            }

            $this->deleteInvalidFollowups($store);

            Mage::getModel('adm_abandonedcart/followup')->registerAbandonedCart($store);

            $collection = Mage::getModel('adm_abandonedcart/followup')->getCollection()
                ->filterAbandonedCartByOffset($store);

            $limit = Mage::helper('adm_abandonedcart')->getMailToSendLimit($store);
            if ($limit) {
                $collection->getSelect()->limit($limit);
            }

            if ($collection->count() > 0) {
                foreach ($collection as $followup) {
                    $followup->sendMail();
                }
            }
        }

        return $this;
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @throws Exception
     */
    private function deleteInvalidFollowups($store)
    {
        //Delete all followups where the quote is no longer abandoned. Thit is those cases where the quote was updated
        //after the date when it was marked as abandoned or when it was converted into an order
        $followupsToDelete = Mage::getModel('adm_abandonedcart/followup')->getCollection();
        $followupsToDelete->addFieldToFilter('store_id', $store->getId());
        $select = $followupsToDelete->getSelect();
        $select->join(
            ['quote' => $followupsToDelete->getTable('sales/quote')],
            'main_table.quote_id = quote.entity_id',
            []
        );
        $select->where('quote.updated_at > main_table.abandoned_at OR quote.is_active = 0');

        /** @var ADM_AbandonedCart_Model_Followup $followupToDelete */
        foreach ($followupsToDelete as $followupToDelete) {
            $followupToDelete->delete();
        }
    }
}
