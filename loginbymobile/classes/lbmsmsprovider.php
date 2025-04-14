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

class Lbmsmsprovider extends ObjectModel
{
    public $id_lbmsmsprovider;
    public $alias;
    public $url;
    public $user_name_key;
    public $password_key;
    public $sender_id_key;
    public $to_key;
    public $message_key;
    public $unicode_key;
    public $flash_key;
    public $transactional_key;
    public $addon_one_key;
    public $addon_two_key;
    public $user_name;
    public $password;
    public $sender_id;
    public $unicode;
    public $flash;
    public $transactional;
    public $addon_one;
    public $addon_two;
    public $processing_type;
    public $live;

    public static $definition = array(
        'table' => 'lbmsmsprovider',
        'primary' => 'id_lbmsmsprovider',
        'fields' => array(
            'id_lbmsmsprovider' => array('type' => self::TYPE_INT),
            'alias' => array('type' => self::TYPE_STRING,),
	    'url' => array('type' => self::TYPE_STRING,),
            'user_name_key' => array('type' => self::TYPE_STRING,),
            'password_key' => array('type' => self::TYPE_STRING,),
            'sender_id_key' => array('type' => self::TYPE_STRING,),
            'to_key' => array('type' => self::TYPE_STRING,),
            'message_key' => array('type' => self::TYPE_STRING,),
            'unicode_key' => array('type' => self::TYPE_STRING,),
            'flash_key' => array('type' => self::TYPE_STRING,),
            'transactional_key' => array('type' => self::TYPE_STRING,),
            'addon_one_key' => array('type' => self::TYPE_STRING,),
            'addon_two_key' => array('type' => self::TYPE_STRING,),
            'user_name' => array('type' => self::TYPE_STRING,),
            'password' => array('type' => self::TYPE_STRING,),
            'sender_id' => array('type' => self::TYPE_STRING,),
            'unicode' => array('type' => self::TYPE_STRING,),
            'flash' => array('type' => self::TYPE_STRING,),
            'transactional' => array('type' => self::TYPE_STRING,),
            'addon_one' => array('type' => self::TYPE_STRING,),
            'addon_two' => array('type' => self::TYPE_STRING,),
            'processing_type' => array('type' => self::TYPE_STRING,),
            'live' => array('type' => self::TYPE_BOOL),
        ),
    );
}
