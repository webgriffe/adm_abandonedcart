<?php
class ADM_AbandonedCart_Model_Observer
{
    /**
     * Cron job method for product flat to reindex
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function registerAbandonedCartAndSendFirstMail(Mage_Cron_Model_Schedule $schedule)
    {
        $affectedRows = Mage::getModel('adm_abandonedcart/followup')->registerAbandonedCart();

        $collection =  Mage::getModel('adm_abandonedcart/followup')->getCollection()->filterAbandonnedCartByOffset();
        if($collection->getSize()>0) {
            foreach ($collection as $followup) {
                $followup->sendMail();
            }
        }
    }
}