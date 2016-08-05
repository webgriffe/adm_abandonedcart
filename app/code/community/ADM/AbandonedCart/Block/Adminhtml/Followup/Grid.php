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
        $this->addColumn('followup_id', array(
                'header' => $this->__('Id #'),
                'width' => '80px',
                'index'  => 'followup_id'
        ));

        $this->addColumn('abandoned_at', array(
                'header' => Mage::helper('sales')->__('Abandoned At'),
                'index' => 'abandoned_at',
                'type' => 'datetime',
                'width' => '150px',
        ));

        $this->addColumn('customer_email', array(
                'header' => $this->__('Email'),
                'index'  => 'customer_email',
                'sortable' => false
       ));

        $this->addColumn('customer_email', array(
                'header' => $this->__('Email'),
                'index'  => 'customer_email',
                'sortable' => false
        ));

        $this->addColumn('base_grand_total', array(
                'header' => Mage::helper('sales')->__('G.T. (Base)'),
                'index' => 'base_grand_total',
                'type'  => 'currency',
                'currency' => 'currency',
        ));


        $this->addColumn('offset', array(
                'header' => Mage::helper('sales')->__('Mails sent'),
                'index' => 'offset',
                'width' => '100px',
        ));

        $this->addColumn('status', array(
                'header' => $this->__('Status'),
                'width' => '80px',
                'index'  => 'status',
                'type'  => 'options',
                'options' => array(ADM_AbandonedCart_Model_Tracker::ERROR=>'Error',
                        ADM_AbandonedCart_Model_Tracker::PENDING=>'Pending',
                        ADM_AbandonedCart_Model_Tracker::SUCCESS=>'Restored'
                        ),
                'sortable' => false

        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('adm_abandonedcart')->__('CSV'));

        return parent::_prepareColumns();
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $this->getMassactionBlock()->addItem('sendmail', array(
                'label'=> $this->__('Send mail(s)'),
                'url'  => $this->getUrl('*/*/massSendMail'),
                'confirm' => $this->__('Are you sure you want to send mails()?')
        ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return false;
    }

}