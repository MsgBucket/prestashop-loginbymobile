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

$sql = array();
$sql[1] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'lbmsmslog';
$sql[2] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'lbmsession';
$sql[3] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'lbmfaillog';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
