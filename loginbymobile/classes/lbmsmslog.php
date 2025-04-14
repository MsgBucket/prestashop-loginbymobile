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

class LbmSMSLog extends ObjectModel
{
    public $id_lbmsmslog;
    public $provider;
    public $simulation;
    public $environment;
    public $destination;
    public $status;
    public $message;
    public $unicode;
    public $transactional;
    public $sent_at;
    public $customer_address;

    public static $definition = array(
        'table' => 'lbmsmslog',
        'primary' => 'id_lbmsmslog',
        'fields' => array(
            'id_lbmsmslog' => array('type' => self::TYPE_INT),
            'provider' => array('type' => self::TYPE_STRING),
            'simulation' => array('type' => self::TYPE_BOOL),
            'environment' => array('type' => self::TYPE_BOOL),
            'destination' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_STRING),
            'message' => array('type' => self::TYPE_STRING),
            'unicode' => array('type' => self::TYPE_BOOL),
            'transactional' => array('type' => self::TYPE_BOOL),
            'sent_at' => array('type' => self::TYPE_DATE),
            'customer_address' => array('type' => self::TYPE_STRING),
        ),
    );
}
