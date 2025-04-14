<?php
/**
* Please do not edit or add any code in this file without the permission of MsgBucket
*
* @author    MsgBucket
* @copyright MsgBucket
* @license   https://www.msgbucket.com
* Prestashop version 1.7+
* loginbymobile 17.0.0
* Sep 2018
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_17_0_50($module)
{

	//$module->uninstallTabs();
    //$module->installTabs();
	Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lbmsmsprovider` (
		`id_lbmsmsprovider` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		`alias` VARCHAR(64) NOT NULL DEFAULT \'\',
		`url` VARCHAR(128) NOT NULL DEFAULT \'\',		
		`user_name_key` VARCHAR(32) NOT NULL DEFAULT \'user_name\',
		`password_key` VARCHAR(32) NOT NULL DEFAULT \'password\',
		`sender_id_key` VARCHAR(32) NOT NULL DEFAULT \'sender_id\',
		`to_key` VARCHAR(32) NOT NULL DEFAULT \'to\',
		`message_key` VARCHAR(32) NOT NULL DEFAULT \'message\',
		`unicode_key` VARCHAR(32) NOT NULL DEFAULT \'\',
		`flash_key` VARCHAR(32) NOT NULL DEFAULT \'\',
		`transactional_key` VARCHAR(32) NOT NULL DEFAULT \'\',
		`addon_one_key` VARCHAR(32) NOT NULL DEFAULT \'\',
		`addon_two_key` VARCHAR(32) NOT NULL DEFAULT \'\',
		`user_name` VARCHAR(64) NOT NULL DEFAULT \'user_name\',
		`password` VARCHAR(128) NOT NULL DEFAULT \'password\',
		`sender_id` VARCHAR(32) NOT NULL DEFAULT \'sender_id\',
		`unicode` VARCHAR(4) NOT NULL DEFAULT \'1\',
		`flash` VARCHAR(4) NOT NULL DEFAULT \'1\',
		`transactional` VARCHAR(4) NOT NULL DEFAULT \'1\',
		`addon_one` VARCHAR(32) NOT NULL DEFAULT \'\',
		`addon_two` VARCHAR(32) NOT NULL DEFAULT \'\',
		`processing_type` VARCHAR(32) NOT NULL DEFAULT \'curl\',
		`live` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
		PRIMARY KEY (`id_lbmsmsprovider`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');
		
	Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lbmevent` (
		`id_lbmevent` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		`event_name` VARCHAR(64) NOT NULL DEFAULT \'\',
		`hook` VARCHAR(64) NOT NULL DEFAULT \'\',		
		`id_lbmsmsprovider` INT UNSIGNED NOT NULL,
		`active` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
		PRIMARY KEY (`id_lbmevent`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

	return true;	
}