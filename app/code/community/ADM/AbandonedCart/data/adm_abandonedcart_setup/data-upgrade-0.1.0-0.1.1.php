<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$connection = $installer->getConnection();

/** @var Mage_Core_Model_Website $website */
foreach (Mage::app()->getWebsites() as $website) {
    foreach ($website->getGroups() as $group) {
        $stores = $group->getStores();
        foreach ($stores as $store) {
            $connection->query(
                "UPDATE {$this->getTable('adm_abandonedcart/followup')} SET currency=? WHERE store_id=?",
                [
                    $store->getCurrentCurrencyCode(),
                    $store->getId()
                ]
            );
        }
    }
}

$installer->endSetup();
