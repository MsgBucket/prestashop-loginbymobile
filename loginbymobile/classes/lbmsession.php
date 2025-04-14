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

class Lbmsession extends ObjectModel
{
    public $id_lbmsession;
    public $id_customer;
    public $email;
    public $passwd;
    public $mobile_num;
    public $id_country;
    public $last_session_gen;
    public $session_key;
    public $twowayfactor;
    public $type;
    public $attempts;
    public $active;

    public static $definition = array(
        'table' => 'lbmsession',
        'primary' => 'id_lbmsession',
        'fields' => array(
            'id_lbmsession' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail'),
            'passwd' => array('type' => self::TYPE_STRING, 'validate' => 'isPasswd'),
            'mobile_num' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_country' => array('type' => self::TYPE_STRING, 'validate' => 'isUnsignedId'),
            'last_session_gen' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'session_key' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'twowayfactor' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'type' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'attempts' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL),
        ),
    );

    public static function addLbmSession($params, $type = "login")
    {
		if ($type === "fp") {
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'lbmsession` WHERE `mobile_num` = \''.pSQL($params['mobile_num']).'\' AND `id_country` = '.(int)$params['id_country'].' AND `type` = \'fp\'');
		} else if ($type === "ca") {
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'lbmsession` WHERE `mobile_num` = \''.pSQL($params['mobile_num']).'\' AND `id_country` = '.(int)$params['id_country'].' AND `type` = \'ca\'');			
		} else {
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'lbmsession` WHERE `id_customer` = '.(int)$params['id_customer'].'');
		}

        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'lbmsession` (`id_customer`, `email`, `passwd`,`mobile_num`, `id_country`, `last_session_gen`, `session_key`, `twowayfactor`, `active`) VALUES ('.(int)$params['id_customer'].',\''.pSQL($params['email']).'\',\''.pSQL($params['passwd']).'\',\''.pSQL($params['mobile_num']).'\','.(int)$params['id_country'].',\''.pSQL($params['last_session_gen']).'\',\''.pSQL($params['session_key']).'\',\''.pSQL($params['twowayfactor']).'\','.(int)$params['active'].')');
    }

    public static function updateLbmSession($params)
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'lbmsession` SET `mobile_num` = \''.pSQL($params['mobile_num']).'\', `id_country` = '.(int)$params['id_country'].', `twowayfactor` = \''.pSQL($params['twowayfactor']).'\'  WHERE `session_key` = \''.pSQL($params['session_key']).'\'');
    }

    public static function updateLbmSessionAttempts($params)
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'lbmsession` SET `attempts` = '.(int)$params['attempts'].' WHERE `session_key` = \''.pSQL($params['session_key']).'\'');
    }
	
    public static function updateLbmSessionFlag($params)
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'lbmsession` SET `active` = '.(int)$params['active'].' WHERE `session_key` = \''.pSQL($params['session_key']).'\'');
    }	

    public static function clearLbmSession($sessionkey)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'lbmsession` WHERE `sessionkey` = \''.pSQL($sessionkey).'\'');
    }

    public static function getLbmSession($sessionkey)
    {
        $row = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'lbmsession` WHERE `session_key` = \''.pSQL($sessionkey).'\'');
        return $row;
    }

}
