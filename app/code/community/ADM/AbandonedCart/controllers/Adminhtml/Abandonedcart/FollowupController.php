<?php

class ADM_AbandonedCart_Adminhtml_AbandonedCart_FollowupController extends Mage_Adminhtml_Controller_action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/customer/adm_abandonedcart');
    }

    public function indexAction()
    {
        $this->_title($this->__('Customer'))->_title($this->__('Abandoned Cart'));
        $this->loadLayout();
        $this->_setActiveMenu('customer/adm_abandonedcart');
        $this->_addContent($this->getLayout()->createBlock('adm_abandonedcart/adminhtml_followup'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('adm_abandonedcart/adminhtml_followup_grid')->toHtml()
        );
    }
}