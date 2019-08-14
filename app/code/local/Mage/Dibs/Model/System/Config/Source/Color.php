<?php
/**
 * Copyright DIBS | Secure Payment Services, (c) 2009.
 */
class Mage_Dibs_Model_System_Config_Source_Color
{
   public function toOptionArray()
    {
        return array(
        	array('value'=>'blank', 'label'=>Mage::helper('adminhtml')->__('_none_')),
            array('value'=>'sand', 'label'=>Mage::helper('adminhtml')->__('sand')),
            array('value'=>'grey', 'label'=>Mage::helper('adminhtml')->__('grey')),
            array('value'=>'blue', 'label'=>Mage::helper('adminhtml')->__('blue')),
        );
    }
	
 
}
