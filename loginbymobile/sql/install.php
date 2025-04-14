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

Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mobilenumlist` (
                `id_mobilenumlist` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `otp_mobile_num` VARCHAR(32) DEFAULT NULL,
                `otp_flag` tinyint(1) unsigned NOT NULL DEFAULT 0,
                `id_customer` int(10) unsigned NOT NULL,
                `id_country` int(10) unsigned NOT NULL,
                `is_guest` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
                `id_lang` int(3) UNSIGNED NOT NULL DEFAULT \'0\',				
                PRIMARY KEY (`id_mobilenumlist`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

	Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lbmorderdelivery` (
		`id_lbmorderdelivery` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_customer` int(11) UNSIGNED NOT NULL,
		`id_order` int(11) UNSIGNED NOT NULL,
		`email` varchar(128) DEFAULT NULL,
        `phone_number` VARCHAR(32) DEFAULT NULL,		
		`id_country` int(11) UNSIGNED NOT NULL,
		`transactional` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
		`unicode` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',		
		`message` VARCHAR(500),		
		`delivered_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`attempts` int(3) UNSIGNED NOT NULL  DEFAULT 1,
		`active` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
		PRIMARY KEY (`id_lbmorderdelivery`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lbmsmslog` (
            `id_lbmsmslog` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `provider` VARCHAR(64),
            `simulation` tinyint(1) unsigned NOT NULL DEFAULT 1,
            `environment` tinyint(1) unsigned NOT NULL DEFAULT 1,
            `destination` VARCHAR(64),
            `status` VARCHAR(64),
            `message` VARCHAR(250),
            `unicode` tinyint(1) unsigned NOT NULL DEFAULT 1,
            `transactional` tinyint(1) unsigned NOT NULL DEFAULT 1,
            `sent_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `customer_address` VARCHAR(64),
            PRIMARY KEY (`id_lbmsmslog`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

Db::getInstance()->execute('
CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lbmsession` (
`id_lbmsession` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`id_customer` int(10) UNSIGNED NOT NULL,
`email` varchar(128) NOT NULL,
`passwd` varchar(32) NOT NULL,
`mobile_num` VARCHAR(32) DEFAULT NULL,
`id_country` int(10) unsigned NOT NULL,
`last_session_gen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`session_key` varchar(32) NOT NULL DEFAULT \'0\',
`twowayfactor` varchar(32) NOT NULL DEFAULT \'0\',
`type` varchar(32) NOT NULL DEFAULT \'\',
`attempts` int(3) UNSIGNED NOT NULL  DEFAULT 1,
`active` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
PRIMARY KEY (`id_lbmsession`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lbmfaillog` (
`id_lbmfaillog` int(11) NOT NULL AUTO_INCREMENT,
`providername` varchar(32) NULL,
`request` text NULL,
`response` text NULL,
`timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
PRIMARY KEY  (`id_lbmfaillog`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'mobilenumlist`  ADD INDEX(`id_customer`);');

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