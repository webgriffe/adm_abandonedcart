<?php

class ADM_AbandonedCart_Helper_Data extends Mage_Core_Helper_Abstract
{
    const OFFSET_MAX = 3;

    /**
     * @param Mage_Core_Model_Store|int $store
     *
     * @return bool
     */
    public function isEnabled($store)
    {
        return Mage::getStoreConfigFlag('abandonedcart/general/enabled', $store);
    }

    /**
     * @param Mage_Core_Model_Store|int $store
     *
     * @return int|null|false
     */
    public function getMailToSendLimit($store)
    {
        return Mage::getStoreConfig('abandonedcart/general/mail_limit', $store);
    }

    /**
     * @param Mage_Core_Model_Store|int $store
     *
     * @return int
     */
    public function getMaxOffset($store)
    {
        //If no offset is enabled or configured, then return 0
        return max(array_merge([0], $this->getAvailableOffsets($store)));
    }

    /**
     * @param Mage_Core_Model_Store|int $store
     *
     * @return int[]
     */
    public function getOffsetDelays($store)
    {
        $delays = [];
        foreach ($this->getAvailableOffsets($store) as $offset) {
            $delays[$offset] = $this->getConfigByOffset('delay', $offset, $store);
        }

        return $delays;
    }

    /**
     * @param string $config
     * @param int $offset
     * @param Mage_Core_Model_Store|int $store
     *
     * @return mixed
     */
    public function getConfigByOffset($config, $offset, $store)
    {
        return Mage::getStoreConfig("abandonedcart/offset{$offset}/{$config}", $store);
    }

    /**
     * @param Mage_Core_Model_Store|int $store
     *
     * @return bool
     */
    public function followVirtual($store)
    {
        return false;
    }

    /**
     * @param Mage_Core_Model_Store|int $store
     *
     * @return array
     */
    public function getUtmParams($store)
    {
        return Mage::getStoreConfig('abandonedcart/utm_parameters', $store);
    }

    /**
     * @param Mage_Core_Model_Store|int $store
     *
     * @return int[]
     */
    protected function getAvailableOffsets($store)
    {
        $offsets = [];
        $config = Mage::getStoreConfig('abandonedcart', $store);
        for ($offset = 1; $offset <= self::OFFSET_MAX; ++$offset) {
            $configKey = 'offset' . $offset;
            if (!empty($config[$configKey]) && $config[$configKey]['scheduled'] && $config[$configKey]['delay']) {
                $offsets[] = $offset;
            } else {
                break;
            }
        }

        return $offsets;
    }
}
