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
require_once(_PS_MODULE_DIR_.'loginbymobile/classes/lbmsession.php');
require_once(_PS_MODULE_DIR_.'loginbymobile/controllers/front/LBMSMSSender.php');
class LoginbymobileLBMAuthModuleFrontController extends ModuleFrontController
{

	public function getRandomNumber($length) {
		$random_number = '';
		for ($i = 0; $i < $length; $i++) {
			$random_number .= mt_rand(0, 9);
		}
		return $random_number;
	}

    public function actionNonTWFLogin($customer) {
        //$this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
        $this->context->cookie->id_customer = (int)($customer->id);
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->logged = 1;
        $customer->logged = 1;
        $this->context->cookie->is_guest = $customer->isGuest();
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->email = $customer->email;

        // Add customer to the context
        $this->context->customer = $customer;

        if (Configuration::get('PS_CART_FOLLOWING')
			&& (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0)
			&& $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
            $this->context->cart = new Cart($id_cart);
        } else {
            $id_carrier = (int)$this->context->cart->id_carrier;
            $this->context->cart->id_carrier = 0;
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
            $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
        }
        $this->context->cart->id_customer = (int)$customer->id;
        $this->context->cart->secure_key = $customer->secure_key;
        $ajax = 0;
        if ($ajax && isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
            $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
            $this->context->cart->setDeliveryOption($delivery_option);
        }

        $this->context->cart->save();
        $this->context->cookie->id_cart = (int)$this->context->cart->id;
        $this->context->cookie->write();
        $this->context->cart->autosetProductAddress();
	if (version_compare(_PS_VERSION_, '1.7.6.6', '>')) {
		$this->context->cookie->registerSession(new CustomerSession());	
	}
        Hook::exec('actionAuthentication', array('customer' => $this->context->customer));

        // Login information have changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);
        $this->authRedirection = false;

        //$back = Tools::getValue('back',$this->l('my-account'));
		$back = Tools::getValue('back', 'my-account');
		$back = $this->getUrlRewrite($back, (int)Context::getContext()->language->id);		
        if ($back == Tools::secureReferrer($back)) {
            $back = html_entity_decode($back);
        } else {
            $back = $this->context->link->getPageLink($back, true, (int)Context::getContext()->language->id);
            //$back = Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : $back));
        }

        $return = array(
			'hasError' => false,
			'redirectlink' => $back,
			'lbmpageid' => 'redirect',
			'token' => Tools::getToken(false),
        );
        die(Tools::jsonEncode($return));
    }


    public function processMobileLogin($mobile_number, $password, $id_country) {
		///print_r($mobile_number.'--'. $password.'--'.$id_country);

        $errors = array();
        $lbmpageid = '';
        $session_key = '';

        if (empty($mobile_number)) {
            $errors[] = $this->l('Enter your mobile number.');
        } else if (Validate::isPhoneNumber($mobile_number) && Configuration::get('LOGINBYMOBILE_ENABLE_LOGIN_FEATURE')) {
            if (empty($password)) {
                $errors[] = $this->l('Password is required.');
            } else if (!Validate::isPasswd($password)) {
                $errors[] = $this->l('Invalid password.');
            } else {
				//print_r($mobile_number.'========================');
                $email = Mobilenumlist::getCustomerEmail($mobile_number);
				//print_r($mobile_number.'========================');
                if ($email === false) {
                    $errors[] = $this->l('Authentication failed. Check your phone number & password.');
                } else {
                    $customer = new Customer();
                    $authentication = $customer->getByEmail(trim($email), trim($password));
                    if (isset($authentication->active) && !$authentication->active) {
                        $errors[] = $this->l('Your account is not available at this time, please contact us');
                    } else if (!$authentication || !$customer->id) {
                        $errors[] = $this->l('Authentication failed.');
                    } else {
                        $LOGINBYMOBILE_OTP = Configuration::get('LOGINBYMOBILE_OTP');
						$LOGINBYMOBILE_TWOWAYFACTOR = Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR');
                        $LOGINBYMOBILE_OTP_list = unserialize($LOGINBYMOBILE_OTP);
                        if (empty($LOGINBYMOBILE_TWOWAYFACTOR) || !isset($LOGINBYMOBILE_OTP_list[$id_country]) || empty($LOGINBYMOBILE_OTP_list[$id_country])) {
							//print_r('1111---'.$mobile_number.'--'. $password.'--'.$id_country);
							//die();
                            $this->actionNonTWFLogin($customer);
                        } else {
							//print_r('2222---'.$mobile_number.'--'. $password.'--'.$id_country);
							//actionTWFLogin($customer);

							$length = Configuration::get('LOGINBYMOBILE_CODE_LENGTH');
							$message = $this->getRandomNumber($length);
							$sessionid = $this->getRandomNumber(8);

							$SMS_TEXT = Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR_TEXT', (int)Context::getContext()->language->id);
							$shop_name = Configuration::get('PS_SHOP_NAME');
							$message_text = str_replace("{OTP}", $message, $SMS_TEXT);
							$message_text = str_replace("{shop}", $shop_name, $message_text);

							$provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
							$lbm_sms_sender = new LBMSMSSender();
							$subject = "Login Two-way-factor";
							$is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');
							$LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
							$LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
							if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
								$LOGINBYMOBILE_TXN_list[$id_country] = 0;
							}
							$is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];
							$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $mobile_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'Mobile TWF OTP'.$message_text);
							$errors = array_merge($smsErrors, $errors);
							$params = array();
							$params['id_customer'] = $customer->id;
							$params['email'] = $email;
							$params['passwd'] = Tools::encrypt($password);
							$params['mobile_num'] = $mobile_number;
							$params['id_country'] = $id_country;
							$params['last_session_gen'] = date('Y-m-d H:i:s', time());
							$params['session_key'] = $sessionid;
							$params['twowayfactor'] = $message;
							$params['active'] = 0;
							$this->addLbmSession($params);

							$session_key = $sessionid;
							$lbmpageid = 'verifysecurekey';
						}
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
			'id_country' => $id_country,
			'errors' => $errors,
			'token' => Tools::getToken(false)
        );

        die(Tools::jsonEncode($return));
    }

    private function getLbmSession($sessionkey)
    {
        $row = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'lbmsession` WHERE `session_key` = \''.pSQL($sessionkey).'\'');
        return $row;
    }

    private function addLbmSession($params)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'lbmsession` WHERE `id_customer` = '.(int)$params['id_customer'].'');

        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'lbmsession` (`id_customer`, `email`, `passwd`,`mobile_num`, `id_country`, `last_session_gen`, `session_key`, `twowayfactor`, `active`) VALUES ('.(int)$params['id_customer'].',\''.pSQL($params['email']).'\',\''.pSQL($params['passwd']).'\',\''.pSQL($params['mobile_num']).'\','.(int)$params['id_country'].',\''.pSQL($params['last_session_gen']).'\',\''.pSQL($params['session_key']).'\',\''.pSQL($params['twowayfactor']).'\','.(int)$params['active'].')');
    }

    private function updateLbmSession($params)
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'lbmsession` SET `mobile_num` = \''.pSQL($params['mobile_num']).'\', `id_country` = '.(int)$params['id_country'].', `twowayfactor` = \''.pSQL($params['twowayfactor']).'\'  WHERE `session_key` = \''.pSQL($params['session_key']).'\'');
    }

    private function updateLbmSessionAttempts($params)
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'lbmsession` SET `attempts` = '.(int)$params['attempts'].' WHERE `session_key` = \''.pSQL($params['session_key']).'\'');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitLoginFormMobile')) {
			$id_country = (int)Tools::getValue('lbm_lf_id_country');
			//print_r(Tools::getValue('lbm_lf_mobile_number'));
			$mobile_number = trim(Tools::getValue('lbm_lf_mobile_number'));
			$password = Tools::getValue('lbm_lf_password');
			$this->processMobileLogin($mobile_number, urldecode(trim($password)), $id_country);
        } else {
			$twowayfactoraction = Tools::getValue('twowayfactoraction');
			if ($twowayfactoraction == 'loginbyemail') {
				$this->actionLoginByEmail();
			} else if ($twowayfactoraction == 'getphone') {
				$this->actionLbmGetPhone();
			} else if ($twowayfactoraction == 'requestsecurekey') {
				$this->actionLbmRequestSecureKey();
			} else if ($twowayfactoraction == 'verifysecurekey') {
				$this->actionLbmVerifySecureKey();
			}
		}
    }

    public function actionLoginByEmail() {
        $errors = array();
        $lbmpageid = '';
        $session_key = '';
        $email = trim(Tools::getValue('email'));
        $passwd = trim(Tools::getValue('passwd'));
		$id_country = 0;
        if (empty($email)) {
            $errors[] = $this->l('Enter your mobile number (OR) email address.');
        } else {
            // email is entered
            if (empty($passwd)) {
                $errors[] = $this->l('Password is required.');
            } else if (!Validate::isPasswd($passwd)) {
                $errors[] = $this->l('Invalid password.');
            } else {
                $customer = new Customer();
                $authentication = $customer->getByEmail(urldecode(trim($email)), urldecode(trim($passwd)));
                if (isset($authentication->active) && !$authentication->active) {
                    $errors[] = $this->l('Your account isn\'t available at this time, please contact us');
                } else if (!$authentication || !$customer->id) {
                    $errors[] = $this->l('Authentication failed.');
                } else {
                    $phone_numberrow = Mobilenumlist::getMobileNum($authentication->id);
                    $phone_number = $phone_numberrow['otp_mobile_num'];
					$id_country = (int)$phone_numberrow['id_country'];
                    if (empty($phone_number)) {
                        $sessionid = '';
                        for ($i = 0; $i < 8; $i++) {
                            $sessionid .= mt_rand(0, 9);
                        }

                        $params = array();
                        $params['id_customer'] = $customer->id;
                        $params['email'] = $email;
                        $params['passwd'] = Tools::encrypt($passwd);
                        $params['mobile_num'] = '';
                        $params['id_country'] = $id_country;
                        $params['last_session_gen'] = date('Y-m-d H:i:s', time());
                        $params['session_key'] = $sessionid;
                        $params['twowayfactor'] = '';
                        $params['active'] = 0;
                        $this->addLbmSession($params);

                        $session_key = $sessionid;
                        $lbmpageid = 'getphone';
                    } else {
                        $LOGINBYMOBILE_OTP = Configuration::get('LOGINBYMOBILE_OTP');
                        $LOGINBYMOBILE_OTP_list = unserialize($LOGINBYMOBILE_OTP);
                        if (!isset($LOGINBYMOBILE_OTP_list[$id_country]) || empty($LOGINBYMOBILE_OTP_list[$id_country])) {
                            $this->actionLoginNonOTPCountryClient($customer);
                        }

                        //$enable_prefix = Configuration::get('LOGINBYMOBILE_ADD_PREFIX');
                        //if ($enable_prefix) {
                        //    $countries = new Country($id_country);
                        //    $phone_number = $countries->call_prefix.$phone_number;
                        //}
                        $length = Configuration::get('LOGINBYMOBILE_CODE_LENGTH');
                        $message = '';
                        for ($i = 0; $i < $length; $i++) {
                            $message .= mt_rand(0, 9);
                        }

                        $sessionid = '';
                        for ($i = 0; $i < 8; $i++) {
                            $sessionid .= mt_rand(0, 9);
                        }

                        $SMS_TEXT = Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR_TEXT', (int)Context::getContext()->language->id);
                        $shop_name = Configuration::get('PS_SHOP_NAME');
                        $message_text = str_replace("{OTP}", $message, $SMS_TEXT);
                        $message_text = str_replace("{shop}", $shop_name, $message_text);

                        /* Provider Change - Start */
                        //require_once(_PS_MODULE_DIR_.'loginbymobile/controllers/front/LBMSMSSender.php');
                        $provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
                        $lbm_sms_sender = new LBMSMSSender();
                        $subject = "Login Twowayfactor";
                        $is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');
                        $LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
                        $LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
                        if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
                            $LOGINBYMOBILE_TXN_list[$id_country] = 0;
                        }
                        $is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];
                        $smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'Email TWF OTP '.$message_text);
						$errors = array_merge($smsErrors, $errors);
                        // store session / securekey / uname/ pwd / phone number
                        $params = array();
                        $params['id_customer'] = $customer->id;
                        $params['email'] = $email;
                        $params['passwd'] = Tools::encrypt($passwd);
                        $params['mobile_num'] = $phone_number;
                        $params['id_country'] = $id_country;
                        $params['last_session_gen'] = date('Y-m-d H:i:s', time());
                        $params['session_key'] = $sessionid;
                        $params['twowayfactor'] = $message;
                        $params['active'] = 0;
                        $this->addLbmSession($params);

                        $session_key = $sessionid;
                        $lbmpageid = 'verifysecurekey';
                    }
                }
            }
        }
        $return = array(
        'hasError' => !empty($errors),
        'lbmpageid' => $lbmpageid,
        'session_key' => $session_key,
        'id_country' => $id_country,
        'errors' => $errors,
        'token' => Tools::getToken(false)
        );
        die(Tools::jsonEncode($return));
    }

    public function actionLbmGetPhone() {
        $sessionid = trim(Tools::getValue('session_key'));
        $phone_number = trim(Tools::getValue('lbmphonenumber'));
        $id_country = trim(Tools::getValue('lbm_id_country'));
        $lbmpageid = '';
        $errors = array();
        $view = '';
        if (!empty($sessionid)) {
            if (Validate::isPhoneNumber($phone_number)) {
                $sessionRow = $this->getLbmSession($sessionid);
                if (isset($sessionRow) && is_array($sessionRow) && !empty($sessionRow)) {
                    $last_session_gen = $sessionRow['last_session_gen'];
                    if ((strtotime($last_session_gen.'+'.($min_time = (int)Configuration::get('LOGINBYMOBILE_SESSION_TIME')).' minutes') - time()) < 0) {
                        $errors[] = sprintf($this->l('Session expired. Please try again. Due to security reasons, you need to complete login process within %d minutes'), (int)$min_time);
                        $lbmpageid = 'root';
                        //redirect
                    } else {
                        $id_customer = $sessionRow['id_customer'];

                        if ($this->mobileNumExists($phone_number)) {
                            $errors[] = $this->l(' Mobile number already registered. Enter a different number or login by mobile number.');
                        } else {
                            //$this->updateMobileNum($phone_number, $id_customer, 0, $id_country);
                            $LOGINBYMOBILE_OTP = Configuration::get('LOGINBYMOBILE_OTP');
                            $LOGINBYMOBILE_OTP_list = unserialize($LOGINBYMOBILE_OTP);
                            if (!isset($LOGINBYMOBILE_OTP_list[$id_country]) || empty($LOGINBYMOBILE_OTP_list[$id_country])) {
                                $customer = new Customer($id_customer);
                                $this->actionLoginNonOTPCountryClient($customer);
                            }
                            //$enable_prefix = Configuration::get('LOGINBYMOBILE_ADD_PREFIX');
                            //if ($enable_prefix) {
                            //    $countries = new Country($id_country);
                            //    $phone_number = $countries->call_prefix.$phone_number;
                            //}
                            $length = Configuration::get('LOGINBYMOBILE_CODE_LENGTH');
                            $message = '';
                            for ($i = 0; $i < $length; $i++) {
                                $message .= mt_rand(0, 9);
                            }

                            $SMS_TEXT = Configuration::get('LOGINBYMOBILE_SMS_TEXT', (int)Context::getContext()->language->id);
                            $shop_name = Configuration::get('PS_SHOP_NAME');
                            $message_text = str_replace("{OTP}", $message, $SMS_TEXT);
                            $message_text = str_replace("{shop}", $shop_name, $message_text);

                            /* Provider Change - Start */
                            //require_once(_PS_MODULE_DIR_.'loginbymobile/controllers/front/LBMSMSSender.php');
                            $provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
                            $lbm_sms_sender = new LBMSMSSender();
                            $subject = "Login Twowayfactor";
                            $is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');

                            $LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
                            $LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
                            if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
                                $LOGINBYMOBILE_TXN_list[$id_country] = 0;
                            }
                            $is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];
                            $smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'Update Mobile OTP '.$message_text);
							$errors = array_merge($smsErrors, $errors);
                            // store session / securekey / uname/ pwd / phone number
                            $params = array();
                            $params['mobile_num'] = $phone_number;
                            $params['id_country'] = $id_country;
                            $params['session_key'] = $sessionid;
                            $params['twowayfactor'] = $message;
                            $this->updateLbmSession($params);
                            $lbmpageid = 'verifysecurekey';
                        }
                    }
                } else {
                    $errors[] = $this->l('Please try again from the begining. Due to security reasons, you need to complete login process within 10 minutes');
                    $lbmpageid = 'root';
                    //illegal access
                    //redirect
                }
            } else {
                $errors[] = $this->l('Invalid Phone number.');
                $lbmpageid = 'getphone';
                //invalid phone
            }
        } else {
            $errors[] = $this->l('Please try again from the begining. Due to security reasons, you need to complete login process within 10 minutes');
            $lbmpageid = 'root';
            // illegal access
            //redirect
        }
        $return = array(
        'hasError' => !empty($errors),
        'lbmpageid' => $lbmpageid,
        'session_key' => $sessionid,
        'id_country' => $id_country,
        'errors' => $errors,
        'token' => Tools::getToken(false),
        );
        die(Tools::jsonEncode($return));
    }

    public function actionLbmRequestSecureKey() {
        $sessionid = trim(Tools::getValue('session_key'));
        $errors = array();
        $lbmpageid = 'verifysecurekey';
        if (!empty($sessionid)) {
            $sessionRow = $this->getLbmSession($sessionid);
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
                        $errors[] = $this->l('You have exceeded maximum allowed SMS attempts. Kindly try again');
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
                            $subject = "Login Twowayfactor";
                            $is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');

                            $LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
                            $LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
                            if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
                                $LOGINBYMOBILE_TXN_list[$id_country] = 0;
                            }
                            $is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];

                            $smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'TWF RESEND OTP '.$message_text);
							$errors = array_merge($smsErrors, $errors);
                            $params = array();
                            $params['session_key'] = $sessionid;
                            $params['attempts'] = $sessionRow['attempts'] + 1;
                            $this->updateLbmSessionAttempts($params);

                            $lbmpageid = 'verifysecurekey';
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
        } else {
            $errors[] = $this->l('Please try again from the begining. Due to security reasons, you need to complete login process within 10 minutes');
            $lbmpageid = 'root';
        }
        $return = array(
            'hasError' => !empty($errors),
            'errors' => $errors,
            'lbmpageid' => $lbmpageid,
        );
        die(Tools::jsonEncode($return));
    }

    public function actionLbmVerifySecureKey() {
        $sessionid = trim(Tools::getValue('session_key'));
        $logintwofactorotp = trim(Tools::getValue('logintwofactorotp'));
        $lbmpageid = '';
        $errors = array();
        $view = '';
        $id_country = '';
        if (!empty($sessionid)) {
            $sessionRow = $this->getLbmSession($sessionid);
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
                        if (empty($logintwofactorotp)) {
                            $errors[] = $this->l('Enter Secure Key. Kindly verify your SMS.');
                            $lbmpageid = 'verifysecurekey';

                            $params = array();
                            $params['session_key'] = $sessionid;
                            $params['attempts'] = $sessionRow['attempts'] + 1;
                            $this->updateLbmSessionAttempts($params);
                            //give him a chance
                            //enter otp - error
                        } else {
                            $twowayfactordb = $sessionRow['twowayfactor'];
                            if ($logintwofactorotp == $twowayfactordb) {
                                $email = $sessionRow['email'];
                                $customer = new Customer();
                                $authentication = $customer->getByEmail(urldecode(trim($email)));
                                if (isset($authentication->active) && !$authentication->active) {
                                    $errors[] = $this->l('Your account isn\'t available at this time, please contact us');
                                    $lbmpageid = 'root';
                                } else if (!$authentication || !$customer->id) {
                                    $errors[] = $this->l('Authentication failed.');
                                    $lbmpageid = 'root';
                                } else {
                                    $phone_numberrow = Mobilenumlist::getMobileNum($authentication->id);
                                    if (is_array($phone_numberrow) && count($phone_numberrow) === 1) {
                                        $phone_number_db = $phone_numberrow['otp_mobile_num'];
                                        if (empty($phone_number_db)) {
                                            if ($this->mobileNumExists($phone_number)) {
                                                $errors[] = $this->l(' Mobile number already registered. Enter a different number or login by mobile number.');
                                                $lbmpageid = 'getphone';
                                            } else {
                                                $this->updateMobileNum($phone_number, $customer->id, 1, $id_country);
                                            }
                                        } else {
                                            if ($phone_number == $phone_number_db) {
                                                $this->updateMobileNum($phone_number, $customer->id, 1, $id_country);
                                            } else {
                                                $errors[] = $this->l(' Technical Issue. Mobile number does not match our record.');
                                                $lbmpageid = 'root';
                                            }
                                        }
                                    } else {
                                        $this->updateMobileNum($phone_number, $customer->id, 1, $id_country);
                                    }

                                    if (empty($errors)) {
                                        //$this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
                                        $this->context->cookie->id_customer = (int)($customer->id);
                                        $this->context->cookie->customer_lastname = $customer->lastname;
                                        $this->context->cookie->customer_firstname = $customer->firstname;
                                        $this->context->cookie->logged = 1;
                                        $customer->logged = 1;
                                        $this->context->cookie->is_guest = $customer->isGuest();
                                        $this->context->cookie->passwd = $customer->passwd;
                                        $this->context->cookie->email = $customer->email;

                                        // Add customer to the context
                                        $this->context->customer = $customer;

                                        if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
                                            $this->context->cart = new Cart($id_cart);
                                        } else {
                                            $id_carrier = (int)$this->context->cart->id_carrier;
                                            $this->context->cart->id_carrier = 0;
                                            $this->context->cart->setDeliveryOption(null);
                                            $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                                            $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                                        }
                                        $this->context->cart->id_customer = (int)$customer->id;
                                        $this->context->cart->secure_key = $customer->secure_key;
                                        $ajax = 0;
                                        if ($ajax && isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                                            $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
                                            $this->context->cart->setDeliveryOption($delivery_option);
                                        }

                                        $this->context->cart->save();
                                        $this->context->cookie->id_cart = (int)$this->context->cart->id;
                                        $this->context->cookie->write();
                                        $this->context->cart->autosetProductAddress();
	if (version_compare(_PS_VERSION_, '1.7.6.6', '>')) {
		$this->context->cookie->registerSession(new CustomerSession());	
	}

                                        Hook::exec('actionAuthentication', array('customer' => $this->context->customer));

                                        // Login information have changed, so we check if the cart rules still apply
                                        CartRule::autoRemoveFromCart($this->context);
                                        CartRule::autoAddToCart($this->context);
                                        $this->authRedirection = false;

                                        //$back = Tools::getValue('back',$this->l('my-account'));
		$back = Tools::getValue('back', 'my-account');
		$back = $this->getUrlRewrite($back, (int)Context::getContext()->language->id);										
                                        if ($back == Tools::secureReferrer($back)) {
                                            $back = html_entity_decode($back);
                                        } else {
                                            $back = $this->context->link->getPageLink($back, true, (int)Context::getContext()->language->id);
                                            //$back = Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : $back));
                                        }

                                        $return = array(
                                            'hasError' => false,
                                            'redirectlink' => $back,
                                        );
                                        die(Tools::jsonEncode($return));
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
                                    $errors[] = $this->l('Incorrect Key. Kindly verify your SMS correctly.');
                                    $lbmpageid = 'verifysecurekey';

                                    $params = array();
                                    $params['session_key'] = $sessionid;
                                    $params['attempts'] = $sessionRow['attempts'] + 1;
                                    $this->updateLbmSessionAttempts($params);
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
        } else {
            $errors[] = $this->l('Please try again from the begining. Due to security reasons, you need to complete login process within 10 minutes');
            $lbmpageid = 'root';
            // illegal access
            //redirect
        }
        $return = array(
        'session_key' => $sessionid,
        'id_country' => $id_country,
        'hasError' => !empty($errors),
        'errors' => $errors,
        'token' => Tools::getToken(false),
        'lbmpageid' => $lbmpageid
        );
        die(Tools::jsonEncode($return));
    }

    public function actionLoginNonOTPCountryClient($customer) {
        //$this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
        $this->context->cookie->id_customer = (int)($customer->id);
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->logged = 1;
        $customer->logged = 1;
        $this->context->cookie->is_guest = $customer->isGuest();
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->email = $customer->email;

        // Add customer to the context
        $this->context->customer = $customer;

        if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
            $this->context->cart = new Cart($id_cart);
        } else {
            $id_carrier = (int)$this->context->cart->id_carrier;
            $this->context->cart->id_carrier = 0;
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
            $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
        }
        $this->context->cart->id_customer = (int)$customer->id;
        $this->context->cart->secure_key = $customer->secure_key;
        $ajax = 0;
        if ($ajax && isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
            $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
            $this->context->cart->setDeliveryOption($delivery_option);
        }

        $this->context->cart->save();
        $this->context->cookie->id_cart = (int)$this->context->cart->id;
        $this->context->cookie->write();
        $this->context->cart->autosetProductAddress();
	if (version_compare(_PS_VERSION_, '1.7.6.6', '>')) {
		$this->context->cookie->registerSession(new CustomerSession());	
	}
        Hook::exec('actionAuthentication', array('customer' => $this->context->customer));

        // Login information have changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);
        $this->authRedirection = false;

        //$back = Tools::getValue('back', $this->l('my-account'));
		$back = Tools::getValue('back', 'my-account');
		$back = $this->getUrlRewrite($back, (int)Context::getContext()->language->id);
        if ($back == Tools::secureReferrer($back)) {
            $back = html_entity_decode($back);
        } else {
            $back = $this->context->link->getPageLink($back, true, (int)Context::getContext()->language->id);
            //$back = Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : $back));
        }

        $return = array(
        'hasError' => false,
        'redirectlink' => $back,
        'lbmpageid' => 'redirect',
        'token' => Tools::getToken(false),
        );
        die(Tools::jsonEncode($return));
    }

    public function getUrlRewrite($pageName, $idLang)
    {
		if (Configuration::get('PS_REWRITING_SETTINGS')) {
			$metas = Meta::getMetaByPage($pageName, $idLang);
			if (is_array($metas)) {
				$url_rewrite = (isset($metas['url_rewrite']) && $metas['url_rewrite']) ? $metas['url_rewrite'] : $pageName;
				return $url_rewrite;
			}
		} else {
			$pageName = $this->context->link->getPageLink($pageName, true, (int)$idLang);	
		}
		return $pageName;
    }

    public function updateMobileNum($mobile_number, $id_customer, $otp_flag, $country_id)
    {
        $mobileNumList = null ;
        $id_mobilenumlist = Mobilenumlist::getIdByCustomer($id_customer);

        if (!empty($id_mobilenumlist)) {
            $mobileNumList = new Mobilenumlist($id_mobilenumlist);
        } else {
            $mobileNumList = new Mobilenumlist();
        }
            $mobileNumList->otp_mobile_num = $mobile_number;
            $mobileNumList->otp_flag = $otp_flag;
            $mobileNumList->id_customer = $id_customer;
			$mobileNumList->id_lang = (int)Context::getContext()->language->id;
            $mobileNumList->id_country = $country_id;
            $mobileNumList->save();
    }

    public function mobileNumExists($otp_mobile_num, $return_id = false)
    {
        $result = Mobilenumlist::getIdByMobileNum($otp_mobile_num);
        return ($return_id ? (int)$result : (bool)$result);
    }


}
