<?php
/**
 * Copyright ePay | Dit Online Betalingssystem, (c) 2009.
 * Modifications copyrighted by  DIBS | Secure Payment Services, (c) 2009.
 */
class Mage_Dibs_Block_Standard_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('dibs/standard/form.phtml');
        parent::_construct();
    }
}