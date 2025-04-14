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

require_once(_PS_MODULE_DIR_.'loginbymobile/classes/lbmfaillog.php');
require_once(_PS_MODULE_DIR_.'loginbymobile/classes/lbmsmslog.php');

class LBMSMSSender
{
	public $errors;
	public $id_lang;
    public function getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, $messageType)
    {
		$messageType = Tools::substr($messageType, 0, 245);
		$this->id_lang = (int)Context::getContext()->language->id;
		$this->errors = array();
        $enable_prefix = Configuration::get('LOGINBYMOBILE_ADD_PREFIX');
        if ($enable_prefix && $id_country != 5) {
            $countries = new Country($id_country);
            $phone_number = $countries->call_prefix.$phone_number;
        }

        switch ($provider) {
            case 'msgbucketin':
                $key = Configuration::get('LOGINBYMOBILE_MSGBUCKETIN_KEY');
				
                if ($simulate) {
                    $this->insertLbmSMSLog($provider, 1, 1, $phone_number, 'SUCCESS', $messageType, $is_unicode, $is_transactional, '');
                } else {
                    $this->sendMessagemsgbucketin($key, $phone_number, $message_text, $is_unicode);
                    $this->insertLbmSMSLog($provider, 0, 1, $phone_number, 'SUCCESS', $messageType, $is_unicode, $is_transactional, '');
                }
            break;
			
			case 'msgbucketus':
                $key = Configuration::get('LOGINBYMOBILE_MSGBUCKETUS_KEY');
				
                if ($simulate) {
                    $this->insertLbmSMSLog($provider, 1, 1, $phone_number, 'SUCCESS', $messageType, $is_unicode, $is_transactional, '');
                } else {
                    $this->sendMessagemsgbucketus($key, $phone_number, $message_text, $is_unicode);
                    $this->insertLbmSMSLog($provider, 0, 1, $phone_number, 'SUCCESS', $messageType, $is_unicode, $is_transactional, '');
                }
            break;

    	    case 'generic':
                $UserName = Configuration::get('LOGINBYMOBILE_GENERIC_UNAME');
                $Password = Configuration::get('LOGINBYMOBILE_GENERIC_PWD');
                $simulate = (int)Configuration::get('LOGINBYMOBILE_GENERIC_SIMULATE');
                $url = Configuration::get('LOGINBYMOBILE_GENERIC_URL');
                $from = Configuration::get('LOGINBYMOBILE_GENERIC_FROM');

                if ($simulate) {
                    $this->insertLbmSMSLog($provider, 1, 1, $phone_number, 'SUCCESS', $messageType, $is_unicode, $is_transactional, '');
                } else {
                    $this->sendMessageGeneric($url, $UserName, $Password, $message_text, $from, $phone_number, $is_unicode);
                    $this->insertLbmSMSLog($provider, 0, 1, $phone_number, 'SUCCESS', $messageType, $is_unicode, $is_transactional, '');
                }
                break;			
    
        }
		return $this->errors;
    }

    public function insertLbmSMSLog($provider, $simulation, $environment, $destination, $status, $message, $unicode, $transactional, $customer_address)
    {
        $lbmsmslog = new LbmSMSLog();
        $lbmsmslog->provider = $provider;
        $lbmsmslog->simulation = $simulation;
        $lbmsmslog->environment = $environment;
        $lbmsmslog->destination = $destination;
        $lbmsmslog->status = $status;
        $lbmsmslog->message = $message;
        $lbmsmslog->unicode = $unicode;
        $lbmsmslog->transactional = $transactional;
        $lbmsmslog->sent_at = date('Y-m-d H:i:s');
        $lbmsmslog->customer_address = '';
        $lbmsmslog->add();
    }

    private static function hexChars($data)
    {
        $mb_hex = '';
        for ($i = 0; $i < mb_strlen($data, 'UTF-8'); $i++) {
            $c = mb_substr($data, $i, 1, 'UTF-8');
            $o = unpack('N', mb_convert_encoding($c, 'UCS-4BE', 'UTF-8'));
            $mb_hex .= sprintf('%04X', $o[1]);
        }
        return $mb_hex;
    }

    public function sendMessageGeneric($url, $UserName, $Password, $Message, $from, $MsgDestinations, $is_unicode)
    {
	
		$uname_text = Configuration::get('LOGINBYMOBILE_GENERIC_UNAME_TXT');
		$pwd_text = Configuration::get('LOGINBYMOBILE_GENERIC_PWD_TXT');
		$from_text = Configuration::get('LOGINBYMOBILE_GENERIC_FROM_TXT');
		$to_text = Configuration::get('LOGINBYMOBILE_GENERIC_TO_TXT');
		$msg_text = Configuration::get('LOGINBYMOBILE_GENERIC_MSG_TXT');
		$uni_text = Configuration::get('LOGINBYMOBILE_GENERIC_UNI_TXT');
		$log = (int)Configuration::get('LOGINBYMOBILE_LOG');
		/*$content =  $url.'?'.$uname_text.'='.rawurlencode($UserName).
			'&'.$pwd_text.'='.rawurlencode($Password).
			'&'.$to_text.'='.rawurlencode($MsgDestinations).
			'&'.$from_text.'='.rawurlencode($from).
			'&'.$msg_text.'='.rawurlencode($Message).
			'&'.$uni_text.'='.rawurlencode($is_unicode);*/
		$content =  $url.'?'.$uname_text.'='.rawurlencode($UserName).
			'&'.$pwd_text.'='.rawurlencode($Password).
			'&'.$to_text.'='.rawurlencode($MsgDestinations).
			'&'.$from_text.'='.rawurlencode($from).
			'&'.$msg_text.'='.rawurlencode($Message);			
		
        try{

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $content);
            curl_setopt($curl, CURLOPT_FAILONERROR, 1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 15);
            $result = curl_exec($curl);
            if ($log) {
                $response = json_decode($result, true);
                $info = curl_getinfo($curl);
                $headerText = '';
                if (isset($info['request_header'])) {
                    $headerText = $info['request_header'];
                }
                $this->insertLbmFailLog('Generic SMS', $content, serialize($response).' header '.$headerText);
            }			
            curl_close($curl);
        } catch (Exception $e) {
            return $e;
        }
    }	
    
    public function sendMessageMsgbucketin($key, $mobiles, $message, $is_unicode)
    {
		$key = Configuration::get('LOGINBYMOBILE_MSGBUCKETIN_KEY');
        $nodeurl = "https://server.msgbucket.com/send";
		$data = array(
			'token' => $key,
			'receiver' => $mobiles,
			'msgtext' => $message
		);
        
        $log = (int)Configuration::get('LOGINBYMOBILE_LOG');
        try{
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($ch, CURLOPT_URL, $nodeurl);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
        } catch (Exception $e) {
            return $e;
        }
    }
	
	public function sendMessageMsgbucketus($key, $mobiles, $message, $is_unicode)
    {
		$key = Configuration::get('LOGINBYMOBILE_MSGBUCKETUS_KEY');
        $nodeurl = "https://server-us.msgbucket.com/send";
		$data = array(
			'token' => $key,
			'receiver' => $mobiles,
			'msgtext' => $message
		);
        
        $log = (int)Configuration::get('LOGINBYMOBILE_LOG');
        try{
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($ch, CURLOPT_URL, $nodeurl);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
        } catch (Exception $e) {
            return $e;
        }
    }
    
    public function insertLbmFailLog($provider, $request, $response)
    {
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'lbmfaillog` (`providername`, `request`, `response`) VALUES (\''.pSQL($provider).'\',\''.pSQL($request).'\',\''.pSQL($response).'\')');
    }


}