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
class LoginbymobileForgotPasswordModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
		$fpAction = Tools::getValue('fpAction');
		if ($fpAction == 'fpsendsms') {
			$this->actionLbmRequestSecureKey();
		} else if ($fpAction == 'fpbymobile') {
			$this->actionLbmVerifySecureKey();
		}
    }

    public function setInitialSession() {
		$mobile_number = Tools::getValue('otp_mobile_num');
		$id_country	 = Tools::getValue('lbm_id_country');
        $errors = array();
        $lbmpageid = '';
        $session_key = '';

        if (empty($mobile_number)) {
            $errors[] = $this->l('Enter your mobile number.');
        } else if (Validate::isPhoneNumber($mobile_number) && Configuration::get('LOGINBYMOBILE_ENABLE_LOGIN_FEATURE')) {
			//print_r($mobile_number.'========================');
			$email = Mobilenumlist::getCustomerEmail($mobile_number);
			//print_r($mobile_number.'========================');
			if ($email === false) {
				$errors[] = $this->l('Authentication failed. Check your phone number');
			} else {
				$customer = new Customer();
				$authentication = $customer->getByEmail(trim($email));
				if (isset($authentication->active) && !$authentication->active) {
					$errors[] = $this->l('Your account is not available at this time, please contact us');
				} else if (!$authentication || !$customer->id) {
					$errors[] = $this->l('Authentication failed.');
				} else {
					$LOGINBYMOBILE_OTP = Configuration::get('LOGINBYMOBILE_OTP');
					$LOGINBYMOBILE_OTP_list = unserialize($LOGINBYMOBILE_OTP);
					if (!isset($LOGINBYMOBILE_OTP_list[$id_country]) || empty($LOGINBYMOBILE_OTP_list[$id_country])) {
						//print_r('1111---'.$mobile_number.'--'. $password.'--'.$id_country);
						//die();
						//follow password reset procedure*************
						$this->actionNonTWFLogin($customer);
					} else {
						//print_r('2222---'.$mobile_number.'--'. $password.'--'.$id_country);
						//actionTWFLogin($customer);

						$length = Configuration::get('LOGINBYMOBILE_CODE_LENGTH');
						$message = $this->getRandomNumber($length);
						$sessionid = $this->getRandomNumber(8);

						$SMS_TEXT = Configuration::get('LOGINBYMOBILE_SMS_TEXT', (int)Context::getContext()->language->id);
						$shop_name = Configuration::get('PS_SHOP_NAME');
						$message_text = str_replace("{OTP}", $message, $SMS_TEXT);
						$message_text = str_replace("{shop}", $shop_name, $message_text);

						$provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
						$lbm_sms_sender = new LBMSMSSender();
						$subject = "Password OTP";
						$is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');
						$LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
						$LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
						if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
							$LOGINBYMOBILE_TXN_list[$id_country] = 0;
						}
						$is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];
						$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $mobile_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'Forgot Password OTP'.$message_text);
						$errors = array_merge($smsErrors, $errors);

						$params = array();
						$params['id_customer'] = $customer->id;
						$params['email'] = $email;
						$params['passwd'] = "";
						$params['mobile_num'] = $mobile_number;
						$params['id_country'] = $id_country;
						$params['last_session_gen'] = date('Y-m-d H:i:s', time());
						$params['session_key'] = $sessionid;
						$params['twowayfactor'] = $message;
						$params['type'] = "fp";
						$params['active'] = 0;
						Lbmsession::addLbmSession($params, "fp");

						$session_key = $sessionid;
						$lbmpageid = 'fp';
					}
				}
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
        $sessionid = trim(Tools::getValue('session_key'));
        $errors = array();
		if (empty($sessionid)) {
			$this->setInitialSession();
		} else {
            $sessionRow = Lbmsession::getLbmSession($sessionid);
            if (isset($sessionRow) && is_array($sessionRow) && !empty($sessionRow)) {
                $last_session_gen = $sessionRow['last_session_gen'];
                $id_country = $sessionRow['id_country'];
                if ((strtotime($last_session_gen.'+'.($min_time = (int)Configuration::get('LOGINBYMOBILE_SESSION_TIME')).' minutes') - time()) < 0) {
                    $errors[] = sprintf($this->l('Session expired. Please try again. Due to security reasons, you need to complete login process within %d minutes'), (int)$min_time);
                    $lbmpageid = 'root';
                } else {
                    $attempts = (int)$sessionRow['attempts'];
                    $allowedAttempts = (int)Configuration::get('LOGINBYMOBILE_ATTEMPTS');
                    if ($attempts > $allowedAttempts) {
                        $errors[] = $this->l('You have exceeded maximum allowed Message attempts. Kindly try again');
                        $lbmpageid = 'root';
                        Lbmsession::clearLbmSession($sessionid);
                        //get him out. retry
                    } else {
                        $phone_number = $sessionRow['mobile_num'];
                        if (Validate::isPhoneNumber($phone_number)) {
                            $id_customer = $sessionRow['id_customer'];
                            $message = $sessionRow['twowayfactor'];

                            //$enable_prefix = Configuration::get('LOGINBYMOBILE_ADD_PREFIX');
                            //if ($enable_prefix) {
                            //    $countries = new Country($id_country);
                            //    $phone_number = $countries->call_prefix.$phone_number;
                            //}

                            $SMS_TEXT = Configuration::get('LOGINBYMOBILE_SMS_TEXT', (int)Context::getContext()->language->id);
                            $shop_name = Configuration::get('PS_SHOP_NAME');
                            $message_text = str_replace("{OTP}", $message, $SMS_TEXT);
                            $message_text = str_replace("{shop}", $shop_name, $message_text);

                            /* Provider Change - Start */
                            //require_once(_PS_MODULE_DIR_.'loginbymobile/controllers/front/LBMSMSSender.php');
                            $provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
                            $lbm_sms_sender = new LBMSMSSender();
                            $subject = "Password OTP";
                            $is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');

                            $LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
                            $LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
                            if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
                                $LOGINBYMOBILE_TXN_list[$id_country] = 0;
                            }
                            $is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];

                            $smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'Forgot Password OTP '.$message_text);
							$errors = array_merge($smsErrors, $errors);

                            $params = array();
                            $params['session_key'] = $sessionid;
                            $params['attempts'] = $sessionRow['attempts'] + 1;
                            Lbmsession::updateLbmSessionAttempts($params);
                            $lbmpageid = 'fp';
                        } else {
                            $errors[] = $this->l('Invalid Phone number.');
                            $lbmpageid = 'root';
                        }
                    }
                }
            } else {
                $errors[] = $this->l('Please try again from the begining. Due to security reasons, you need to complete login process within 10 minutes');
                $lbmpageid = 'root';
            }
        }

        $return = array(
            'hasError' => !empty($errors),
            'errors' => $errors,
			'redirectlink' => $this->context->link->getPageLink('authentication', true, (int)Context::getContext()->language->id),
            'lbmpageid' => $lbmpageid,

        );
        die(Tools::jsonEncode($return));
    }
    public function actionLbmVerifySecureKey() {
        $sessionid = trim(Tools::getValue('session_key'));
		$phone_number = $lbm_fp_mobile_number = trim(Tools::getValue('lbm_fp_mobile_number'));
        $lbm_fp_otp = trim(Tools::getValue('lbm_fp_otp'));
        $id_country = (int)Tools::getValue('lbm_fp_id_country');
        $lbmpageid = '';
        $errors = array();
        $view = '';
        if (empty($sessionid)) {
			if (empty($lbm_fp_mobile_number)) {
				$errors[] = $this->l('Enter your mobile number.');
			} else if (Validate::isPhoneNumber($lbm_fp_mobile_number) && Configuration::get('LOGINBYMOBILE_ENABLE_LOGIN_FEATURE')) {
				//print_r($mobile_number.'========================');
				$email = Mobilenumlist::getCustomerEmail($lbm_fp_mobile_number);
				
				//print_r($mobile_number.'========================');
				if ($email === false) {
					$errors[] = $this->l('Authentication failed. Check your phone number');
				} else {
					$email = urldecode(trim($email));
					$customer = new Customer();
					$authentication = $customer->getByEmail($email);
					if (isset($authentication->active) && !$authentication->active) {
						$errors[] = $this->l('Your account is not available at this time, please contact us');
					} else if (!$authentication || !$customer->id) {
						$errors[] = $this->l('Authentication failed.');
					} else {
						$LOGINBYMOBILE_OTP = Configuration::get('LOGINBYMOBILE_OTP');
						$LOGINBYMOBILE_OTP_list = unserialize($LOGINBYMOBILE_OTP);
						if (!isset($LOGINBYMOBILE_OTP_list[$id_country]) || empty($LOGINBYMOBILE_OTP_list[$id_country])) {
/////////////Send Password
							$resultArray = $this->processPasSmsSend($lbm_fp_mobile_number, $id_country, $email);
							$errors = array_merge($resultArray, $errors);
						} else {
/////////////OTP / Session Key is missing / Error condition
							$errors[] = $this->l(' Technical Issue. Session details missing. OTP is required.');
							$lbmpageid = 'root';
						}
					}
				}
			} else {
				$errors[] = $this->l('Invalid mobile number.');
			}
		} else {
            $sessionRow = Lbmsession::getLbmSession($sessionid);
            if (isset($sessionRow) && is_array($sessionRow) && !empty($sessionRow)) {
                $last_session_gen = $sessionRow['last_session_gen'];
                if ((strtotime($last_session_gen.'+'.($min_time = (int)Configuration::get('LOGINBYMOBILE_SESSION_TIME')).' minutes') - time()) < 0) {
                    $errors[] = sprintf($this->l('Session expired. Please try again. Due to security reasons, you need to complete login process within %d minutes'), (int)$min_time);
                    $lbmpageid = 'root';
                    //redirect
                } else {
                    $phone_number = $sessionRow['mobile_num'];
                    if (Validate::isPhoneNumber($phone_number)) {
                        $id_country = $sessionRow['id_country'];
                        if (empty($lbm_fp_otp)) {
                            $errors[] = $this->l('Enter Secure Key. Kindly verify your Message.');
                            $lbmpageid = 'fp';

                            $params = array();
                            $params['session_key'] = $sessionid;
                            $params['attempts'] = $sessionRow['attempts'] + 1;
                            Lbmsession::updateLbmSessionAttempts($params);
                            //give him a chance
                            //enter otp - error
                        } else {
                            $twowayfactordb = $sessionRow['twowayfactor'];
                            if ($lbm_fp_otp == $twowayfactordb) {
                                $email = $sessionRow['email'];
								$email = urldecode(trim($email));
                                $customer = new Customer();
                                $authentication = $customer->getByEmail($email);
                                if (isset($authentication->active) && !$authentication->active) {
                                    $errors[] = $this->l('Your account isn\'t available at this time, please contact us');
                                    $lbmpageid = 'root';
                                } else if (!$authentication || !$customer->id) {
                                    $errors[] = $this->l('Unable to fetch your account details.');
                                    $lbmpageid = 'root';
                                } else {
                                    $phone_numberrow = Mobilenumlist::getMobileNum($authentication->id);
									//print_r(count($phone_numberrow));
									//die();
                                    if (!empty($phone_numberrow)) {
                                        $phone_number_db = $phone_numberrow['otp_mobile_num'];
                                        if (empty($phone_number_db)) {
											$errors[] = $this->l(' Technical Issue. Mobile number missing for the provided session.');
											$lbmpageid = 'root';
                                        } else {
                                            if ($phone_number == $phone_number_db) {
///////////////////////// send password
/////////////Send Password
												$resultArray = $this->processPasSmsSend($phone_number, $id_country, $email);
												$errors = array_merge($resultArray, $errors);
                                            } else {
                                                $errors[] = $this->l(' Technical Issue. Mobile number does not match our record.');
                                                $lbmpageid = 'root';
                                            }
                                        }
                                    } else {
										$errors[] = $this->l(' Technical Issue. Mobile number missing for the provided session.');
										$lbmpageid = 'root';
                                    }
                                }
                            } else {
                                $attempts = (int)$sessionRow['attempts'];
                                $allowedAttempts = (int)Configuration::get('LOGINBYMOBILE_ATTEMPTS');
                                if ($attempts > $allowedAttempts) {
                                    $errors[] = $this->l('You have exceeded maximum allowed attempts. Kindly try again');
                                    $lbmpageid = 'root';
                                    Lbmsession::clearLbmSession($sessionid);
                                    //get him out. retry
                                } else {
                                    $errors[] = $this->l('Incorrect Key. Kindly verify your Message correctly. You have ').($allowedAttempts - $attempts).' more attempts';
                                    $lbmpageid = 'fp';

                                    $params = array();
                                    $params['session_key'] = $sessionid;
                                    $params['attempts'] = $sessionRow['attempts'] + 1;
                                    Lbmsession::updateLbmSessionAttempts($params);
                                    //give him a chance
                                }
                                // otp does not match
                                // check number of attempts
                                // if attempt exceeds cancel session
                            }
                        }
                    } else {
                        $errors[] = $this->l('Invalid Phone number.');
                        $lbmpageid = 'getphone';
                        //invalid phone
                    }
                }
            } else {
                $errors[] = $this->l('Please try again from the begining. Due to security reasons, you need to complete login process within 10 minutes');
                $lbmpageid = 'root';
                //illegal access
                //redirect
            }
		}
		$countries = new Country($id_country, $this->context->language->id);
        $phone_number = "(+".$countries->call_prefix.")".$phone_number;
		$this->context->smarty->assign('lbm_fp_mobile_number', $phone_number);
		$this->context->smarty->assign('lbm_fp_country_name', $countries->name);
		$this->context->smarty->assign('lbm_fp_lf_url', Context::getContext()->shop->getBaseURI());
		


        $return = array(
			'session_key' => $sessionid,
			'id_country' => $id_country,
			'hasError' => !empty($errors),
			'redirectlink' => $this->context->link->getPageLink('authentication', true, (int)Context::getContext()->language->id),
			'page' => $this->context->smarty->fetch('module:loginbymobile/views/templates/hook/forgot_password_confirmation.tpl'),
			'errors' => $errors,
			'token' => Tools::getToken(false),
			'lbmpageid' => $lbmpageid
        );

		//print_r($return);
		//die();

        die(Tools::jsonEncode($return));
    }


/*public function initContent()
{
parent::initContent();		//$this->setTemplate('module:loginbymobile/views/templates/hook/forgot_password.tpl');
}*/

    public function processPasSmsSend($otp_mobile_num, $id_country, $email)
    {
		$errors = array();
        if ($email) {
            $customer = new Customer();
            $customer->getByemail($email);
            if (!Validate::isLoadedObject($customer)) {
                $errors[] = $this->l('Customer account not found');
            } else if (!$customer->active) {
                $errors[] = $this->l('You cannot regenerate the password for this account.');
            } else if ((strtotime($customer->last_passwd_gen.'+'.(int)Configuration::get('PS_PASSWD_TIME_FRONT').' minutes') - time()) > 0) {
				$errors[] = $this->l('You just Reset your password. you shall reset the password next time after ').strtotime($customer->last_passwd_gen.'+'.(int)Configuration::get('PS_PASSWD_TIME_FRONT').' minutes');
            } else {
                $customer->passwd = Tools::encrypt($password = Tools::passwdGen(MIN_PASSWD_LENGTH, 'NUMERIC'));
                $customer->last_passwd_gen = date('Y-m-d H:i:s', time());
                $shop = new shop($this->context->shop->id);
                if ($customer->update()) {
                    $message = $password;
                    $countries = new Country($id_country);
                    //$enable_prefix = Configuration::get('LOGINBYMOBILE_ADD_PREFIX');
                    $phone_number = $otp_mobile_num;
                    //if ($enable_prefix) {
                    //    $countries = new Country($id_country);
                    //    $phone_number = $countries->call_prefix.$otp_mobile_num;
                    //} else {
                    //    $phone_number = $otp_mobile_num;
                    //}
                    $customer_id = $customer->id;

                    $SMS_TEXT = Configuration::get('LOGINBYMOBILE_PASSWORD_TEXT', (int)Context::getContext()->language->id);
                    $customer_name = $customer->firstname;
                    $shop_name = Configuration::get('PS_SHOP_NAME');
                    $message_text = str_replace("{customername}", $customer_name, $SMS_TEXT);
                    $message_text = str_replace("{shop}", $shop_name, $message_text);
                    $message_text = str_replace("{pwd}", $message, $message_text);

                    /* Provider Change - Start */
                    require_once(_PS_MODULE_DIR_.'loginbymobile/controllers/front/LBMSMSSender.php');
                    $provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
                    $lbm_sms_sender = new LBMSMSSender();
                    $subject = "Password Reset";
                    $is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');
                    $LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
                    $LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
                    if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
                        $LOGINBYMOBILE_TXN_list[$id_country] = 0;
                    }
                    $is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];
                    $smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'PWD '.$message_text);
					$errors = array_merge($smsErrors, $errors);

                } else {
                    $errors[] = $this->l('An error occurred with your account, which prevents us from sending you a new password. Please report this issue using the contact form.');
                }
            }
        } else {
            $errors[] = $this->l('We cannot regenerate your password with the data you\'ve submitted.');
        }
		return $errors;
    }

	public function getRandomNumber($length) {
		$random_number = '';
		for ($i = 0; $i < $length; $i++) {
			$random_number .= mt_rand(0, 9);
		}
		return $random_number;
	}
}
