<?php
$installer = $this;

$connection = $installer->getConnection();
foreach (Mage::app()->getWebsites() as $website) {
    foreach ($website->getGroups() as $group) {
        $stores = $group->getStores();
        foreach ($stores as $store) {
            $connection->query('UPDATE ' . $this->getTable('adm_abandonedcart/followup') . ' SET currency=? WHERE store_id=?', array($store->getCurrentCurrencyCode(), $store->getId()));
        }
    }
}

$installer->endSetup();