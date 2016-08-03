<?php

class ADM_AbandonedCart_Block_Adminhtml_Followup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('adminhtml_followup_grid');
        $this->setDefaultSort('abandonned_at');
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

        $this->addColumn('abandonned_at', array(
                'header' => Mage::helper('sales')->__('Abandonned At'),
                'index' => 'abandonned_at',
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


        $this->addColumn('offset', array(
                'header' => Mage::helper('sales')->__('Mails sent'),
                'index' => 'offset',
                'width' => '100px',
        ));

        $this->addColumn('is_closed', array(
                'header' => $this->__('Status'),
                'width' => '80px',
                'index'  => 'is_closed',
                'type'  => 'options',
                'options' => array('0'=>'Closed', '1'=>'Pending'),
                'sortable' => false

        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        //return $this->getUrl('*/*/edit', array('id' => $row->getEntityId()));
        return false;
    }

}