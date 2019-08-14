<?php
/**
 * Copyright DIBS | Secure Payment Services, (c) 2009.
 */
class Mage_Dibs_Model_System_Config_Source_Decorator
{
   public function toOptionArray()
    {
        return array(
            array('value'=>'default', 'label'=>Mage::helper('adminhtml')->__('default')),
            array('value'=>'basal', 'label'=>Mage::helper('adminhtml')->__('basal')),
            array('value'=>'rich', 'label'=>Mage::helper('adminhtml')->__('rich')),
            array('value'=>'own', 'label'=>Mage::helper('adminhtml')->__('Own decorator')),	
        );
    }
	
 
}
