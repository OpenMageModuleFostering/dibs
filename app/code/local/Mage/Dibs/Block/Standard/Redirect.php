<?php 
/**
 * Copyright ePay | Dit Online Betalingssystem, (c) 2009.
 * Modifications copyrighted by  DIBS | Secure Payment Services, (c) 2009.
 */
class Mage_Dibs_Block_Standard_Redirect extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();
        
        $standard = Mage::getModel('dibs/standard');
        if ($standard->getConfigData('paymentwindow') == '1') {
        	$this->setTemplate('dibs/standard/redirect_flexwin.phtml');
        } else {
        	$this->setTemplate('dibs/standard/redirect_paymentwindow.phtml');
        }
        
        // Save the order into the dibs_order_status table
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$write->insert('dibs_order_status', Array('orderid'=>$standard->getCheckout()->getLastRealOrderId()));
    }
}
