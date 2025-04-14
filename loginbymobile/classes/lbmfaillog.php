<?php
/**
 * Please do not edit or add any code in this file without the permission of MsgBucket
 *
 * @author    MsgBucket
 * @copyright MsgBucket
 * @license   https://www.msgbucket.com
 * Prestashop version 1.7+
 * trackdelhivery 3.0.9
 * June 2017
 */

class LbmFailLog extends ObjectModel
{
    public $id_lbmfaillog;
    public $providername;
    public $request;
    public $response;
    public $timestamp;

    public static $definition = array(
            'table' => 'lbmfaillog',
            'primary' => 'id_lbmfaillog',
            'fields' => array(
                    'providername' => array('type' => self::TYPE_STRING),
                    'request' => array('type' => self::TYPE_STRING),
                    'response' => array('type' => self::TYPE_STRING),
                    'timestamp' => array('type' => self::TYPE_DATE),
                ),
    );
}
