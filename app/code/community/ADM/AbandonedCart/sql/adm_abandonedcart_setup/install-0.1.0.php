<?php

// $installer = $this; //Mage_Core_Model_Resource_Setup
$installer = new Mage_Customer_Model_Entity_Setup ('core_setup');
$installer->startSetup();

/**
 * Create table 'adm_abandonedcart/followup'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('adm_abandonedcart/followup'))
    ->addColumn(
        'followup_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        [
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ],
        'Followup Id'
    )
    ->addColumn(
        'quote_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ],
        'Quote Id'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ],
        'Store Id'
    )
    ->addColumn(
        'abandonned_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        [
            'nullable'  => false,
        ],
        'Cart Abandoned At'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        [
            'unsigned'  => true,
            'default'   => '0',
        ],
        'Customer Id'
    )
    ->addColumn(
        'customer_email',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        [],
        'Customer Email'
    )
    ->addColumn(
        'base_grand_total',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        [
            'default'   => '0.0000',
        ],
        'Base Grand Total'
    )
    ->addColumn(
        'coupon_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        [],
        'Coupon Code'
    )
    ->addColumn(
        'secret_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        [],
        'Secret code'
    )
    ->addColumn(
        'is_closed',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        [
            'unsigned'  => true,
            'default'   => '0',
        ],
        'Is restored'
    )
    ->addColumn(
        'offset',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        [
            'unsigned'  => true,
            'default'   => '0',
        ],
        'Offset'
    )
    ->addColumn(
        'mail_scheduled_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        [
            'nullable'  => false,
        ],
        'Mail1 Scheduled At'
    )
    ->addIndex(
        $installer->getIdxName('adm_abandonedcart/followup', ['customer_id', 'store_id']),
        ['customer_id', 'store_id']
    )
    ->addIndex(
        $installer->getIdxName('adm_abandonedcart/followup', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName('adm_abandonedcart/followup', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $installer->getIdxName('adm_abandonedcart/followup', ['quote_id']),
        ['quote_id']
    )
    ->addForeignKey(
        $installer->getFkName('adm_abandonedcart/followup', 'quote_id', 'sales/quote', 'entity_id'),
        'quote_id',
        $installer->getTable('sales/quote'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('ADM Cart Abandoned to follow');

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('adm_abandonedcart/tracker'))
    ->addColumn(
        'tracker_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        [
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ],
        'Tracker Id'
    )
    ->addColumn(
        'followup_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ],
        'Followup Id'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        [
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ],
        'Store Id'
    )
    ->addColumn(
        'restored',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        [
            'unsigned'  => true,
            'default'   => '0',
        ],
        'Restored'
    )
    ->addColumn(
        'offset',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        [
            'unsigned'  => true,
            'default'   => '0',
        ],
        'Offset'
    )
    ->addColumn(
        'track_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        [],
        'Message'
    )
    ->addColumn(
        'track_message',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        [],
        'Message'
    )
    ->addColumn(
        'remote_ip',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        16,
        [
            'nullable'  => false
        ],
        'Customer IP'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        [
            'nullable'  => false,
        ],
        'Created At'
    )
    ->addForeignKey(
        $installer->getFkName('adm_abandonedcart/tracker', 'followup_id', 'adm_abandonedcart/tracker', 'followup_id'),
        'followup_id',
        $installer->getTable('adm_abandonedcart/followup'),
        'followup_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('ADM Cart Abandoned tracker');

$installer->getConnection()->createTable($table);

$installer->endSetup();
