<?php
/**
 * Copyright ePay | Dit Online Betalingssystem, (c) 2009.
 * Modifications copyrighted by  DIBS | Secure Payment Services, (c) 2009.
 */
class Mage_Dibs_StandardController extends Mage_Core_Controller_Front_Action {
	 
    protected function _expireAjax() {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with dibs strandard order transaction information
     *
     * @return Mage_Dibs_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('dibs/standard');
    }

    /**
     * When a customer chooses Dibs on Checkout/Payment page
     */
    public function redirectAction() {
    	
    	// Load layout
      	$this->loadLayout();
	    $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('dibs/standard_redirect'));
    	$this->renderLayout();

	    // Load the session object
      	$session = Mage::getSingleton('checkout/session');
      	$session->setDibsStandardQuoteId($session->getQuoteId());
      
      	// Save order comment
      	$order = Mage::getModel('sales/order');
      	$order->loadByIncrementId($session->getLastRealOrderId());
      	$order->addStatusToHistory(
        	$order->getStatus(),
          	$this->__('DIBS_LABEL_3')
      	);
      	$order->save();
      
      	// Add items back on stock (if used)
      	$this->addToStock();
    }
    
	public function setOrderStatusAfterPayment()     {
    
      $payment = Mage::getModel('dibs/standard');
      
      $order = Mage::getModel('sales/order');
      $order->loadByIncrementId($_POST['orderid']);
   
      $order->addStatusToHistory($payment->getConfigData('order_status_after_payment'), '', false);
      $order->save();
    }
    public function addToStock() {
    	
    	// Load the payment object
    	$payment = Mage::getModel('dibs/standard');
      
      	// Load the session object
      	$session = Mage::getSingleton('checkout/session');
      	$session->setDibsStandardQuoteId($session->getQuoteId());
      
      	// Load the order object
      	$order = Mage::getModel('sales/order');
      	$order->loadByIncrementId($session->getLastRealOrderId());
      
    	// add items back on stock
    	// Put the order back on stock as it is not yet paid!
    	// http://www.magentocommerce.com/wiki/groups/132/protx_form_-_subtracting_stock_on_successful_payment
    	if (((int)$payment->getConfigData('handlestock')) == 1) {
      	if(!isset($_SESSION['stock_removed']) || $_SESSION['stock_removed'] != $session->getLastRealOrderId()) {
            /* Put the stock back on, we don't want it taken off yet */
            $items = $order->getAllItems(); // Get all items from the order
            if ($items) {
                foreach($items as $item) {
                  $quantity = $item->getQtyOrdered(); // get Qty ordered
                  $product_id = $item->getProductId(); // get it's ID
                  
                  $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id); // Load the stock for this product
                  $stock->setQty($stock->getQty()+$quantity); // Set to new Qty            
                  $stock->save(); // Save
                  continue;                        
                }
           } 
           
           // Flag so that stock is only updated once!
           $_SESSION['stock_removed'] = $session->getLastRealOrderId();
        }
      }
    }
    
    // Remove from stock (if used)
    public function removeFromStock() {
    	
    	// Load the payment object
      	$payment = Mage::getModel('dibs/standard');
      
    	// Load the session object
      	$session = Mage::getSingleton('checkout/session');
     	$session->setDibsStandardQuoteId($session->getQuoteId());
      
      	// Load the order object
      	$order = Mage::getModel('sales/order');
      	$order->loadByIncrementId($session->getLastRealOrderId());
      
    	// remove items from stock
    	// http://www.magentocommerce.com/wiki/groups/132/protx_form_-_subtracting_stock_on_successful_payment
    	if (((int)$payment->getConfigData('handlestock')) == 1) {
	        $items = $order->getAllItems(); // Get all items from the order
	        if ($items) {
            foreach($items as $item) {
              $quantity = $item->getQtyOrdered(); // get Qty ordered
              $product_id = $item->getProductId(); // get it's ID
              
              $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id); // Load the stock for this product
              $stock->setQty($stock->getQty()-$quantity); // Set to new Qty            
              $stock->save(); // Save
              continue;                        
            }
	       }            
      }
    }

    /**
     * When a customer cancel payment from dibs.
     */
    public function cancelAction() {
    	$session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getDibsStandardQuoteId(true));
        // Save order comment
      	$order = Mage::getModel('sales/order');
      	$order->loadByIncrementId($session->getLastRealOrderId());
      	$order->addStatusToHistory(
        	$order->getStatus(),
          	$this->__('DIBS_LABEL_20')
      	);
      	$order->save();
        $this->_redirect('checkout/cart');
     }

    public function  successAction() {   
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getDibsStandardQuoteId(true));
        
        $order = Mage::getModel('sales/order');
        $payment = Mage::getModel('dibs/standard');
        
        // Load the order number
        if (Mage::getSingleton('checkout/session')->getLastOrderId()) {
          $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        } else {
          if (isset($_REQUEST["orderid"])) {
            $order->loadByIncrementId((int)$_REQUEST["orderid"]);
          } else {
            echo "<h1>An error occured!</h1>";
            echo "No orderid was supplied to the system!";
            exit();
          }
        }
        
        // Validate the order and send email confirmation if enabled
        if(!$order->getId()) {
          echo "<h1>An error occured!</h1>";
          echo "The order id was not known to the system";
          exit();
        }
        
        if (!isset($_REQUEST["amount"])) {
            echo "<h1>An error occured!</h1>";
            echo "No amount supplied to the system!";
            exit();
        }
        
        // Validate amount
        if ((int)$_REQUEST["amount"] != ($order->getTotalDue() * 100)) {
          echo "<h1>An error occured!</h1>";
          echo "The amount received from DIBS did not match the order amount!";
          exit();
        }
        
        if (!isset($_REQUEST["currency"])) {
            echo "<h1>An error occured!</h1>";
            echo "No currency (currency) supplied to the system!";
            exit();
        }
        
        // Validate currency
        if (((int)$payment->convertToDibsCurrency($order->getOrderCurrency())) != (int)$_REQUEST["currency"]) {
          echo "<h1>An error occured!</h1>";
          echo "The currency received from DIBS did not match the order currency!";
          exit();
        }
        
        // validate md5 if enabled
        if ($payment->getConfigData('md5key1') != "" && $payment->getConfigData('md5key2') != "") {
            $transact = $_REQUEST["transact"];
        	$amount = $order->getTotalDue() * 100; 
            $currency = $_REQUEST["currency"];
            $md5key1 = $payment->getConfigData('md5key1');
            $md5key2 = $payment->getConfigData('md5key2');
            $tmp = md5($md5key2.md5($md5key1.'transact='.$transact.'&amount='.$amount.'&currency='.$currency));

            if ((int)$payment->getConfigData('calcfee') == 1) {
            	$amount = $_REQUEST["amount"] + $_REQUEST["fee"];
  				$tmp = md5($md5key2.md5($md5key1.'transact='.$transact.'&amount='.$amount.'&currency='.$currency));          	
            }
            
            //
            // Validate authkey
            if ($tmp != $_REQUEST["authkey"]) {
              echo "<h1>An error occured!</h1>";
              echo "The MD5 key does not match!";
              exit();
            }
        }
        
    	// Remove items from stock if either not yet removed or only if stock handling is enabled
   		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
   		$row = $read->fetchRow("select * from dibs_order_status where orderid = '" . $_REQUEST['orderid'] . "'");
    	if ($row['status'] == '0') {
    		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        	$write->query('update dibs_order_status set transact = "' . ((isset($_REQUEST['transact'])) ? $_REQUEST['transact'] : '0') . '", status = 1, ' .
    					  'amount = "' . ((isset($_REQUEST['amount'])) ? $_REQUEST['amount'] : '0') . '", '.
    					  'currency = "' . ((isset($_REQUEST['currency'])) ? $_REQUEST['currency'] : '0') . '", '.
						  'paytype = "' . ((isset($_REQUEST['paytype'])) ? $_REQUEST['paytype'] : '0') . '", '.
						  'cardnomask = "' . ((isset($_REQUEST['cardnomask'])) ? $_REQUEST['cardnomask'] : '0') . '", '.
						  'cardprefix = "' . ((isset($_REQUEST['cardprefix'])) ? $_REQUEST['cardprefix'] : '0') . '", '.
						  'cardexpdate = "' . ((isset($_REQUEST['cardexpdate'])) ? $_REQUEST['cardexpdate'] : '0') . '", '.
						  'cardcountry = "' . ((isset($_REQUEST['cardcountry'])) ? $_REQUEST['cardcountry'] : '0') . '", '.
						  'acquirer = "' . ((isset($_REQUEST['acquirer'])) ? $_REQUEST['acquirer'] : '0') . '", '.
						  'enrolled = "' . ((isset($_REQUEST['enrolled'])) ? $_REQUEST['enrolled'] : '0') . '", '.
						  'fee = "' . ((isset($_REQUEST['fee'])) ? $_REQUEST['fee'] : '0') . '" where orderid = "' . $_REQUEST['orderid'] . '"');
	    
        	// Remove items from stock as the payment now has been made
	    	$this->removeFromStock();						
	    		
			// Send email order confirmation (if enabled). May be done only once!
       		if (((int)$payment->getConfigData('sendmailorderconfirmation')) == 1) {
           		$order->sendNewOrderEmail();
          	}
          	$this->setOrderStatusAfterPayment();
          	$session->setQuoteId($session->getDibsStandardQuoteId(true));
    	}
        // Save order comment
      	$order = Mage::getModel('sales/order');
      	$order->loadByIncrementId($session->getLastRealOrderId());
     	$order->addStatusToHistory(
   			$order->getStatus(),
      		$this->__('DIBS_LABEL_22')
      	);
      	$order->save();    		
        //redirect the user to the success page
        $this->_redirect('checkout/onepage/success');
  
    }
    
    public function callbackAction() {
    	$session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getDibsStandardQuoteId(true));
        
        $order = Mage::getModel('sales/order');
        $payment = Mage::getModel('dibs/standard');
        
        // Load the order number
        if (Mage::getSingleton('checkout/session')->getLastOrderId()) {
          $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        } else {
          if (isset($_REQUEST["orderid"])) {
            $order->loadByIncrementId((int)$_REQUEST["orderid"]);
          } else {
            echo "<h1>An error occured!</h1>";
            echo "No orderid was supplied to the system!";
            exit();
          }
        }
        
        // Validate the order and send email confirmation if enabled
        if(!$order->getId()) {
          echo "<h1>An error occured!</h1>";
          echo "The order id was not known to the system";
          exit();
        }
        
        if (!isset($_REQUEST["amount"])) {
            echo "<h1>An error occured!</h1>";
            echo "No amount supplied to the system!";
            exit();
        }
        
        // Validate amount
        if ((int)$_REQUEST["amount"] != (int)($order->getTotalDue() * 100)) {
          echo "<h1>An error occured!</h1>";
          echo "The amount received from DIBS did not match the order amount!";
          exit();
        }
        
        if (!isset($_REQUEST["currency"])) {
            echo "<h1>An error occured!</h1>";
            echo "No currency (currency) supplied to the system!";
            exit();
        }
        
        // Validate currency
        if (((int)$payment->convertToDibsCurrency($order->getOrderCurrency())) != (int)$_REQUEST["currency"]) {
          echo "<h1>An error occured!</h1>";
          echo "The currency received from DIBS did not match the order currency!";
          exit();
        }
        
        // validate md5 if enabled
        if ($payment->getConfigData('md5key1') != "" && $payment->getConfigData('md5key2') != "") {
            $transact = $_REQUEST["transact"];
        	$amount = $order->getTotalDue() * 100; 
            $currency = $_REQUEST["currency"];
            $md5key1 = $payment->getConfigData('md5key1');
            $md5key2 = $payment->getConfigData('md5key2');
            $tmp = md5($md5key2.md5($md5key1.'transact='.$transact.'&amount='.$amount.'&currency='.$currency));

            if ((int)$payment->getConfigData('calcfee') == 1) {
            	$amount = $_REQUEST["amount"] + $_REQUEST["fee"];
  				$tmp = md5($md5key2.md5($md5key1.'transact='.$transact.'&amount='.$amount.'&currency='.$currency));          	
            }
            
            //
            // Validate authkey
            if ($tmp != $_REQUEST["authkey"]) {
              echo "<h1>An error occured!</h1>";
              echo "The MD5 key does not match!";
              exit();
            }
        }
        
    	// Remove items from stock if either not yet removed or only if stock handling is enabled
   		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
   		$row = $read->fetchRow("select * from dibs_order_status where orderid = '" . $_REQUEST['orderid'] . "'");
    	if ($row['status'] == '0') {
    		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        	$write->query('update dibs_order_status set transact = "' . ((isset($_REQUEST['transact'])) ? $_REQUEST['transact'] : '0') . '", status = 1, ' .
    					  'amount = "' . ((isset($_REQUEST['amount'])) ? $_REQUEST['amount'] : '0') . '", '.
    					  'currency = "' . ((isset($_REQUEST['currency'])) ? $_REQUEST['currency'] : '0') . '", '.
						  'paytype = "' . ((isset($_REQUEST['paytype'])) ? $_REQUEST['paytype'] : '0') . '", '.
						  'cardnomask = "' . ((isset($_REQUEST['cardnomask'])) ? $_REQUEST['cardnomask'] : '0') . '", '.
						  'cardprefix = "' . ((isset($_REQUEST['cardprefix'])) ? $_REQUEST['cardprefix'] : '0') . '", '.
						  'cardexpdate = "' . ((isset($_REQUEST['cardexpdate'])) ? $_REQUEST['cardexpdate'] : '0') . '", '.
						  'cardcountry = "' . ((isset($_REQUEST['cardcountry'])) ? $_REQUEST['cardcountry'] : '0') . '", '.
						  'acquirer = "' . ((isset($_REQUEST['acquirer'])) ? $_REQUEST['acquirer'] : '0') . '", '.
						  'enrolled = "' . ((isset($_REQUEST['enrolled'])) ? $_REQUEST['enrolled'] : '0') . '", '.
						  'fee = "' . ((isset($_REQUEST['fee'])) ? $_REQUEST['fee'] : '0') . '" where orderid = "' . $_REQUEST['orderid'] . '"');
	    
        	// Remove items from stock as the payment now has been made
	    	$this->removeFromStock();						
	    		
			// Send email order confirmation (if enabled). May be done only once!
       		if (((int)$payment->getConfigData('sendmailorderconfirmation')) == 1) {
           		$order->sendNewOrderEmail();
          	}
          	$this->setOrderStatusAfterPayment();
          	$session->setQuoteId($session->getDibsStandardQuoteId(true));
          	
        	// Save order comment
      		$order = Mage::getModel('sales/order');
      		$order->loadByIncrementId($session->getLastRealOrderId());
      		$order->addStatusToHistory(
        		$order->getStatus(),
          		$this->__('DIBS_LABEL_21')
      		);
      		$order->save();
    	}
    	   	
        // redirect the user to the success page
      	$this->_redirect('checkout/onepage/success');
	}

}
