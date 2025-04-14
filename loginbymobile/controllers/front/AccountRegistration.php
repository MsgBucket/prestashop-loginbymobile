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

require_once(_PS_MODULE_DIR_.'loginbymobile/classes/mobilenumlist.php');
require_once(_PS_MODULE_DIR_.'loginbymobile/controllers/front/LBMSMSSender.php');
require_once(_PS_MODULE_DIR_.'loginbymobile/classes/lbmsession.php');
class LoginbymobileAccountRegistrationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
		$caAction = Tools::getValue('caAction');
		if ($caAction == 'casendsms') {
			$this->actionLbmRequestSecureKey();
		}
    }

    public function mobileNumExists($otp_mobile_num, $return_id = false)
    {
        $result = Mobilenumlist::getIdByMobileNum($otp_mobile_num);
        return ($return_id ? (int)$result : (bool)$result);
    }
	
    public function setInitialSession($mobile_number, $id_country, $smsAttempt = 0) {
        $errors = array();
        $lbmpageid = '';
        $session_key = '';

        if (empty($mobile_number)) {
            $errors[] = $this->l('Enter your mobile number.');
        } else if (Validate::isPhoneNumber($mobile_number)) {
			if ($this->mobileNumExists($mobile_number)) {
				$errors[] = $this->l('An account using this Mobile Number has already been registered. Please register a different Mobile Number (or) Login with your Mobile number.');				
			} else {				
				$length = Configuration::get('LOGINBYMOBILE_CODE_LENGTH');
				$message = $this->getRandomNumber($length);
				$sessionid = $this->getRandomNumber(8);

				$SMS_TEXT = Configuration::get('LOGINBYMOBILE_SMS_TEXT', (int)Context::getContext()->language->id);
				$shop_name = Configuration::get('PS_SHOP_NAME');
				$message_text = str_replace("{OTP}", $message, $SMS_TEXT);
				$message_text = str_replace("{shop}", $shop_name, $message_text);

				$provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
				$lbm_sms_sender = new LBMSMSSender();
				$subject = "Registration OTP";
				$is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');
				$LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
				$LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
				if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
					$LOGINBYMOBILE_TXN_list[$id_country] = 0;
				}
				$is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];
				$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $mobile_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'Registration OTP '.$message_text);
				$errors = array_merge($smsErrors, $errors);

				$params = array();
				$params['id_customer'] = 0;
				$params['email'] = "";
				$params['passwd'] = "";
				$params['mobile_num'] = $mobile_number;
				$params['id_country'] = $id_country;
				$params['last_session_gen'] = date('Y-m-d H:i:s', time());
				$params['session_key'] = $sessionid;
				$params['twowayfactor'] = $message;
				if (!empty($smsAttempt)) {
					$params['attempts'] = $smsAttempt;
				}
				$params['type'] = "ca";
				$params['active'] = 0;
				Lbmsession::addLbmSession($params, "ca");

				$session_key = $sessionid;
				$lbmpageid = 'ca';
			}
        } else {
			$errors[] = $this->l('Invalid mobile number.');
        }
        $return = array(
			'hasError' => !empty($errors),
			'lbmpageid' => $lbmpageid,
			'session_key' => $session_key,
			'redirectlink' => $this->context->link->getPageLink('authentication', true, (int)Context::getContext()->language->id),
			'errors' => $errors,
			'token' => Tools::getToken(false)
        );

        die(Tools::jsonEncode($return));
    }


    public function actionLbmRequestSecureKey() {
        $sessionid = trim(Tools::getValue('session_key_reg'));
		$mobile_number_ui = Tools::getValue('otp_mobile_num');
		$id_country_ui	 = Tools::getValue('lbm_id_country');		
        $errors = array();
		if (empty($sessionid)) {
			// TO DO:: IP verification for SMS attempts
			$this->setInitialSession($mobile_number_ui, $id_country_ui);
		} else {
            $sessionRow = Lbmsession::getLbmSession($sessionid);
            if (isset($sessionRow) && is_array($sessionRow) && !empty($sessionRow)) {
                $phone_number = $sessionRow['mobile_num'];				
                $id_country = $sessionRow['id_country'];
				$attempts = (int)$sessionRow['attempts'];
				if ($phone_number == $mobile_number_ui && $id_country == $id_country_ui) {
					$last_session_gen = $sessionRow['last_session_gen'];
					if ((strtotime($last_session_gen.'+'.($min_time = (int)Configuration::get('LOGINBYMOBILE_SESSION_TIME')).' minutes') - time()) < 0) {
						$errors[] = sprintf($this->l('Session expired. Please try again. Due to security reasons, you need to complete registration process within %d minutes'), (int)$min_time);
						$lbmpageid = 'root';
					} else {
						$allowedAttempts = (int)Configuration::get('LOGINBYMOBILE_ATTEMPTS');
						if ($attempts > $allowedAttempts) {
							$errors[] = $this->l('You have exceeded maximum allowed Message attempts. Kindly try again');
							$lbmpageid = 'root';
							Lbmsession::clearLbmSession($sessionid);
							//get him out. retry
						} else {
							if (Validate::isPhoneNumber($phone_number)) {
								$message = $sessionRow['twowayfactor'];
								$SMS_TEXT = Configuration::get('LOGINBYMOBILE_SMS_TEXT', (int)Context::getContext()->language->id);
								$shop_name = Configuration::get('PS_SHOP_NAME');
								$message_text = str_replace("{OTP}", $message, $SMS_TEXT);
								$message_text = str_replace("{shop}", $shop_name, $message_text);

								$provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
								$lbm_sms_sender = new LBMSMSSender();
								$subject = "Registration OTP r";
								$is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');

								$LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
								$LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
								if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
									$LOGINBYMOBILE_TXN_list[$id_country] = 0;
								}
								$is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];

								$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'Registration OTP '.$message_text);
								$errors = array_merge($smsErrors, $errors);

								$params = array();
								$params['session_key'] = $sessionid;
								$params['attempts'] = $sessionRow['attempts'] + 1;
								Lbmsession::updateLbmSessionAttempts($params);
								$lbmpageid = 'ca';
							} else {
								$errors[] = $this->l('Invalid Phone number.');
								$lbmpageid = 'root';
							}
						}
					}					
				} else {
					Lbmsession::clearLbmSession($sessionid); 				
					$this->setInitialSession($mobile_number_ui, $id_country_ui);
				}
            } else {
                $errors[] = $this->l('Please try again from the begining. Due to security reasons, you need to complete login process within 10 minutes');
                $lbmpageid = 'root';
            }
        }

        $return = array(
			'hasError' => !empty($errors),
			'lbmpageid' => $lbmpageid,
			'session_key' => $sessionid,
			'redirectlink' => $this->context->link->getPageLink('authentication', true, (int)Context::getContext()->language->id),
			'errors' => $errors,
			'token' => Tools::getToken(false)
        );		
        die(Tools::jsonEncode($return));
    }

	public function getRandomNumber($length) {
		$random_number = '';
		for ($i = 0; $i < $length; $i++) {
			$random_number .= mt_rand(0, 9);
		}
		return $random_number;
	}	
}
