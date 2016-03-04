<?php

class ADM_AbandonedCart_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getAvailableOffsets()
    {
         return array(1,2,3);
    }

    public function getMaxOffset()
    {
        return 3;
    }

    public function getOffsetDelays()
    {
        $delays = array();
        for($offset=1; $offset<=$this->getMaxOffset(); $offset++) {
            $delays[$offset] = $this->getConfigByOffset('delay', $offset);
        }

        return !empty($delays[1]) ? $delays : array();
    }


    public function getConfigByOffset($config, $offset, $store = null)
    {
        return Mage::getStoreConfig('checkout/abandonedcart/offset'.$offset.'/'.$config, $store);
    }

    public function followVirtual()
    {
        return false;
    }

    public function getUtmParams($store = null)
    {
        return Mage::getStoreConfig('checkout/abandonedcart/utm_parameters', $store);
    }
}