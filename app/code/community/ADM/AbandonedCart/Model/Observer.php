<?php
class ADM_AbandonedCart_Model_Observer
{
    /**
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function registerAbandonedCartAndSendFirstMail(Mage_Cron_Model_Schedule $schedule)
    {
        if(!Mage::helper('adm_abandonedcart')->isEnabled()) {
            return false;
        }

        $affectedRows = Mage::getModel('adm_abandonedcart/followup')->registerAbandonedCart();

        $collection =  Mage::getModel('adm_abandonedcart/followup')->getCollection()
            ->filterAbandonedCartByOffset();

        $limit = Mage::helper('adm_abandonedcart')->getMailToSendLimit();
        if($limit) {
            $collection->getSelect()->limit($limit);
        }

        if($collection->getSize()>0) {
            foreach ($collection as $followup) {
                $followup->sendMail();
            }
        }

        return $this;
    }
}