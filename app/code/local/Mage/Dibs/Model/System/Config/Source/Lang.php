<?php
/**
 * Copyright DIBS | Secure Payment Services, (c) 2009.
 */
class Mage_Dibs_Model_System_Config_Source_Lang
{
   public function toOptionArray()
    {
        return array(
        	array('value'=>'auto', 'label'=>Mage::helper('adminhtml')->__('Auto')),
            array('value'=>'da', 'label'=>Mage::helper('adminhtml')->__('Danish')),
            array('value'=>'nl', 'label'=>Mage::helper('adminhtml')->__('Dutch')),
            array('value'=>'en', 'label'=>Mage::helper('adminhtml')->__('English')),
            array('value'=>'fo', 'label'=>Mage::helper('adminhtml')->__('Faroese')),
            array('value'=>'fi', 'label'=>Mage::helper('adminhtml')->__('Finnish')),
            array('value'=>'fr', 'label'=>Mage::helper('adminhtml')->__('French')),
            array('value'=>'de', 'label'=>Mage::helper('adminhtml')->__('German')),
            array('value'=>'it', 'label'=>Mage::helper('adminhtml')->__('Italian')),
            array('value'=>'no', 'label'=>Mage::helper('adminhtml')->__('Norwegian')),
            array('value'=>'pl', 'label'=>Mage::helper('adminhtml')->__('Polish')),
            array('value'=>'es', 'label'=>Mage::helper('adminhtml')->__('Spanish')),
            array('value'=>'sv', 'label'=>Mage::helper('adminhtml')->__('Swedish')),
        );
    }
	
 
}
