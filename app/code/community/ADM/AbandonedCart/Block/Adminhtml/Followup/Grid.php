<?php

class ADM_AbandonedCart_Block_Adminhtml_Followup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('adminhtml_followup_grid');
        $this->setDefaultSort('abandoned_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('adm_abandonedcart/followup')->getCollection();

        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('followup_id', [
            'header' => $this->__('Id #'),
            'width' => '80px',
            'index'  => 'followup_id'
        ]);

        $this->addColumn('abandoned_at', [
            'header' => Mage::helper('sales')->__('Abandoned At'),
            'index' => 'abandoned_at',
            'type' => 'datetime',
            'width' => '150px',
        ]);

        $this->addColumn('customer_email', [
            'header' => $this->__('Email'),
            'index'  => 'customer_email',
            'sortable' => false
        ]);

        $this->addColumn('customer_email', [
            'header' => $this->__('Email'),
            'index'  => 'customer_email',
            'sortable' => false
        ]);

        $this->addColumn('base_grand_total', [
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'currency',
        ]);

        $this->addColumn('offset', [
            'header' => Mage::helper('sales')->__('Mails sent'),
            'index' => 'offset',
            'width' => '100px',
        ]);

        $this->addColumn('status', [
            'header' => $this->__('Status'),
            'width' => '80px',
            'index'  => 'status',
            'type'  => 'options',
            'options' => [
                ADM_AbandonedCart_Model_Tracker::ERROR => 'Error',
                ADM_AbandonedCart_Model_Tracker::PENDING => 'Pending',
                ADM_AbandonedCart_Model_Tracker::SUCCESS => 'Restored',
            ],
            'sortable' => false,
        ]);

        $this->addExportType('*/*/exportCsv', Mage::helper('adm_abandonedcart')->__('CSV'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $this->getMassactionBlock()->addItem('sendmail', [
            'label' => $this->__('Send mail(s)'),
            'url' => $this->getUrl('*/*/massSendMail'),
            'confirm' => $this->__('Are you sure you want to send mails()?')
        ]);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return false;
    }
}
