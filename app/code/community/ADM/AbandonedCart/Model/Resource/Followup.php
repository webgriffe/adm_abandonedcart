<?php

class ADM_AbandonedCart_Model_Resource_Followup extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        //Note that the id refers to the key field in your database table.
        $this->_init('adm_abandonedcart/followup', 'followup_id');
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @param int $minDelay
     * @param int $maxDelay
     *
     * @return int
     */
    public function registerAbandonedCart($store, $minDelay, $maxDelay)
    {
        /** @var Mage_Sales_Model_Resource_Quote_Collection $quotes */
        $quotes = Mage::getModel('sales/quote')->getCollection()
            ->addFieldToFilter('main_table.store_id', $store->getId())
            ->addFieldToFilter('main_table.is_active', 1)
            ->addFieldToFilter('main_table.items_count', ['gt' => 0])
            ->addFieldToFilter('main_table.customer_email', ['notnull' => true])
            ->addFieldToFilter('main_table.updated_at', ['lt' => $this->getDateSubTime($minDelay)])
            ->addFieldToFilter('main_table.updated_at', ['gt' => $this->getDateSubTime($maxDelay)]);

        if (!Mage::helper('adm_abandonedcart')->followVirtual($store)) {
            $quotes->addFieldToFilter('main_table.is_virtual', 0);
        }

        $quotes->getSelect()
            ->joinLeft(
                ['followup' => $this->getMainTable()],
                'followup.quote_id = main_table.entity_id',
                []
            )
            ->where('followup.followup_id IS NULL');

        Mage::dispatchEvent('adm_abandonedcart_quote_collection_load_before', ['collection' => $quotes]);

        $numberOfAbandonedQuotesFound = 0;
        if ($quotes->count() > 0) {
            $now = Mage::getModel('core/date')->gmtDate();
            $followupRows = [];
            /** @var Mage_Sales_Model_Quote $quote */
            foreach ($quotes as $quote) {
                $followupRows[] = [
                    'quote_id'          => $quote->getId(),
                    'store_id'          => $quote->getStoreId(),
                    'abandoned_at'      => $quote->getUpdatedAt(),
                    'customer_id'       => $quote->getCustomerId(),
                    'customer_email'    => $quote->getCustomerEmail(),
                    'secret_code'       => md5(uniqid()),
                    'currency'          => $quote->getQuoteCurrencyCode(),
                    'base_grand_total'  => $quote->getBaseGrandTotal(),
                    'coupon_code'       => $quote->getCouponCode(),
                    'mail_scheduled_at' => $now,
                ];
            }

            if (count($followupRows)) {
                $numberOfAbandonedQuotesFound = $this->_getWriteAdapter()->insertMultiple(
                    $this->getMainTable(),
                    $followupRows
                );
            }
        }

        return $numberOfAbandonedQuotesFound;
    }

    protected function getDateSubTime($nbr, $type = Zend_Date::HOUR)
    {
        $date = Mage::app()->getLocale()->date()
            ->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)
            ->sub($nbr, $type)
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        return $date;
    }

    protected function getDateAddTime($nbr, $type = Zend_Date::HOUR)
    {
        $date = Mage::app()->getLocale()->date()
            ->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)
            ->add($nbr, $type)
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        return $date;
    }
}
