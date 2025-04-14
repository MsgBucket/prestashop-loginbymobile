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

class Mobilenumlist extends ObjectModel
{
    public $id_mobilenumlist;
    public $otp_mobile_num;
    public $otp_flag;
    public $id_customer;
    public $id_country;
    public $is_guest;
	public $id_lang;

    public static $definition = array(
        'table' => 'mobilenumlist',
        'primary' => 'id_mobilenumlist',
        'fields' => array(
            'id_mobilenumlist' => array('type' => self::TYPE_INT),
            'otp_mobile_num' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'otp_flag' => array('type' => self::TYPE_STRING, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_STRING, 'validate' => 'isUnsignedId'),
            'id_country' => array('type' => self::TYPE_STRING, 'validate' => 'isUnsignedId'),
            'is_guest' => array('type' => self::TYPE_BOOL),
            'id_lang' => array('type' => self::TYPE_INT),			
        ),
    );

    public static function getCountryID($otp_mobile_num)
    {
        $id_country = Db::getInstance()->getValue('
                    SELECT id_country
                    FROM '._DB_PREFIX_.'mobilenumlist
                    WHERE  otp_mobile_num = '.pSQL($otp_mobile_num));
        return $id_country;
    }

    public static function getIdByMobileNum($otp_mobile_num)
    {
        $otp_mobile_num = empty($otp_mobile_num) ? 'noval' : $otp_mobile_num;
        $id_mobilenumlist = Db::getInstance()->getValue('SELECT `id_mobilenumlist` FROM `'._DB_PREFIX_.'mobilenumlist` WHERE otp_mobile_num = \''.pSQL($otp_mobile_num).'\'');
        return $id_mobilenumlist;
    }

    public static function getIdByCustomer($id_customer)
    {
        $id_mobilenumlist = Db::getInstance()->getValue('SELECT `id_mobilenumlist` FROM `'._DB_PREFIX_.'mobilenumlist` WHERE id_customer = '.(int)$id_customer);
        return $id_mobilenumlist;
    }

    public static function deleteCustomerRecords($customerIdsString)
    {
    	Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mobilenumlist` WHERE `id_customer` IN ('.pSQL($customerIdsString).')');
	return true;
    }

    public static function getMobileNum($id_customer)
    {
        $results = Db::getInstance()->getRow('
                SELECT `otp_mobile_num`, `otp_flag`, `id_country`, `id_lang`
                FROM `'._DB_PREFIX_.'mobilenumlist`
                WHERE `id_customer` = '.(int)$id_customer.'');
        return $results;
    }

    public static function getCustomerEmail($login_mobile_num)
    {
        $result = Db::getInstance()->getValue('
                SELECT `id_customer`
                FROM `'._DB_PREFIX_.'mobilenumlist`
                WHERE `otp_mobile_num` = \''.pSQL($login_mobile_num).'\'
                ');
        if (empty($result)) {
            return $result;
        } else {
            $email = Db::getInstance()->getValue('
                SELECT `email`
                FROM `'._DB_PREFIX_.'customer`
                WHERE `id_customer` = '.(int)$result.'');
            return $email;
        }
    }

    public static function getRegistrationDataByCustomerId($id_customer)
    {
        if (empty($id_customer)) {
            return array();
        }
        $registrationData = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'mobilenumlist` WHERE id_customer = '.(int)$id_customer);
        return $registrationData;
    }

    public static function checkIfMobileNumberExistsAndActive($mobile_num, $id_country)
    {
        if (empty($mobile_num) || empty($id_country)) {
            return false;
        }
        $id_mobilenumlist = Db::getInstance()->getValue('SELECT `id_mobilenumlist` FROM `'._DB_PREFIX_.'mobilenumlist` WHERE otp_mobile_num = \''.pSQL($mobile_num).'\' AND otp_flag = 1 AND id_country = '.(int)$id_country);
        return (bool)$id_mobilenumlist;
    }

    public static function getCustomerEmailByMobileAndCountry($login_mobile_num, $id_country)
    {
        $result = Db::getInstance()->getValue('
                SELECT `id_customer`
                FROM `'._DB_PREFIX_.'mobilenumlist`
                WHERE `otp_mobile_num` = \''.pSQL($login_mobile_num).'\' AND `id_country` = '.(int)$id_country);
        if (empty($result)) {
            return false;
        } else {
            $email = Db::getInstance()->getValue('
                SELECT `email`
                FROM `'._DB_PREFIX_.'customer`
                WHERE `id_customer` = '.(int)$result.'');
            return $email;
        }
    }
}
