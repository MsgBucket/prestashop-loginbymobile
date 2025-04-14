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

function upgrade_module_17_0_49($module)
{
	$sql = array();
	$checksql = 'SELECT * FROM `'._DB_PREFIX_.'lbmorderdelivery`';
	$result = Db::getInstance()->getRow($checksql);
	if (is_array($result) && count($result) > 0) {
		if (!isset($result['email'])) {
			$sql[1] = 'ALTER TABLE  `'._DB_PREFIX_.'lbmorderdelivery` ADD `email` varchar(128) DEFAULT NULL';
		}
	}

	foreach ($sql as $query) {
		try{
			Db::getInstance()->execute($query);
		} catch(Exception $e) {}
	}
	return true;	
}