<?php
/**
 * Copyright DIBS | Secure Payment Services, (c) 2009.
 */
class Mage_Dibs_Model_System_Config_Source_Paymentwindow
{
   public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('adminhtml')->__('FlexWin')),
            array('value'=>2, 'label'=>Mage::helper('adminhtml')->__('Payment Window')),
        );
    }
	
 
}
