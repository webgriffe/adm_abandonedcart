<?php

class ADM_AbandonedCart_Block_Adminhtml_Followup extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'adm_abandonedcart';
        $this->_controller = 'adminhtml_followup';
        $this->_headerText = $this->__('Abandoned Cart');

        parent::__construct();
        $this->_removeButton('add');

    }
}