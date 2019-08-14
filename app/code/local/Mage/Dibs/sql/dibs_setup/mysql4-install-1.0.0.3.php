<?php
/**
 * Copyright ePay | Dit Online Betalingssystem, (c) 2009.
 * Modifications copyrighted by  DIBS | Secure Payment Services, (c) 2009.
 */

$installer = $this;
/* @var $installer Mage_Dibs_Model_Mysql4_Setup */

$installer->startSetup();

$installer->run("

		delete from {$installer->getTable('core_resource')} where code = 'dibs_setup';
		
		CREATE TABLE if not exists `dibs_order_status` (
  	`orderid` VARCHAR(45) NOT NULL,
  	`transact` VARCHAR(50) NOT NULL,
  	`status` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 = unpaid, 1 = paid',
  	`amount` VARCHAR(45) NOT NULL,
  	`currency` VARCHAR(45) NOT NULL,
  	`paytype` VARCHAR(45) NOT NULL,
  	`cardnomask` VARCHAR(45) NOT NULL,
  	`cardprefix` VARCHAR(45) NOT NULL,
  	`cardexpdate` VARCHAR(45) NOT NULL,
  	`cardcountry` VARCHAR(45) NOT NULL,
  	`acquirer` VARCHAR(45) NOT NULL,
  	`enrolled` VARCHAR(45) NOT NULL,
  	`fee` VARCHAR(45) NOT NULL
		);
		
    ");


$installer->endSetup();