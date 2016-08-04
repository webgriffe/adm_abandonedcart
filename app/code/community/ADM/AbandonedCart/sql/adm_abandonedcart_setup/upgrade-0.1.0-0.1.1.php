<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$trackerTable = $this->getTable('adm_abandonedcart/tracker');
$connection->dropColumn(
        $trackerTable,
        'restored'
);

$connection->query('UPDATE ' . $trackerTable . ' SET track_code=? WHERE track_code=?', array('mail_err_sending', 'err_send_mail_error'));
$connection->query('UPDATE ' . $trackerTable . ' SET track_code=? WHERE track_code=?', array('mail_err_sending_no_template', 'err_send_no_template'));
$connection->query('UPDATE ' . $trackerTable . ' SET track_code=? WHERE track_code=?', array('mail_err_sending_no_quote', 'err_send_no_quote'));
$connection->query('UPDATE ' . $trackerTable . ' SET track_code=? WHERE track_code=?', array('mail_err_sending_no_quote_active', 'err_send_no_quote_active'));
$connection->query('UPDATE ' . $trackerTable . ' SET track_code=? WHERE track_code=?', array('mail_ok_sent', 'ok_mail_sent'));


$followupTable = $this->getTable('adm_abandonedcart/followup');
$connection->changeColumn(
        $followupTable,
        'is_closed',
        'status',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'unsigned'  => true,
            'comment' => 'Status',
            'default'   => '0'
        )
);

$connection->addColumn(
        $followupTable,
        'currency',
        'varchar(255) DEFAULT NULL COMMENT \'Quote Currency Code\' after base_grand_total'
);

$installer->endSetup();
