<?php

class ADM_AbandonedCart_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_offest_max = 3;


    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag('abandonedcart/general/enabled', $store);
    }

    public function getMailToSendLimit($store = null)
    {
        $limit = Mage::getStoreConfig('abandonedcart/general/mail_limit', $store);

        return $limit;
    }

    public function getAvailableOffsets()
    {
        $offsets = array();
        $config =Mage::getStoreConfig('abandonedcart');
        for($offsetNbr=1; $offsetNbr<=$this->_offest_max; $offsetNbr++) {
            $configKey = 'offset' . $offsetNbr;
            if(!empty($config[$configKey]) and !empty($config[$configKey]['scheduled']) and !empty($config[$configKey]['delay'])) {
                $offsets[]  = $offsetNbr;
            } else {
                break;
            }
        }

         return $offsets;
    }

    public function getMaxOffset()
    {
        return max($this->getAvailableOffsets());
    }

    public function getOffsetDelays()
    {
        $delays = array();
        foreach($this->getAvailableOffsets() as $offset) {
            $delays[$offset] = $this->getConfigByOffset('delay', $offset);
        }

        return !empty($delays[1]) ? $delays : array();
    }


    public function getConfigByOffset($config, $offset, $store = null)
    {
        return Mage::getStoreConfig('abandonedcart/offset'.$offset.'/'.$config, $store);
    }

    public function followVirtual()
    {
        return false;
    }

    public function getUtmParams($store = null)
    {
        return Mage::getStoreConfig('abandonedcart/utm_parameters', $store);
    }
}