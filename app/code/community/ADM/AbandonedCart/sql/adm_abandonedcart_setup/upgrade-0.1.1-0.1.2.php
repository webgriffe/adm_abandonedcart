<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$followupTable = $this->getTable('adm_abandonedcart/followup');
$connection->changeColumn(
        $followupTable,
        'status',
        'status',
        array(
                'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => false,
                'comment' => 'Status',
                'default'   => '0'
        )
);


$connection->changeColumn(
        $followupTable,
        'abandonned_at',
        'abandoned_at',
        array(
                'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment' => 'Cart Abandoned At'
        )
);

$connection->addColumn(
        $followupTable,
        'order_id',
        'int(10) unsigned DEFAULT 0 COMMENT \'Order Id\' AFTER mail_scheduled_at'
);

$connection->addColumn(
        $followupTable,
        'order_coupon_code',
        'varchar(255) DEFAULT NULL COMMENT \'Order Coupon Code\' AFTER order_id'
);

$installer->endSetup();
