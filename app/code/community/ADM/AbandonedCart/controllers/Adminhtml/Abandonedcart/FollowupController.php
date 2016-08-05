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

    public function massSendMailAction()
    {
        $followupIds = $this->getRequest()->getParam('ids');
        if(count($followupIds) > 0 ) {
            $followupCollection = Mage::getModel('adm_abandonedcart/followup')->getCollection()->addFieldToFilter('followup_id', $followupIds);
            try {
                $i=0;
                foreach($followupCollection as $followup){
                    $followup->sendMail(true);
                    $i+= $followup->getMailSent() ? 1 : 0;
                }
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('%s mail(s) successfully sent',$i));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        else
       {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Nothing selected'));
        }
        $this->_redirect('*/*/');
    }


    public function exportCsvAction()
    {
        $fileName = 'abandoned_cart_followup.csv';
        $content = $this->getLayout()
        ->createBlock('adm_abandonedcart/adminhtml_followup_grid')
        ->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'text/csv')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die();
    }
}