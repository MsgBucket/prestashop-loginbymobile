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

function upgrade_module_17_0_48($module)
{
	
	Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lbmorderdelivery` (
		`id_lbmorderdelivery` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_customer` int(11) UNSIGNED NOT NULL,
		`id_order` int(11) UNSIGNED NOT NULL,
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


	$sql = array();
	$sql[1] = 'ALTER TABLE  `'._DB_PREFIX_.'mobilenumlist` ADD `id_lang` int(2) UNSIGNED NOT NULL DEFAULT 0';
	/*$checksql = 'SELECT * FROM `'._DB_PREFIX_.'mobilenumlist`';
	$result = Db::getInstance()->getRow($checksql);
	if (is_array($result) && count($result) > 0) {
		if (!isset($result['id_lang'])) {
			
		}
	}*/

	foreach ($sql as $query) {
		try{
			Db::getInstance()->execute($query);
		} catch(Exception $e) {}
	}
	return true;	
}