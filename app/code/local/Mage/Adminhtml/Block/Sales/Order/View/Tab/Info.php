<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order information tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tab_Info
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Retrieve source model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getOrder();
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return array(
            'can_display_total_due'      => true,
            'can_display_total_paid'     => true,
            'can_display_total_refunded' => true,
        );
    }

    public function getOrderInfoData()
    {
        return array(
            'no_use_order_link' => true,
        );
    }

    public function getTrackingHtml()
    {
        return $this->getChildHtml('order_tracking');
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('order_items');
    }

    /**
     * Retrive giftmessage block html
     *
     * @return string
     */
    public function getGiftmessageHtml()
    {
        return $this->getChildHtml('order_giftmessage');
    }

    public function getPaymentHtml()
    {
    	$res = $this->getChildHtml('order_payment');
    	
			//
			// Read info directly from the database   	
    	$read = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$row = $read->fetchRow("select * from dibs_order_status where orderid = " . $this->getOrder()->getIncrementId());
    	
    	$standard = Mage::getModel('dibs/standard');
    	if ($row['status'] == '1') {
    		//
    		// Payment has been made to this order
    		$res .= "<br><br>" . "<table border='0' width='100%'>";
    		$res .= "<tr><td colspan='2'><b>" . Mage::helper('dibs')->__('DIBS_LABEL_7') . "</b></td></tr>";
    		if ($row['transact'] != '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_8') . "</td>";
    			$res .= "<td>" . $row['transact'] . "</td></tr>";
    		}
    		if ($row['amount'] != '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_9') . "</td>";
    			$res .= "<td>" . $this->getOrder()->getOrderCurrencyCode() . "&nbsp;" . number_format(((int)$row['amount']) / 100, 2, ',', ' ') . "</td></tr>";
    		}
    		if ($row['currency'] != '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_10') . "</td>";
    			$res .= "<td>" . $row['currency'] . "</td></tr>";
    		}
    		if ($row['fee'] != '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_11') . "</td>";
    			$res .= "<td>" . $this->getOrder()->getOrderCurrencyCode() . "&nbsp;" . number_format(((int)$row['fee']) / 100, 2, ',', ' ') . "</td></tr>";
    		}
    		if ($row['paytype'] != '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_12') . "</td>";
				$res .= "<td>" . $this->printLogo($row['paytype']) . "</td></tr>";
    		}
    		if ($row['cardnomask'] != '0' && $row['cardprefix'] == '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_13') . "</td>";
				$res .= "<td>............" . trim($row['cardnomask'],'X') . "</td></tr>";
    		}
    		if ($row['cardprefix'] != '0' && $row['cardnomask'] == '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_13') . "</td>";
				$res .= "<td>" . $row['cardprefix'] . "..........</td></tr>";
    		}
    		if ($row['cardprefix'] != '0' && $row['cardnomask'] != '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_13') . "</td>";
				$res .= "<td>" . $row['cardprefix'] . '......' . trim($row['cardnomask'],'X') . "</td></tr>";
    		}
    		if ($row['cardexpdate'] != '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_14') . "</td>";
				$res .= "<td>" . substr($row['cardexpdate'],2,2) . " / " . substr($row['cardexpdate'],0,2) . "</td></tr>";
    		}
    		if ($row['cardcountry'] != '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_15') . "</td>";
				$res .= "<td>" . $row['cardcountry'] . "</td></tr>";
    		}
    		if ($row['acquirer'] != '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_16') . "</td>";
				$res .= "<td>" . $row['acquirer'] . "</td></tr>";
    		}
    		if ($row['enrolled'] != '0') {
    			$res .= "<tr><td>" . Mage::helper('dibs')->__('DIBS_LABEL_17') . "</td>";
				$res .= "<td>" . $row['enrolled'] . "</td></tr>";
    		}
    		$res .= "</table><br>";
    		
    		$res .= "<a href='https://payment.architrade.com/admin/' target='_blank'>" . Mage::helper('dibs')->__('DIBS_LABEL_18') . "</a>";
    		$res .= "<br><br>";
    		
    	} else {
    		$res .= "<br>" . Mage::helper('dibs')->__('DIBS_LABEL_19') . "<br>";
    	}
			
        return $res;
    }
    
public function printLogo($paytype) {
    	   	
    	switch($paytype) {
    		case 'AMEX': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/amex.gif') . '" border="0" />'; break;
    		}
    		case 'AMEX(DK)': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/amex.gif') . '" border="0" />';  break;
    		}
    		case 'BAX': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/bax.gif') . '" border="0" />';  break;
    		}
    		case 'DIN': {
    			$res = $this->getSkinUrl('images/dibs/diners.gif') . '" border="0" />';  break;
    		}
    		case 'DIN(DK)': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/diners.gif') . '" border="0" />';  break;
    		}
    		case 'DK': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/dankort.gif') . '" border="0" />';  break;
    		}
    		case 'FFK': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/forbrugforeningen.gif') . '" border="0" />';  break;
    		}
    		case 'JCB': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/jcb.gif') . '" border="0" />';  break;
    		}
    		case 'MC': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/mastercard.gif') . '" border="0" />';  break;
    		}
    		case 'MC(DK)': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/mastercard.gif') . '" border="0" />';  break;
    		}
    		case 'MC(SE)': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/mastercard.gif') . '" border="0" />';  break;
    		}
    		case 'MTRO': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/maestro.gif') . '" border="0" />';  break;
    		}
    		case 'MTRO(DK)': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/maestro.gif') . '" border="0" />';  break;
    		}
    		case 'MTRO(SE)': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/maestro.gif') . '" border="0" />';  break;
    		}
    		case 'MOCA': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/mobilcash.gif')  . '" border="0" />'; break;
    		}
    		case 'V-DK': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/visa.gif') . '" border="0" />';  break;
    		}
    		case 'VISA': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/visa.gif') . '" border="0" />'; break;
    		}
    		case 'VISA(DK)': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/visa.gif') . '" border="0" />';  break;
    		}
    		case 'VISA(SE)': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/visa.gif') . '" border="0" />';  break;
    		}
    		case 'ELEC': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/visaelectron.gif') . '" border="0" />';  break;
    		}
    		case 'AKTIA': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/aktia.gif') . '" border="0" />';  break;
    		}
    		case 'DNB': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/danskenetbetaling.gif') . '" border="0" />';  break;
    		}
    		case 'EDK': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/edankort.gif') . '" border="0" />';  break;
    		}
    		case 'ELV': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/bankeinzug.gif') . '" border="0" />';  break;
    		}
    		case 'EW': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/ewire.gif') . '" border="0" />';  break;
    		}
    		case 'FSB': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/swedbankdirektbetaling.gif') . '" border="0" />';  break;
    		}
    		case 'GIT': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/getitcard.gif') . '" border="0" />';  break;
    		}
    		case 'ING': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/ideal.gif') . '" border="0" />';  break;
    		}
    		case 'NDB': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/nordea.gif') . '" border="0" />';  break;
    		}
    		case 'SEB': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/seb.gif') . '" border="0" />';  break;
    		}
    		case 'SHB': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/shbdirektbetaling.gif') . '" border="0" />';  break;
    		}
    		case 'SOLO': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/nordea.gif') . '" border="0" />';  break;
    		}
    		case 'VAL': {
    			$res = '<img src="' . $this->getSkinUrl('images/dibs/valus.gif') . '" border="0" />';  break;
    		}
    		default: {
    			$res = '<img src="' . $paytype;
    		}
    			
    	}
    	return $res;
    }

    public function getViewUrl($orderId)
    {
        return $this->getUrl('*/*/*', array('order_id'=>$orderId));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Information');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Order Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}