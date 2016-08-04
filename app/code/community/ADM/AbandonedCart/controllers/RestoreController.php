<?php

class ADM_AbandonedCart_RestoreController extends Mage_Core_Controller_Front_Action
{
    public function cartAction()
    {
        $id   = (int) $this->getRequest()->getParam('id');
        $restoreCode = (string) $this->getRequest()->getParam('restore_code');

        $redirectUrl = '/';
        $return = array();
        if ($id && $restoreCode) {
            $followup = Mage::getModel('adm_abandonedcart/followup')->load($id);
            $return = $followup->tryToRestoreCart($restoreCode);

            if(!empty($return)) {
                if(!empty($return['message'])) {
                    if(empty($return['error'])) {
                        Mage::getSingleton('checkout/session')->addSuccess($this->__($return['message']));
                    } else {
                        Mage::getSingleton('checkout/session')->addError($this->__($return['message']));
                    }
                }
            }

            $redirectUrl = $followup->getRedirectUrl();
        }
        return $this->_redirect($redirectUrl);
    }


}