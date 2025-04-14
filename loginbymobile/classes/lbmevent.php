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

class Lbmevent extends ObjectModel
{
    public $id_lbmevent;
    public $event_name;
	public $hook;
    public $id_lbmsmsprovider;
    public $active;	

    public static $definition = array(
        'table' => 'lbmevent',
        'primary' => 'id_lbmevent',
        'fields' => array(
            'id_lbmevent' => array('type' => self::TYPE_INT),
            'event_name' => array('type' => self::TYPE_STRING),
	    'hook' => array('type' => self::TYPE_STRING),
            'id_lbmsmsprovider' => array('type' => self::TYPE_INT),   'active' => array('type' => self::TYPE_BOOL),			
        ),
    );
}
