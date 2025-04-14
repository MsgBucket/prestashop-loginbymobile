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
require_once(_PS_MODULE_DIR_.'loginbymobile/classes/mobilenumlist.php');
require_once(_PS_MODULE_DIR_.'loginbymobile/classes/lbmsession.php');
require_once(_PS_MODULE_DIR_.'loginbymobile/controllers/front/LBMSMSSender.php');
class Loginbymobile extends Module
{
    public $tabName = 'renderForm';
    public $tabsArray = array();
    public $parentTabClass = 'AdminLBM';
	public $default_language = 0;
	public $default_country = 0;

    public function __construct()
    {
        $this->name = 'loginbymobile';
        $this->tab = 'mobile';
        $this->version = '1.1.0';
        $this->author = 'MsgBucket';
        $this->need_instance = 1;
        $this->module_key = '';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Login By Whatsapp');
        $this->description = $this->l('Allow your Customers to Login/Register their accounts securely by OTP on their Mobile Phone Number. Send order notifications to your customers directly on their registered mobile phone number.');
        $this->confirmUninstall = $this->l('Un-install LOGIN BY MOBILE module? Kindly back-up mobilenumlist table. Customer Registered Phone Numbers are stored in mobilenumlist. You will not be able to see Registered Phone Numbers if you uninstall this module');

        $this->tabsArray = array(
            'AdminMobileByCustomer' => 'Mobile Numbers',
			'AdminLbmEventSmsProvider' => 'Events',
			'AdminLbmSmsProvider' => 'Message Providers',
            'AdminLbmSMSLog' => 'Message Log',
            'AdminLbmFailLog' => 'Fail Log',
        );
        $this->tabsArray_hidden = array();
		$this->default_country = (int)$this->context->country->id;
		$this->default_language = (int)$this->context->language->id;
    }

    public function install()
    {
        Configuration::updateValue('LOGINBYMOBILE_LOG', 0);
        Configuration::updateValue('LOGINBYMOBILE_SMS_PROVIDER', 'msgbucketin');

     /* Provider Change - Start */
        
        
        Configuration::updateValue('LOGINBYMOBILE_MSGBUCKETIN_KEY', 'key');
		Configuration::updateValue('LOGINBYMOBILE_MSGBUCKETUS_KEY', 'key');
        
        Configuration::updateValue('LOGINBYMOBILE_GENERIC_URL', 'https://api.generic.com/send.php');
        Configuration::updateValue('LOGINBYMOBILE_GENERIC_UNAME', 'Username Value');
        Configuration::updateValue('LOGINBYMOBILE_GENERIC_SIMULATE', 1);
        Configuration::updateValue('LOGINBYMOBILE_GENERIC_PWD', 'Password Value');
        Configuration::updateValue('LOGINBYMOBILE_GENERIC_FROM', 'Sender ID');
		Configuration::updateValue('LOGINBYMOBILE_GENERIC_UNAME_TXT', 'Username Parameter Name');
		Configuration::updateValue('LOGINBYMOBILE_GENERIC_PWD_TXT', 'Password Parameter Name');
		Configuration::updateValue('LOGINBYMOBILE_GENERIC_FROM_TXT', 'Sender Parameter Name ');
		Configuration::updateValue('LOGINBYMOBILE_GENERIC_TO_TXT', 'To Parameter Name');
		Configuration::updateValue('LOGINBYMOBILE_GENERIC_MSG_TXT', 'Message Parameter Name');
		Configuration::updateValue('LOGINBYMOBILE_GENERIC_UNI_TXT', 'Unicode Parameter Name');

        /* Provider Change - End */

        Configuration::updateValue('LOGINBYMOBILE_NNEWORDERA_ENABLE', 0);
		Configuration::updateValue('LBM_SELLER_NEWORDER_ENABLE', 0);
		Configuration::updateValue('LBM_SELLER_LOWSTOCK_ENABLE', 0);		
		Configuration::updateValue('LBM_FIRSTORDER_SMS_ENABLE', 0);
		Configuration::updateValue('LBM_FIRSTORDER_CRON_ENABLE', 0);

        Configuration::updateValue('LOGINBYMOBILE_NNEWORDER_ENABLE', 0);
        Configuration::updateValue('LOGINBYMOBILE_NTRACKING_ENABLE', 0);
        Configuration::updateValue('LOGINBYMOBILE_NDELIVERED_ENABLE', 0);
        Configuration::updateValue('LOGINBYMOBILE_NSHIPPED_ENABLE', 0);
        Configuration::updateValue('LOGINBYMOBILE_NCANCELED_ENABLE', 0);
        Configuration::updateValue('LOGINBYMOBILE_ADMINPHONE', '1111000011');
        Configuration::updateValue('LOGINBYMOBILE_ADMINCOUNTRY', 110);
        Configuration::updateValue('LOGINBYMOBILE_STATUS_CANCELLED', 6);
        Configuration::updateValue('LOGINBYMOBILE_STATUS_DELIVERED', 5);
		Configuration::updateValue('LOGINBYMOBILE_STATUS_ORDER', 14);
		Configuration::updateValue('LOGINBYMOBILE_STATUS_PC', 2);		
        Configuration::updateValue('LOGINBYMOBILE_STATUS_SHIPPED', 4);
        Configuration::updateValue('LOGINBYMOBILE_DDB_ENABLE', '0');
        Configuration::updateValue('LOGINBYMOBILE_EMAIL_REQUIRED', '1');
        Configuration::updateValue('LOGINBYMOBILE_SMS_MAX_COUNT', '10');
        Configuration::updateValue('LOGINBYMOBILE_WELCOMESMS_ENABLE', 0);
        Configuration::updateValue('LOGINBYMOBILE_ADD_PREFIX', 0);
        Configuration::updateValue('LOGINBYMOBILE_CODE_LENGTH', '4');
        Configuration::updateValue('LOGINBYMOBILE_MAIL_DOMAIN', 'demo.com');
        Configuration::updateValue('LOGINBYMOBILE_DISP_MAIL_AC_PAGE', 1);
        Configuration::updateValue('LOGINBYMOBILE_OTP_TIMEINTERVAL', 20);
		
		$this->setGlobalValueForLangConfiguration('LOGINBYMOBILE_WELCOMESMS_TEXT', 'Dear {customername} , welcome to our {shop}.');
		$this->setGlobalValueForLangConfiguration('LOGINBYMOBILE_PASSWORD_TEXT', 'Dear {customername}, your {shop} password is {pwd}.');
		$this->setGlobalValueForLangConfiguration('LOGINBYMOBILE_TWOWAYFACTOR_TEXT', 'Dear Customer, {shop} secure login key is {OTP}.');
		$this->setGlobalValueForLangConfiguration('LOGINBYMOBILE_SMS_TEXT', 'Your OTP is {OTP} ');
		$this->setGlobalValueForLangConfiguration('LOGINBYMOBILE_NNEWORDERA', 'Dear {firstname} A customer has successfully placed Order {order_name} with {shopname}');
		
		$this->setGlobalValueForLangConfiguration('LBM_SELLER_NEWORDER_TEXT', 'Dear {firstname} A customer has successfully placed Order {order_name} with {shopname}');
		$this->setGlobalValueForLangConfiguration('LBM_SELLER_LOWSTOCK_TEXT', 'Dear {firstname} The Product is low on stock');		
		
		$this->setGlobalValueForLangConfiguration('LOGINBYMOBILE_NNEWORDER', 'Dear {firstname} you have successfully placed Order {order_name} with {shopname}');
		
		$this->setGlobalValueForLangConfiguration('LOGINBYMOBILE_NTRACKING', 'Dear {firstname} Track your order {order_id} with {shopname}. {carrier} tracking number is {tracking_number}');
		$this->setGlobalValueForLangConfiguration('LOGINBYMOBILE_NDELIVERED', 'Dear {firstname} your order {order_id} with {shopname} has been Delivered');
		
		$this->setGlobalValueForLangConfiguration('LOGINBYMOBILE_NSHIPPED', 'Dear {firstname}  your order {order_id} with {shopname} has been Shipped');
		$this->setGlobalValueForLangConfiguration('LOGINBYMOBILE_NCANCELED', 'Dear {firstname} your order {order_id} with {shopname} has been cancelled');		
        Configuration::updateValue('LOGINBYMOBILE_SMS_UNICODE', 1);
        Configuration::updateValue('LOGINBYMOBILE_ENABLE_LOGIN_FEATURE', 1);
		Configuration::updateValue('LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN', 1);
        Configuration::updateValue('LOGINBYMOBILE_MOBILE_REG', 1);
		Configuration::updateValue('LOGINBYMOBILE_ACCEPTZERO', 1);
        Configuration::updateValue('LOGINBYMOBILE_TWOWAYFACTOR', 1);
        Configuration::updateValue('LOGINBYMOBILE_ATTEMPTS', 10);
        Configuration::updateValue('LOGINBYMOBILE_SESSION_TIME', 10);
        include(dirname(__FILE__).'/sql/install.php');

        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayCustomerLoginFormAfter')
            && $this->registerHook('additionalCustomerFormFields')
            && $this->registerHook('validateCustomerFormFields')
            && $this->registerHook('displayCustomerIdentityForm')
			&& $this->registerHook('actionFrontControllerSetMedia')
			&& $this->registerHook('actionDispatcherBefore')
            && $this->registerHook('backOfficeHeader')
            && $this->registerHook('actionAdminOrdersTrackingNumberUpdate')
            && $this->registerHook('actionValidateOrder')
            && $this->registerHook('actionOrderStatusUpdate')
            && $this->registerHook('displayCustomerAccountFormTop')
            && $this->registerHook('actionCustomerAccountAdd')
            && $this->registerHook('actionCustomerAccountUpdate')
            && $this->registerHook('displayHeader')
            && $this->registerHook('actionObjectCustomerDeleteAfter')
            && $this->registerHook('myAccountBlock')
            && $this->registerHook('displayMyAccountBlockfooter')
            && $this->registerHook('customerAccount')
            && $this->installTabs();
    }
//            && $this->registerHook('displayOverrideTemplate')
//            && $this->registerHook('actionBeforeAuthentication')
//            && $this->registerHook('actionBeforeSubmitAccount')
//            && $this->registerHook('displayCustomerAccountForm')

    public function installTabs()
    {
        $parentTab = new Tab();

        foreach (Language::getLanguages() as $language) {
            $parentTab->name[$language['id_lang']] = 'Login By Mobile';
        }

        $parentTab->class_name = $this->parentTabClass;
        $parentTab->module = $this->name;
        $parentTab->id_parent = (int)Tab::getIdFromClassName('CONFIGURE');
		$parentTab->icon = 'phone_iphone';
        if (!$parentTab->save()) {
            return false;
        } else {
            $idTab = $parentTab->id;
            //$idEn = Language::getIdByIso('en');
            foreach ($this->tabsArray as $tabKey => $name) {
                $childTab = new Tab();
                foreach (Language::getLanguages() as $language) {
                    $childTab->name[$language['id_lang']] = $name;
                }
                $childTab->class_name = $tabKey;
                $childTab->module = $this->name;
                $childTab->id_parent = $idTab;
                if (!$childTab->save()) {
                    return false;
                }
            }
            foreach ($this->tabsArray_hidden as $tabKey => $name) {
                $childTab = new Tab();
                foreach (Language::getLanguages() as $language) {
                    $childTab->name[$language['id_lang']] = $name;
                }
                $childTab->class_name = $tabKey;
                $childTab->module = $this->name;
                $childTab->id_parent = -1;
                if (!$childTab->save()) {
                    return false;
                }
            }
        }
        return true;
    }

    public function uninstall()
    {
        include_once(dirname(__FILE__).'/sql/uninstall.php');
        $enable_ddb = Configuration::get('LOGINBYMOBILE_DDB_ENABLE');
        if ($enable_ddb) {
            Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'mobilenumlist`');
        }
        Configuration::deleteByName('LOGINBYMOBILE');
        $this->uninstallTabs();
        return parent::uninstall();
    }

    public function uninstallTabs()
    {
        foreach ($this->tabsArray as $tabKey => $name) {
            $idTab = Tab::getIdFromClassName($tabKey);
            if (!empty($idTab)) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }

        foreach ($this->tabsArray_hidden as $tabKey => $name) {
            $idTab = Tab::getIdFromClassName($tabKey);
            if (!empty($idTab)) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }

        $idTab = Tab::getIdFromClassName($this->parentTabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();
        }
        return true;
    }

/********************* Hooks - Start ***********************/
	public function hookCustomerAccount($params)
	{
		$link_module_lbm = Context::getContext()->link->getPageLink('identity', true);
		$this->smarty->assign(array('link_module_lbm' => $link_module_lbm));
		return $this->display(__FILE__, 'views/templates/hook/my_account_block.tpl');
	}

	public function hookMyAccountBlock($params)
	{
		if ($this->context->customer->isLogged()) {
			$link_module_lbm = Context::getContext()->link->getPageLink('identity', true);
			$this->smarty->assign(array('link_module_lbm' => $link_module_lbm));
			return $this->display(__FILE__, 'views/templates/hook/my_account_link.tpl');
		}
	}

	public function hookdisplayMyAccountBlockfooter($params)
	{
		return $this->hookMyAccountBlock($params);
	}

	public function hookMyAccountBlockfooter($params)
	{
		return $this->hookMyAccountBlock($params);
	}

	public function hookAdditionalCustomerFormFields($params) {

		//var_dump($this->getCountriesForRegField());
		//die();
		$additional_fields = array();
		$additional_fields['lbm_ca_id_country'] = (new FormField())
				->setName('lbm_ca_id_country')
				->setType('countrySelect')
				->setLabel($this->l('Country'))
				->setRequired(true)
				->setValue($this->default_country)
				->setAvailableValues($this->getCountriesForRegField());
		$additional_fields['lbm_ca_mobile_number'] = (new FormField())
				->setName('lbm_ca_mobile_number')
				->setType('text')
				->setLabel($this->l('Mobile Number'))
				->setRequired(true)
				->setMaxLength(10)
				->addAvailableValue('placeholder', '1234567890')
				->addAvailableValue('comment', $this->l('Do not enter country phone prefix. Enter mobile number directly'))
				->addConstraint('isPhoneNumber');
		$additional_fields['lbm_ca_otp'] = (new FormField())
				->setName('lbm_ca_otp')
				->setType('number')
				->setRequired(true)
				->setLabel($this->l('OTP'))
				->setMaxLength(6)
				->addAvailableValue('comment', $this->l('Enter the Key number received through Message in your Mobile Number'));
		/*$additional_fields['send_otp_ca_form'] = (new FormField())
				->setName('send_otp_ca_form')
				->setType('button')
				->setRequired(true)
				->setValue($this->l('Send OTP'));*/


		$pageName = $this->context->controller->php_self;
		if ($pageName === 'identity') {
			// Personal Info edit
			$id_customer = $this->context->customer->id;
			$result = Mobilenumlist::getMobileNum($id_customer);
			//print_r($result);
			//die();
			if (isset($result['id_country']) && isset($result['otp_mobile_num'])) {
				$additional_fields['lbm_ca_id_country']->setValue($result['id_country']);
				$additional_fields['lbm_ca_mobile_number']->setValue($result['otp_mobile_num']);
			}
		} else if ($pageName === 'authentication') {
			// Customer Registration Flow
		} else if ($pageName === 'order') {
			// Order - Customer Registration Flow
		} 
		return $additional_fields;
	}
	public function updateTempEmail()
	{
		if (!Configuration::get('LOGINBYMOBILE_EMAIL_REQUIRED')) {
			$email = Tools::getValue('email');
			if (empty($email)) {
				$lbm_ca_id_country = (int)Tools::getValue('lbm_ca_id_country');
				$lbm_ca_mobile_number = Tools::getValue('lbm_ca_mobile_number');
				if (!empty($lbm_ca_id_country) && !empty($lbm_ca_mobile_number)) {
					$domain_name = Configuration::get('LOGINBYMOBILE_MAIL_DOMAIN');
					$countries = new Country($lbm_ca_id_country);
					$prefix_id = $countries->call_prefix;
					$temp_email_id = $prefix_id."_".$lbm_ca_mobile_number."@".$domain_name;
					$_POST['email'] = $temp_email_id;
				}
			}
		}		
	}
	
	public function hookActionDispatcherBefore($params)
	{
		$controller = Dispatcher::getInstance()->getController();
		switch ($controller) {
			case 'authentication':
				if (Tools::getValue('create_account') && Tools::getValue('submitCreate')) {
					$this->updateTempEmail();
				}
				break;
			case 'identity':
				if (Tools::getValue('submitCreate')) {
					$this->updateTempEmail();
				}
				break;
			case 'order':
				if (!$this->context->customer->isLogged()) {
					if (Tools::getValue('submitCreate')) {
						$this->updateTempEmail();
						// Guest Accounts
						$password = Tools::getValue('password');
						$session_key = Tools::getValue('session_key_reg');
						if (empty($password) && !empty($session_key)) {
							$_POST['password'] = $session_key;
						}						
					}						
				}
				break;
		}

	}
	
	public function hookValidateCustomerFormFields($params)
	{
		//print_r($_POST);
		//die();
		$additional_fields = $params['fields'];
		$input_mobile_number = '';
		$input_otp = 1111;
		$input_id_country = 0;
		$lbm_result = array('hasError' => 0, 'errors' => array());
		foreach ($additional_fields as &$additional_field) {
			$additional_field_name = $additional_field->getName();
			switch ($additional_field_name) {
				case 'lbm_ca_id_country':
					$input_id_country = (int)$additional_field->getValue();
				break;
				case 'lbm_ca_mobile_number':
					$input_mobile_number = $additional_field->getValue();
				break;
				case 'lbm_ca_otp':
					$input_otp = $additional_field->getValue();
				break;
			}
		}

		$pageName = $this->context->controller->php_self;
		
		//print_r($pageName);
		//die();
		if ($pageName === 'identity') {
			// Personal Info edit
			$lbm_result = $this->processIdentityUpdate($input_id_country, $input_otp, $input_mobile_number);

		} else if ($pageName === 'authentication' || $pageName === 'order') {
			// Customer Registration Flow
			$email = Tools::getValue('email');			
			$lbm_result = $this->processAccountRegistration($email, $input_mobile_number, $input_id_country, $input_otp);
		}

		if ($lbm_result['hasError']) {
			$errors = $lbm_result['errors'];
			foreach ($additional_fields as &$additional_field) {
				$additional_field_name = $additional_field->getName();
				if (isset($errors[$additional_field_name])) {
					$additional_field->addError($errors[$additional_field_name]);
				}
				$this->errors[$additional_field_name] = $additional_field->getValue();
			}

		} else {
			// If a custom field is required initially. It will always be validated and thrown error even though the module specific code approves the field blank value
			//if ($pageName === 'identity') {
				if (empty($input_otp)) {
					$input_otp = 1111;					
				}
				foreach ($additional_fields as &$additional_field) {
					$additional_field_name = $additional_field->getName();
					if ($additional_field_name === 'lbm_ca_otp') {
						$additional_field->setValue($input_otp);
					}
				}
			//}
		}
		//var_dump($additional_fields);
		//die();
		return $additional_fields;
		//var_dump($params);
		//die();
	}
	public function hookBackOfficeHeader()
	{
		$this->context->controller->addCSS($this->_path . 'views/css/boicon.css');
	}

	public function hookDisplayHeader()
	{
		$allowedControllers = array('authentication', 'identity', 'order', 'order-opc', 'supercheckout', 'password', 'checkout');
		$controller = Dispatcher::getInstance()->getController();
		//print_r($controller);
		//die();
		if (in_array($controller, $allowedControllers)) {
			$mobile_size_max = Configuration::get('LOGINBYMOBILE_MBLENGTH');
			$mobile_size_min = Configuration::get('LOGINBYMOBILE_MBLENGTH_MIN');
			$mobile_size_max_list = unserialize($mobile_size_max);
			$mobile_size_min_list = unserialize($mobile_size_min);
			$LOGINBYMOBILE_OTP = Configuration::get('LOGINBYMOBILE_OTP');
			$LOGINBYMOBILE_OTP_list = unserialize($LOGINBYMOBILE_OTP);

			$id_country = Tools::getValue('lbm_id_country');
			if (empty($id_country)) {
				$id_country = $this->default_country;
			}
			$this->assignCountries($id_country);

			if (!isset($LOGINBYMOBILE_OTP_list[$id_country]) || empty($LOGINBYMOBILE_OTP_list[$id_country])) {
				$LOGINBYMOBILE_OTP_list[$id_country] = 0;
			}

			$LOGINBYMOBILE_OTP_TIMEINTERVAL = (int)Configuration::get('LOGINBYMOBILE_OTP_TIMEINTERVAL');

			if (empty($LOGINBYMOBILE_OTP_TIMEINTERVAL)) {
				$LOGINBYMOBILE_OTP_TIMEINTERVAL = 120;
			}

			$back = Tools::getValue('back');

			if (!empty($back)) {
				$key = Tools::safeOutput(Tools::getValue('key'));
				if (!empty($key)) {
					$back .= (strpos($back, '?') !== false ? '&' : '?').'key='.$key;
				}
				if ($back == Tools::secureReferrer(Tools::getValue('back'))) {
					$this->context->smarty->assign('back', html_entity_decode($back));
				} else {
					$this->context->smarty->assign('back', Tools::safeOutput($back));
				}
			} else {
				if ($controller === 'order-opc' || $controller === 'orderopc' || $controller === 'supercheckout') {
					$back = 'order-opc';
				} else if ($controller === 'checkout') {
					//$back = 'checkout';
					$back = Context::getContext()->link->getModuleLink('bestkit_opc', 'checkout');
				} else {
					$back = 'order';
				}
				$this->context->smarty->assign('back', Tools::safeOutput($back));
			}
			$lbm_back = Tools::getValue('lbm_back');
			if (empty($lbm_back)) {
				$lbm_back = $back;
			}

			$this->context->smarty->assign(array(
				'mobilenum_length' => $mobile_size_max_list,
				'mobile_size_min_list' => $mobile_size_min_list,
				//'LOGINBYMOBILE_TXN_list' => $LOGINBYMOBILE_TXN_list,
				'LOGINBYMOBILE_OTP_list' => $LOGINBYMOBILE_OTP_list,
				'LOGINBYMOBILE_OTP_TIMEINTERVAL' => $LOGINBYMOBILE_OTP_TIMEINTERVAL,
				'LOGINBYMOBILE_ENABLE_LOGIN_FEATURE' => Configuration::get('LOGINBYMOBILE_ENABLE_LOGIN_FEATURE'),
				'LOGINBYMOBILE_MOBILE_REG' => Configuration::get('LOGINBYMOBILE_MOBILE_REG'),
				'LOGINBYMOBILE_EMAIL_REQUIRED' => Configuration::get('LOGINBYMOBILE_EMAIL_REQUIRED'),
				'LOGINBYMOBILE_TWOWAYFACTOR' => Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR'),
				'LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN' => Configuration::get('LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN'),
				'LOGINBYMOBILE_DISP_MAIL_AC_PAGE' => Configuration::get('LOGINBYMOBILE_DISP_MAIL_AC_PAGE'),
				'LOGINBYMOBILE_MAIL_DOMAIN' => Configuration::get('LOGINBYMOBILE_MAIL_DOMAIN'),
				'LOGINBYMOBILE_ACCEPTZERO' => Configuration::get('LOGINBYMOBILE_ACCEPTZERO'),
				'lbmajax' => 0,
				'twowayfactoraction' => 'login',
				'lbmLoadingImgsBaseDir' => __PS_BASE_URI__,
				'lbm_back' => $lbm_back,
				'orig_back' => $back,
			));
			return $this->display(__FILE__, 'views/templates/hook/lbm_js_variables.tpl');
		}
	}

	public function addIdentityPageAssets() {
		$original_mobile_number = '';
		$original_id_country = 0;
		$this->context->controller->registerStylesheet(
			'module-lbm-auth-css',
			'modules/'.$this->name.'/views/css/account_reg_form_style.css',
			[
			  'media' => 'all',
			  'priority' => 202,
			  'inline' => false,
			]
		);
		$id_customer = $this->context->customer->id;
		$customer_lbm_dbdata = Mobilenumlist::getMobileNum($id_customer);
		if (isset($customer_lbm_dbdata['otp_mobile_num'])) {
			if (!empty($customer_lbm_dbdata['otp_mobile_num'])) {
				$original_mobile_number = $customer_lbm_dbdata['otp_mobile_num'];
			}
		}
		if (isset($customer_lbm_dbdata['id_country'])) {
			if (!empty($customer_lbm_dbdata['id_country'])) {
				$original_id_country = $customer_lbm_dbdata['id_country'];
			}
		}
		$lbm_ca_id_country = Tools::getValue('lbm_ca_id_country', 0);
		if (empty($lbm_ca_id_country)) {
			if (empty($original_id_country)) {
				$lbm_ca_id_country = $this->context->country->id;
			} else {
				$lbm_ca_id_country = $original_id_country;
			}
		}

		$this->assignCountries($lbm_ca_id_country);

		$lbm_ca_mobile_number = Tools::getValue('lbm_ca_mobile_number');

		if (empty($lbm_ca_mobile_number)) {
			$lbm_ca_mobile_number = $original_mobile_number;
		}
		$lbm_ca_otp = Tools::getValue('lbm_ca_otp');
		if (empty($lbm_ca_otp)) {
			$lbm_ca_otp = '';
		}
		$this->context->smarty->assign('lbm_ca_mobile_number', $lbm_ca_mobile_number);
		
		$session_key_reg = Tools::getValue('session_key_reg');
		if (empty($session_key_reg)) {
			$session_key_reg = '';
		}
		$this->context->smarty->assign('session_key_reg', $session_key_reg);

		Media::addJsDef(array(
			'account_reg_form_mobile' => $this->display(__FILE__, 'views/templates/hook/account_reg_form_mobile.tpl'),
			'selected_country' => $lbm_ca_id_country,
			'original_mobile_number' => $original_mobile_number,
			'original_id_country' => $original_id_country,
		));

		$this->context->controller->registerJavascript(
			'module-lbm-reg-form-js',
			'modules/'.$this->name.'/views/js/account_reg_form.js',
			[
			  'priority' => 202,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);

		$this->context->controller->registerJavascript(
			'module-lbm-identity-js',
			'modules/'.$this->name.'/views/js/identity_form.js',
			[
			  'priority' => 204,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);
	}
	public function addLoginPageAssets() {
		$this->context->controller->registerStylesheet(
			'module-lbm-auth-css',
			'modules/'.$this->name.'/views/css/login_style.css',
			[
			  'media' => 'all',
			  'priority' => 200,
			  'inline' => false,
			]
		);

		$this->context->controller->registerJavascript(
			'module-lbm-auth-js',
			'modules/'.$this->name.'/views/js/login_form.js',
			[
			  'priority' => 200,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);

		if ((int)Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR')) {
			$this->context->controller->registerJavascript(
				'module-lbm-auth-twf-js',
				'modules/'.$this->name.'/views/js/twf_lf.js',
				[
				  'priority' => 200,
				  'attribute' => 'async',
				  'position' => 'bottom',
				  'inline' => false,
				]
			);

			$this->context->controller->registerStylesheet(
				'module-lbm-twf-css',
				'modules/'.$this->name.'/views/css/twf.css',
				[
				  'media' => 'all',
				  'priority' => 203,
				  'inline' => false,
				]
			);
		}
	}
	public function addRegistrationPageAssets() {
		$this->context->controller->registerStylesheet(
			'module-lbm-reg-form-css',
			'modules/'.$this->name.'/views/css/account_reg_form_style.css',
			[
			  'media' => 'all',
			  'priority' => 202,
			  'inline' => false,
			]
		);

		$lbm_ca_id_country = Tools::getValue('lbm_ca_id_country');
		if (empty($lbm_ca_id_country)) {
			$lbm_ca_id_country = $this->context->country->id;
		}
		$this->assignCountries($lbm_ca_id_country);

		$lbm_ca_mobile_number = Tools::getValue('lbm_ca_mobile_number');
		if (empty($lbm_ca_mobile_number)) {
			$lbm_ca_mobile_number = '';
		}

		$lbm_ca_otp = Tools::getValue('lbm_ca_otp');
		if (empty($lbm_ca_otp)) {
			$lbm_ca_otp = '';
		}

		$this->context->smarty->assign('lbm_ca_mobile_number', $lbm_ca_mobile_number);
		
		$session_key_reg = Tools::getValue('session_key_reg');
		if (empty($session_key_reg)) {
			$session_key_reg = '';
		}
		$this->context->smarty->assign('session_key_reg', $session_key_reg);		
		Media::addJsDef(array(
			'account_reg_form_mobile' => $this->display(__FILE__, 'views/templates/hook/account_reg_form_mobile.tpl'),
			'selected_country' => $lbm_ca_id_country,
		));

		$this->context->controller->registerJavascript(
			'module-lbm-reg-form-js',
			'modules/'.$this->name.'/views/js/account_reg_form.js',
			[
			  'priority' => 202,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);
	}
	
	public function addBkLoginPageAssets() {
		Media::addJsDef(array(
			'lbm_mobile_text' => $this->l('(or) Mobile Number', 'BkcheckoutCore'),
			'time_interval_otp' => 30,
			'timeIntervalMsg' => $this->l('You shall resend OTP only after 120 seconds', 'BkcheckoutCore'),
		));			
		$this->context->controller->registerStylesheet(
			'module-lbm-auth-css',
			'modules/'.$this->name.'/views/css/login_style.css',
			[
			  'media' => 'all',
			  'priority' => 200,
			  'inline' => false,
			]
		);

		$this->context->controller->registerJavascript(
			'module-lbm-sc-auth-js',
			'modules/'.$this->name.'/views/js/bk/login_form.js',
			[
			  'priority' => 200,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);

		if ((int)Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR')) {
			$this->context->controller->registerJavascript(
				'module-lbm-sc-auth-twf-js',
				'modules/'.$this->name.'/views/js/bk/twf_lf.js',
				[
				  'priority' => 200,
				  'attribute' => 'async',
				  'position' => 'bottom',
				  'inline' => false,
				]
			);

			$this->context->controller->registerStylesheet(
				'module-lbm-twf-css',
				'modules/'.$this->name.'/views/css/twf.css',
				[
				  'media' => 'all',
				  'priority' => 203,
				  'inline' => false,
				]
			);
		}

	}

	public function addBkRegistrationPageAssets() {
		$this->context->controller->registerStylesheet(
			'module-lbm-reg-form-css',
			'modules/'.$this->name.'/views/css/bk/account_reg_form_style.css',
			[
			  'media' => 'all',
			  'priority' => 202,
			  'inline' => false,
			]
		);

		$lbm_ca_id_country = Tools::getValue('lbm_ca_id_country');
		if (empty($lbm_ca_id_country)) {
			$lbm_ca_id_country = $this->context->country->id;
		}
		$this->assignCountries($lbm_ca_id_country);

		$lbm_ca_mobile_number = Tools::getValue('lbm_ca_mobile_number');
		if (empty($lbm_ca_mobile_number)) {
			$lbm_ca_mobile_number = '';
		}

		$lbm_ca_otp = Tools::getValue('lbm_ca_otp');
		if (empty($lbm_ca_otp)) {
			$lbm_ca_otp = '';
		}

		$this->context->smarty->assign('lbm_ca_mobile_number', $lbm_ca_mobile_number);
		
		$session_key_reg = Tools::getValue('session_key_reg');
		if (empty($session_key_reg)) {
			$session_key_reg = '';
		}
		$this->context->smarty->assign('session_key_reg', $session_key_reg);		
		Media::addJsDef(array(
			'account_reg_form_mobile' => $this->display(__FILE__, 'views/templates/hook/bk/account_reg_form_complete.tpl'),
			'selected_country' => $lbm_ca_id_country,
		));

		$this->context->controller->registerJavascript(
			'module-lbm-reg-form-js',
			'modules/'.$this->name.'/views/js/bk/account_reg_form.js',
			[
			  'priority' => 202,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);
	}
	
	public function addBkCheckoutPageAssets() {
		$co_lf_country_mobile = $this->displayBkCheckoutLoginForm();
		Media::addJsDef(array(
			'co_lf_country_mobile' => $co_lf_country_mobile,
		));
		$this->context->controller->registerStylesheet(
			'module-lbm-co-css',
			'modules/'.$this->name.'/views/css/checkout_style.css',
			[
			  'media' => 'all',
			  'priority' => 198,
			  'inline' => false,
			]
		);
		$this->context->controller->registerJavascript(
			'module-lbm-co-js',
			'modules/'.$this->name.'/views/js/bk/checkout_form.js',
			[
			  'priority' => 198,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);
	}
	
	public function addCheckoutPageAssets() {
		$co_lf_country_mobile = $this->displayCheckoutLoginForm();
		Media::addJsDef(array(
			'co_lf_country_mobile' => $co_lf_country_mobile,
		));
		$this->context->controller->registerStylesheet(
			'module-lbm-co-css',
			'modules/'.$this->name.'/views/css/checkout_style.css',
			[
			  'media' => 'all',
			  'priority' => 198,
			  'inline' => false,
			]
		);
		$this->context->controller->registerJavascript(
			'module-lbm-co-js',
			'modules/'.$this->name.'/views/js/checkout_form.js',
			[
			  'priority' => 198,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);
	}
	
	public function displayBkCheckoutLoginForm()
	{
		$out_html = '';
		$id_country = Tools::getValue('lbm_id_country');
		if (empty($id_country)) {
			$id_country = $this->default_country;
		}
		$LOGINBYMOBILE_TWOWAYFACTOR = (int)Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR');
		$this->assignCountries($id_country);
		$this->context->smarty->assign('lbmLoadingImgsBaseDir', __PS_BASE_URI__);
		$this->context->smarty->assign('back', 'index');
		$this->context->smarty->assign('LOGINBYMOBILE_TWOWAYFACTOR', $LOGINBYMOBILE_TWOWAYFACTOR);
		
		$link_password_recovery = Context::getContext()->link->getPageLink('password', true);
		$this->smarty->assign(array('link_password_recovery' => $link_password_recovery));


		$out_html = $this->display(__FILE__, 'views/templates/hook/bk/checkout_login_form.tpl');
		if ($LOGINBYMOBILE_TWOWAYFACTOR) {
			$out_html .= $this->display(__FILE__, 'views/templates/hook/bk/twf_form.tpl');
		}
		//print_r($out_html);
		//die();
		return $out_html;
	}	

	/**
	* Method Name: displayCheckoutLoginForm
	* This method is a replica of hookDisplayCustomerLoginFormAfter (which is present just below the login form (Email / Password / Forgot Password))
	* In order to give a different look and feel for Checkout Login Form, this logic has been kept separate inspite of duplication and for furture enhancements
	*/
	public function displayCheckoutLoginForm()
	{
		$out_html = '';
		$id_country = Tools::getValue('lbm_id_country');
		if (empty($id_country)) {
			$id_country = $this->default_country;
		}
		$LOGINBYMOBILE_TWOWAYFACTOR = (int)Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR');
		$this->assignCountries($id_country);
		$this->context->smarty->assign('lbmLoadingImgsBaseDir', __PS_BASE_URI__);
		$this->context->smarty->assign('back', 'order');
		$this->context->smarty->assign('LOGINBYMOBILE_TWOWAYFACTOR', $LOGINBYMOBILE_TWOWAYFACTOR);

		$link_password_recovery = Context::getContext()->link->getPageLink('password', true);
		$this->smarty->assign(array('link_password_recovery' => $link_password_recovery));

		$out_html = $this->display(__FILE__, 'views/templates/hook/checkout_login_form.tpl');
		if ($LOGINBYMOBILE_TWOWAYFACTOR) {
			$out_html .= $this->display(__FILE__, 'views/templates/hook/twf_form.tpl');
		}
		//print_r($out_html);
		//die();
		return $out_html;
	}

	public function addForgotPasswordPageAssets() {
		$lbm_fp_id_country = Tools::getValue('lbm_fp_id_country', 0);
		$session_key = Tools::getValue('session_key', 0);
		if (empty($lbm_fp_id_country)) {
			$lbm_fp_id_country = $this->context->country->id;
		}
		$this->assignCountries($lbm_fp_id_country);

		$lbm_fp_mobile_number = Tools::getValue('lbm_fp_mobile_number');
		if (empty($lbm_fp_mobile_number)) {
			$lbm_fp_mobile_number = '';
		}

		if (empty($session_key)) {
			$session_key = '';
		}


		$this->context->smarty->assign('lbm_fp_mobile_number', $lbm_fp_mobile_number);
		$this->context->smarty->assign('session_key', $session_key);
		Media::addJsDef(array(
			'fp_country_mobile' => $this->display(__FILE__, 'views/templates/hook/forgot_password_form.tpl'),
			'selected_country' => $lbm_fp_id_country,
		));

		$this->context->controller->registerStylesheet(
			'module-lbm-fp-css',
			'modules/'.$this->name.'/views/css/fp_style.css',
			[
			  'media' => 'all',
			  'priority' => 200,
			  'inline' => false,
			]
		);

		$this->context->controller->registerJavascript(
			'module-lbm-fp-js',
			'modules/'.$this->name.'/views/js/fp_form.js',
			[
			  'priority' => 200,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);
	}

	public function hookActionFrontControllerSetMedia($params)
	{
		$allowedControllers = array('authentication', 'identity', 'orderopc', 'order-opc', 'supercheckout', 'password', 'checkout');
		$controller = Dispatcher::getInstance()->getController();
		//print_r($controller);
		//print_r("==================");
		//die();
		switch ($controller) {
			case 'authentication':
				if (Tools::getValue('create_account') || Tools::getValue('submitCreate')) {
					// Account Registration Page
					$this->addRegistrationPageAssets();
				} else {
					// Login Page
					$this->addLoginPageAssets();
				}
				break;
			case 'identity':
				$this->addIdentityPageAssets();
				break;
			case 'order':
				if ($this->context->customer->isLogged()) {
					// just Identitiy
					$this->addIdentityPageAssets();
				} else {
					//Login + Account Reg
					$this->addCheckoutPageAssets();
					$this->addLoginPageAssets();
					$this->addRegistrationPageAssets();
				}
				break;
			case 'checkout':
				if ($this->context->customer->isLogged()) {
					// just Identitiy
					//$this->addIdentityPageAssets();
				} else {
					//Login + Account Reg
					$this->addBkCheckoutPageAssets();
					$this->addBkLoginPageAssets();
					$this->addBkRegistrationPageAssets();
				}
				break;				
			case 'password':
				// As of 1.7.4.2 HookHeader is not included as part of ForgotPassword page. This might be a defect. Hence explicit HookHeader call is added.  (TO DO :::) Remove below call when this defect is addressed in future PSv1.7 versions.
				$this->hookDisplayHeader();
				$this->addForgotPasswordPageAssets();
				break;
			case 'supercheckout':
				if ($this->context->customer->isLogged()) {
					// just Identitiy
					//$this->addIdentityPageAssets();
				} else {
					//Login + Account Reg
					$this->addSCCheckoutPageAssets();
					$this->addSCLoginPageAssets();
					$this->addSCRegistrationPageAssets();
				}
				break;			
				
		}
	}

	public function addSCLoginPageAssets() {
		Media::addJsDef(array(
			'lbm_mobile_text' => $this->l('(or) Mobile Number', 'SupercheckoutCore'),
			'time_interval_otp' => 30,
			'timeIntervalMsg' => $this->l('You shall resend OTP only after 120 seconds', 'SupercheckoutCore'),
		));			
		/*$this->context->controller->registerStylesheet(
			'module-lbm-auth-css',
			'modules/'.$this->name.'/views/css/login_style.css',
			[
			  'media' => 'all',
			  'priority' => 200,
			  'inline' => false,
			]
		);*/

		$this->context->controller->registerJavascript(
			'module-lbm-sc-auth-js',
			'modules/'.$this->name.'/views/js/sc/login_form.js',
			[
			  'priority' => 200,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);

		if ((int)Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR')) {
			$this->context->controller->registerJavascript(
				'module-lbm-sc-auth-twf-js',
				'modules/'.$this->name.'/views/js/sc/twf_lf.js',
				[
				  'priority' => 200,
				  'attribute' => 'async',
				  'position' => 'bottom',
				  'inline' => false,
				]
			);

			$this->context->controller->registerStylesheet(
				'module-lbm-twf-css',
				'modules/'.$this->name.'/views/css/twf.css',
				[
				  'media' => 'all',
				  'priority' => 203,
				  'inline' => false,
				]
			);
		}

	}

	public function addSCRegistrationPageAssets() {
		$this->context->controller->registerStylesheet(
			'module-lbm-reg-form-css',
			'modules/'.$this->name.'/views/css/account_reg_form_style.css',
			[
			  'media' => 'all',
			  'priority' => 202,
			  'inline' => false,
			]
		);

		$lbm_ca_id_country = Tools::getValue('lbm_ca_id_country');
		if (empty($lbm_ca_id_country)) {
			$lbm_ca_id_country = $this->context->country->id;
		}
		$this->assignCountries($lbm_ca_id_country);

		$lbm_ca_mobile_number = Tools::getValue('lbm_ca_mobile_number');
		if (empty($lbm_ca_mobile_number)) {
			$lbm_ca_mobile_number = '';
		}

		$lbm_ca_otp = Tools::getValue('lbm_ca_otp');
		if (empty($lbm_ca_otp)) {
			$lbm_ca_otp = '';
		}

		$this->context->smarty->assign('lbm_ca_mobile_number', $lbm_ca_mobile_number);
		
		$session_key_reg = Tools::getValue('session_key_reg');
		if (empty($session_key_reg)) {
			$session_key_reg = '';
		}
		$this->context->smarty->assign('session_key_reg', $session_key_reg);		
		Media::addJsDef(array(
			'account_reg_form_mobile' => $this->display(__FILE__, 'views/templates/hook/sc/account_reg_form_complete.tpl'),
			'selected_country' => $lbm_ca_id_country,
		));

		$this->context->controller->registerJavascript(
			'module-lbm-reg-form-js',
			'modules/'.$this->name.'/views/js/sc/account_reg_form.js',
			[
			  'priority' => 202,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);
	}
	
	public function addSCCheckoutPageAssets() {
		$co_lf_country_mobile = $this->displaySCCheckoutLoginForm();
		Media::addJsDef(array(
			'co_lf_country_mobile' => $co_lf_country_mobile,
		));
		$this->context->controller->registerStylesheet(
			'module-lbm-co-css',
			'modules/'.$this->name.'/views/css/checkout_style.css',
			[
			  'media' => 'all',
			  'priority' => 198,
			  'inline' => false,
			]
		);
		$this->context->controller->registerJavascript(
			'module-lbm-co-js',
			'modules/'.$this->name.'/views/js/sc/checkout_form.js',
			[
			  'priority' => 198,
			  'attribute' => 'async',
			  'position' => 'bottom',
			  'inline' => false,
			]
		);
	}

	public function displaySCCheckoutLoginForm()
	{
		$out_html = '';
		$id_country = Tools::getValue('lbm_id_country');
		if (empty($id_country)) {
			$id_country = $this->default_country;
		}
		$LOGINBYMOBILE_TWOWAYFACTOR = (int)Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR');
		$this->assignCountries($id_country);
		$this->context->smarty->assign('lbmLoadingImgsBaseDir', __PS_BASE_URI__);
		$this->context->smarty->assign('back', 'order');
		$this->context->smarty->assign('LOGINBYMOBILE_TWOWAYFACTOR', $LOGINBYMOBILE_TWOWAYFACTOR);

		$link_password_recovery = Context::getContext()->link->getPageLink('password', true);
		$this->smarty->assign(array('link_password_recovery' => $link_password_recovery));

		$out_html = $this->display(__FILE__, 'views/templates/hook/sc/checkout_login_form.tpl');
		if ($LOGINBYMOBILE_TWOWAYFACTOR) {
			$out_html .= $this->display(__FILE__, 'views/templates/hook/sc/twf_form.tpl');
		}
		//print_r($out_html);
		//die();
		return $out_html;
	}

	/**
	* Method Name: hookDisplayCustomerLoginFormAfter
	* This hook is present just below the login form (Email / Password / Forgot Password)
	* At the same time, this hook has Not been used in the Login Block present in the Checkout Flow. Probably a defect as of PSv1742.
	* We need to find a way to add this hook in Checkout Flow (time sake) and (TO DO :::) the same has to be removed when the this HOOK is added in future PSv1.7 versions
	*/
	public function hookDisplayCustomerLoginFormAfter($params)
	{
		$out_html = '';
		$id_country = Tools::getValue('lbm_id_country');
		if (empty($id_country)) {
			$id_country = $this->default_country;
		}
		$this->assignCountries($id_country);
		$this->context->smarty->assign('lbmLoadingImgsBaseDir', __PS_BASE_URI__);
		
		$link_password_recovery = Context::getContext()->link->getPageLink('password', true);
		$this->smarty->assign(array('link_password_recovery' => $link_password_recovery));		
		
		$out_html = $this->display(__FILE__, 'views/templates/hook/login_form.tpl');
		if ((int)Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR')) {
			$out_html .= $this->display(__FILE__, 'views/templates/hook/twf_form.tpl');
		}
		return $out_html;
	}

	/**
	* Method Name: hookDisplayCustomerAccountFormTop
	* This hook is present just above the Account Registration block title (First name / Last Name / Email / Password ...)
	* At the same time, this hook has Not been used in the Registration Block present in the Checkout Flow. Probably a defect as of PSv1742.
	* This Hook is NOT displayed in the LOGIN form
	* Due to this Temporary inconsistancies in the display, this hook is Not being used as of PSv1742
	* (TO DO :::) Check if any useful information can be provided to the Cusomter using this hook.
	*/
	public function hookDisplayCustomerAccountFormTop($params) {}

	/**
	* Method Name: hookDisplayCustomerAccountForm
	* This hook is present just below the Account Registration block Save button (First name / Last Name / Email / Password ...)
	* At the same time, this hook has Not been used in the Registration Block present in the Checkout Flow. Probably a defect as of PSv1742.
	* This Hook is NOT displayed in the LOGIN form
	* Due to this Temporary inconsistancies in the display, this hook is Not being used as of PSv1742
	* (TO DO :::) Check if any useful information can be provided to the Cusomter using this hook.
	*/
	public function hookDisplayCustomerAccountForm($params) {}

	/**
	* Method Name: hookActionCustomerAccountUpdate
	* This hook was required to update the Mobile Number registered by the Customer during the Account Registration process. As of PSv1.7 the Mobile Number shall be updated using the HOOK-CustomerFieldsValidation.
	* (TO DO :::) Check if this HOOK logic is still required.
	*/
	public function hookActionCustomerAccountUpdate($params) {}

	/**
	* Method Name: hookActionCustomerAccountAdd
	* This hook is required to register the Mobile Number of the Customer during the Account Registration process. The Mobile Number and OTP shall be verified in the HOOK-CustomerFieldsValidation, but, as we would not know if the Customer registration is successfull (Due to the possible data issues with other Registration fields), we need to Register the Mobile Number only after the Customer is added to the system. Hence this Hook is still being used.
	* (TO DO :::) Check HOOK-CustomerFieldsValidation method to remove any possible redundant code.
	*/
	public function hookActionCustomerAccountAdd($params)	
	{
		$lbm_ca_mobile_number = Tools::getValue('lbm_ca_mobile_number');
		$lbm_ca_id_country = (int) Tools::getValue('lbm_ca_id_country', 0);
		$sessionid = trim(Tools::getValue('session_key_reg'));
		$mobile_verified = 0;
		// TO DO :: Should the active flag be verified from the Session. 
		$LOGINBYMOBILE_OTP = Configuration::get('LOGINBYMOBILE_OTP');
		$LOGINBYMOBILE_OTP_list = unserialize($LOGINBYMOBILE_OTP);
		if (!isset($LOGINBYMOBILE_OTP_list[$lbm_ca_id_country]) || empty($LOGINBYMOBILE_OTP_list[$lbm_ca_id_country])) {
			// Not verified // Directly update mobile number and Country code
			$mobile_verified = 0;
		} else {
			//verified // check the session flag and update mobile number and Country code
			$mobile_verified = 1;
		}
		$mobileNumList = new Mobilenumlist();
		$mobileNumList->otp_mobile_num = $lbm_ca_mobile_number;
		$mobileNumList->otp_flag = $mobile_verified;
		$mobileNumList->id_customer = $params['newCustomer']->id;
		$mobileNumList->id_lang = (int)Context::getContext()->language->id;
		$mobileNumList->id_country = $lbm_ca_id_country;
		$mobileNumList->is_guest = 0;
		$mobileNumList->add();
		$enable_welSms = Configuration::get('LOGINBYMOBILE_WELCOMESMS_ENABLE');

		if ($enable_welSms) {
			$phone_number = $lbm_ca_mobile_number;
			$SMS_TEXT = Configuration::get('LOGINBYMOBILE_WELCOMESMS_TEXT', (int)Context::getContext()->language->id);
			$customer_name = $params['newCustomer']->firstname;
			$shop_name = Configuration::get('PS_SHOP_NAME');
			$message_text = str_replace("{customername}", $customer_name, $SMS_TEXT);
			$message_text = str_replace("{shop}", $shop_name, $message_text);
			$provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
			$lbm_sms_sender = new LBMSMSSender();
			$subject = "Welcome Message";
			$is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');
			$LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
			$LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
			if (!isset($LOGINBYMOBILE_TXN_list[$lbm_ca_id_country]) || empty($LOGINBYMOBILE_TXN_list[$lbm_ca_id_country])) {
				$LOGINBYMOBILE_TXN_list[$lbm_ca_id_country] = 0;
			}
			$is_transactional = (int)$LOGINBYMOBILE_TXN_list[$lbm_ca_id_country];
			$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $lbm_ca_id_country, $is_transactional, $is_unicode, "WELCOME".$message_text);
		}		
	}

	/**
	* Method Name: hookActionObjectCustomerDeleteAfter
	* This hook is required to remove the Cusotmer identity (Mobile Number / Country / Customer ID...etc) from the local table, when the Customer is removed from the Shop.
	* (TO DO :::) Check if there are any changes in the HOOK parameters and it performs as per the requirement.
	*/
	public function hookActionObjectCustomerDeleteAfter($params)
	{
		if (isset($params['object'])) {
			$customer = $params['object'];
			$id_customer = $customer->id;
			$mobilenumlistId = Mobilenumlist::getIdByCustomer($id_customer);
			$mobileNumList = new Mobilenumlist($mobilenumlistId);
			$mobileNumList->delete();
		}
		return true;
	}

	/**
	* Method Name: hookDisplayOverrideTemplate
	* This hook is used to change the Template file in the Front Office. This hook is used to handle Forgot Password scenario as of PSv1.6.
	* As of PSv17, Media hook is being used to handle the Forgot Password scenario. Hence this hook has been orphaned temporarily
	* (TO DO :::) Check if any useful information can be provided to the Cusomter using this hook.
	*/
    //public function hookDisplayOverrideTemplate($params) {}

	/**
	* Method Name: hookActionValidateOrder
	* This hook is currently used as a SMS Notification hook
	* (TO DO :::) Check if additional notifications can be added.
	* (TO DO :::) Check if Bulk Notifications can be implemented.
	*/
	public function hookActionValidateOrder($params)
	{
		$smsToCustomer = (int)Configuration::get('LOGINBYMOBILE_NNEWORDER_ENABLE');
		$smsToAdmin = (int)Configuration::get('LOGINBYMOBILE_NNEWORDERA_ENABLE');
		if ($smsToCustomer || $smsToAdmin) {
			$text = Configuration::get('LOGINBYMOBILE_NNEWORDER', (int)Context::getContext()->language->id);
			$texttoadmin = Configuration::get('LOGINBYMOBILE_NNEWORDERA', (int)Context::getContext()->language->id);

			$host = 'https://'.Tools::getHttpHost(false, true);
			$id_lang = (int)Context::getContext()->language->id;
			$currency = $params['currency'];
			$order = $params['order'];
			$customer = $params['customer'];
			$delivery = new Address((int)$order->id_address_delivery);
			$invoice = new Address((int)$order->id_address_invoice);
			$delivery_state = $delivery->id_state ? new State((int)$delivery->id_state) : false;
			$invoice_state = $invoice->id_state ? new State((int)$invoice->id_state) : false;			
			
			$order_date_text = Tools::displayDate($order->date_add, (int)$id_lang);
			$carrier = new Carrier((int)$order->id_carrier);
			$message = $order->getFirstMessage();

			$items = '';
			$products = $params['order']->getProducts();
			$custom_datas = Product::getAllCustomizedDatas((int)$params['cart']->id);
			Product::addCustomizationPrice($products, $custom_datas);
			foreach ($products as $key => $product) {
				$key;
				$unit_price = $product['product_price_wt'];
				//$ref = $product['product_reference'];
				$customization_text = '';
				if (isset($custom_datas[$product['product_id']][$product['product_attribute_id']])) {
					foreach ($custom_datas[$product['product_id']][$product['product_attribute_id']] as $customization) {
						if (isset($customization['datas'][_CUSTOMIZE_TEXTFIELD_])) {
							foreach ($customization['datas'][_CUSTOMIZE_TEXTFIELD_] as $text) {
								$customization_text .= $text['name'].': '.$text['value'].'\n';
							}
						}
					}
					$customization_text = rtrim($customization_text, '\n');
				}

				$items .= (int)$product['product_quantity'].'x '.$product['product_name'].
				(isset($product['attributes_small']) ? ' '.$product['attributes_small'] : '').
				(!empty($customization_text) ? '<br />'.$customization_text : '').
				' ('.Tools::displayPrice($unit_price, $currency, false).') = '.
				Tools::displayPrice(($unit_price * $product['product_quantity']), $currency, false).'\n';
			}

			$values = array(
				'{firstname}' => $customer->firstname,
				'{lastname}' => $customer->lastname,
				'{email}' => $customer->email,
				'{delivery_company}' => $delivery->company,
				'{delivery_firstname}' => $delivery->firstname,
				'{delivery_lastname}' => $delivery->lastname,
				'{delivery_address1}' => $delivery->address1,
				'{delivery_address2}' => $delivery->address2,
				'{delivery_city}' => $delivery->city,
				'{delivery_postal_code}' => $delivery->postcode,
				'{delivery_country}' => $delivery->country,
				'{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
				'{delivery_phone}' => $delivery->phone_mobile,
				'{delivery_other}' => $delivery->other,
				'{invoice_company}' => $invoice->company,
				'{invoice_firstname}' => $invoice->firstname,
				'{invoice_lastname}' => $invoice->lastname,
				'{invoice_address2}' => $invoice->address2,
				'{invoice_address1}' => $invoice->address1,
				'{invoice_city}' => $invoice->city,
				'{invoice_postal_code}' => $invoice->postcode,
				'{invoice_country}' => $invoice->country,
				'{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
				'{invoice_phone}' => $invoice->phone_mobile,
				'{invoice_other}' => $invoice->other,
				'{order_name}' => sprintf('%06d', $order->id),
				'{date}' => $order_date_text,
				'{carrier}' => (($carrier->name == '0') ? Configuration::get('PS_SHOP_NAME') : $carrier->name),
				'{ref}' => $order->reference,
				'{payment}' => Tools::substr($order->payment, 0, 32),
				'{items}' => $items,
				'{total_paid}' => Tools::displayPrice($order->total_paid, $currency),
				'{total_products}' => Tools::displayPrice($order->getTotalProductsWithTaxes(), $currency),
				'{total_discounts}' => Tools::displayPrice($order->total_discounts, $currency),
				'{total_shipping}' => Tools::displayPrice($order->total_shipping, $currency),
				'{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $currency),
				'{currency}' => $currency->sign,
				'{message}' => $message,
				'{shopname}' => Configuration::get('PS_SHOP_NAME'),
				'{shopurl}' => $host.__PS_BASE_URI__
			);

			$provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
			$lbm_sms_sender = new LBMSMSSender();
			$subject = "Order Created Successfully";
			$is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');
			$LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
			$LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
			$phone_numberrow = Mobilenumlist::getMobileNum($customer->id);
			if (!empty($phone_numberrow)) {
				$phone_number = $phone_numberrow['otp_mobile_num'];
				$id_country =  $phone_numberrow['id_country'];
				if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
					$LOGINBYMOBILE_TXN_list[$id_country] = 0;
				}
				$is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];
if (!empty($phone_number)) {
				if (!empty($text)) {
					$message_text = str_replace(array_keys($values), array_values($values), $text);
					$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'New Order '.$message_text);
				}
}				
			}

			$adminCountryId = (int)Configuration::get('LOGINBYMOBILE_ADMINCOUNTRY');
			$adminPhoneNumber = Configuration::get('LOGINBYMOBILE_ADMINPHONE');

			if (!empty($adminPhoneNumber)) {
				if (!empty($texttoadmin)) {
					$message_text = str_replace(array_keys($values), array_values($values), $texttoadmin);
					$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $adminPhoneNumber, $message_text, $subject, $adminCountryId, $is_transactional, $is_unicode, 'New Order '.$message_text);
				}
			}
		}
	}
	/**
	* Method Name: hookActionOrderStatusUpdate
	* This hook is currently used as a SMS Notification hook
	* (TO DO :::) Check if additional notifications can be added.
	* (TO DO :::) Check if Bulk Notifications can be implemented.
	*/
	public function hookActionOrderStatusUpdate($params)
	{
		$order = new Order((int)$params['id_order']);
		
		$smsToCustomerFirstOrderCron = 0;
		$smsToCustomerFirstOrder = 0;
		$order_count = (int)Order::getCustomerNbOrders((int)$order->id_customer);
		if ($order_count === 1) {
			$smsToCustomerFirstOrderCron = (int)Configuration::get('LBM_FIRSTORDER_CRON_ENABLE');
			$smsToCustomerFirstOrder = (int)Configuration::get('LBM_FIRSTORDER_SMS_ENABLE');			
		}
		
		
		$deliveredEnable = (int)Configuration::get('LOGINBYMOBILE_NDELIVERED_ENABLE');
		$shippedEnable = (int)Configuration::get('LOGINBYMOBILE_NSHIPPED_ENABLE');
		$canceledEnable = (int)Configuration::get('LOGINBYMOBILE_NCANCELED_ENABLE');
		
		if ($canceledEnable || $deliveredEnable || $shippedEnable || $smsToCustomerFirstOrder || $smsToCustomerFirstOrderCron) {
			$newStatusId = (int)$params['newOrderStatus']->id;
			$canceledStatusId = (int)Configuration::get('LOGINBYMOBILE_STATUS_CANCELLED');
			$deliveredStatusId = (int)Configuration::get('LOGINBYMOBILE_STATUS_DELIVERED');
			$shippedStatusId = (int)Configuration::get('LOGINBYMOBILE_STATUS_SHIPPED');
			if ($newStatusId === $canceledStatusId || $newStatusId === $deliveredStatusId || $newStatusId === $shippedStatusId) {
				$current_order_state = $order->getCurrentOrderState();
				if ($current_order_state) {
					$state = $params['newOrderStatus']->name;
					$customer = new Customer((int)$order->id_customer);
					$carrier = new Carrier((int)$order->id_carrier);

					$provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
					$lbm_sms_sender = new LBMSMSSender();
					$is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');
					$LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
					$LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
					$phone_numberrow = Mobilenumlist::getMobileNum($customer->id);
					if (!empty($phone_numberrow)) {
						$phone_number = isset($phone_numberrow['otp_mobile_num']) ? $phone_numberrow['otp_mobile_num'] : "";
						$id_country =  $phone_numberrow['id_country'];
						if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
							$LOGINBYMOBILE_TXN_list[$id_country] = 0;
						}
						$is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];

						$text = "";
						$smsToCustomerFirstOrderText = "";
						$subject = "";
						$values = array(
							'{firstname}' => $customer->firstname,
							'{lastname}' => $customer->lastname,
							'{ref}' => $order->reference,
							'{tracking_number}' => $order->shipping_number,
							'{carrier}' => (($carrier->name == '0') ? Configuration::get('PS_SHOP_NAME') : $carrier->name),
							'{carrier_url}' => $carrier->url,
							'{order_id}' => sprintf('%06d', $order->id),
							'{order_state}' => $state,
							'{shopname}' => Configuration::get('PS_SHOP_NAME')
						);						
						if ($newStatusId === $deliveredStatusId  && $deliveredEnable) {
							$text = Configuration::get('LOGINBYMOBILE_NDELIVERED', (int)Context::getContext()->language->id);
							$subject = "Order Delivered";
						} else if ($newStatusId === $shippedStatusId) {
							$text = Configuration::get('LOGINBYMOBILE_NSHIPPED', (int)Context::getContext()->language->id);
							$subject = "Order Shipped";
						} else {
							$text = Configuration::get('LOGINBYMOBILE_NCANCELED', (int)Context::getContext()->language->id);
							$subject = "Order Cancelled";
						}

						if (!empty($text) && !empty($phone_number)) {
							$message_text = str_replace(array_keys($values), array_values($values), $text);
							$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, $subject.$message_text);
						}

						if ($newStatusId === $deliveredStatusId  && ($smsToCustomerFirstOrderCron || $smsToCustomerFirstOrder)) {
							$smsToCustomerFirstOrderText = Configuration::get('LBM_FIRSTORDER_SMS_TEXT', (int)Context::getContext()->language->id);
							$subject = "First Order Delivered";

								$message_text = str_replace(array_keys($values), array_values($values), $smsToCustomerFirstOrderText);
								if ($smsToCustomerFirstOrderCron) {
									$params = array();
									$params['id_customer'] = $customer->id;
									$params['email'] = $customer->email;
									$params['id_order'] = $order->id;
									$params['phone_number'] = $phone_number;
									$params['id_country'] = $id_country;
									$params['transactional'] = $is_transactional;
									$params['unicode'] = $is_unicode;
									$params['message'] = $message_text;
									$params['delivered_on'] = date('Y-m-d H:i:s', time());
									$params['active'] = 1;
									$this->addLbmFirstOrderDelivery($params);
								}
							if (!empty($phone_number)) {
								if ($smsToCustomerFirstOrder) {
									$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, $subject.$message_text);
								}
							}							
						}						
					}
				}
			}
		}
	}

	/**
	* Method Name: hookActionAdminOrdersTrackingNumberUpdate
	* This hook is currently used as a SMS Notification hook
	* (TO DO :::) Check if additional notifications can be added.
	* (TO DO :::) Check if Bulk Notifications can be implemented.
	*/
	public function hookActionAdminOrdersTrackingNumberUpdate($params)
	{
		$trackingEnabled = (int)Configuration::get('LOGINBYMOBILE_NTRACKING_ENABLE');
		if ($trackingEnabled) {
			$text = Configuration::get('LOGINBYMOBILE_NTRACKING', (int)Context::getContext()->language->id);
			$order = $params['order'];
			$customer = new Customer((int)$order->id_customer);
			$carrier = new Carrier((int)$order->id_carrier);

			$provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
			$lbm_sms_sender = new LBMSMSSender();
			$is_unicode = (int)Configuration::get('LOGINBYMOBILE_SMS_UNICODE');
			$LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
			$LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
			$phone_numberrow = Mobilenumlist::getMobileNum($customer->id);
			$subject = "Tracking";
			if (!empty($phone_numberrow)) {
				$phone_number = $phone_numberrow['otp_mobile_num'];
				$id_country =  $phone_numberrow['id_country'];
				if (!isset($LOGINBYMOBILE_TXN_list[$id_country]) || empty($LOGINBYMOBILE_TXN_list[$id_country])) {
					$LOGINBYMOBILE_TXN_list[$id_country] = 0;
				}
				$is_transactional = (int)$LOGINBYMOBILE_TXN_list[$id_country];

				if (!empty($text) && !empty($phone_number)) {
					$values = array(
						'{firstname}' => $customer->firstname,
						'{lastname}' => $customer->lastname,
						'{tracking_number}' => $order->shipping_number,
						'{carrier}' => (($carrier->name == '0') ? Configuration::get('PS_SHOP_NAME') : $carrier->name),
						'{carrier_url}' => $carrier->url,
						'{ref}' => $order->reference,
						'{order_id}' => sprintf('%06d', $order->id),
						'{shopname}' => Configuration::get('PS_SHOP_NAME')
					);
					$message_text = str_replace(array_keys($values), array_values($values), $text);
					$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $phone_number, $message_text, $subject, $id_country, $is_transactional, $is_unicode, 'Tracking '.$message_text);
				}
			}
		}
	}

/********************* Hooks - End ***********************/

/********************* Renderforms - Start ***********************/
	public function getContent()
	{
		
	//$this->uninstallTabs();
    //$this->installTabs();
		
		Configuration::updateValue(
			'LOGINBYMOBILE_SMS_PROVIDER',
			Tools::getValue('LOGINBYMOBILE_SMS_PROVIDER',
				Configuration::get('LOGINBYMOBILE_SMS_PROVIDER')));
		$provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
		$html = '';
		/* Provider Change - Start */
		if ($provider === '') {
			$html .='<div id="flashMessage"><div class="flash fail"></div></div>';
		} else if (empty($provider)) {
			$provider = 'prestasms';
			$html .='<div id="flashMessage"><div class="flash fail"></div></div>';
		} else {
			$html .='<div id="flashMessage"><div class="flash fail">You have selected '.Tools::strtoupper($provider).' as your Message provider. Kindly make sure you update '.Tools::strtoupper($provider).' SETTINGS tab</div></div>';
		}
		/* Provider Change - End */

		if (Tools::isSubmit('submitLoginModule')) {
			Configuration::updateValue('LOGINBYMOBILE_DDB_ENABLE', Tools::getValue('LOGINBYMOBILE_DDB_ENABLE'));
			Configuration::updateValue('LOGINBYMOBILE_SMS_UNICODE', Tools::getValue('LOGINBYMOBILE_SMS_UNICODE'));
			Configuration::updateValue('LOGINBYMOBILE_ADD_PREFIX',Tools::getValue('LOGINBYMOBILE_ADD_PREFIX'));

			$html .= $this->displayConfirmation($this->l('Settings Updated'));
			$this->tabName = 'renderForm';
		
			/* Provider Change - Start */
		
		} else if (Tools::isSubmit('submitMsgbucketinForm')) {
			
			Configuration::updateValue('LOGINBYMOBILE_MSGBUCKETIN_KEY', Tools::getValue('LOGINBYMOBILE_MSGBUCKETIN_KEY'));
			
			 $html .= $this->displayConfirmation($this->l('Msgbucket India Settings Updated'));
			$this->tabName = 'renderMsgbucketinForm';     
			
		
		} else if (Tools::isSubmit('submitMsgbucketusForm')) {
			
			Configuration::updateValue('LOGINBYMOBILE_MSGBUCKETUS_KEY', Tools::getValue('LOGINBYMOBILE_MSGBUCKETUS_KEY'));
			
			 $html .= $this->displayConfirmation($this->l('Msgbucket International Settings Updated'));
			$this->tabName = 'renderMsgbucketusForm';     
		
			/* Provider Change - End */

		} else if (Tools::isSubmit('submitLBMEmailForm')) {
			Configuration::updateValue('LOGINBYMOBILE_EMAIL_REQUIRED', Tools::getValue('LOGINBYMOBILE_EMAIL_REQUIRED'));
			Configuration::updateValue('LOGINBYMOBILE_DISP_MAIL_AC_PAGE', Tools::getValue('LOGINBYMOBILE_DISP_MAIL_AC_PAGE'));
			Configuration::updateValue('LOGINBYMOBILE_MAIL_DOMAIN',Tools::getValue('LOGINBYMOBILE_MAIL_DOMAIN'));
			$html .= $this->displayConfirmation($this->l('Email Field Settings Updated'));
			$this->tabName = 'renderEmailForm';
		} else if (Tools::isSubmit('submitLBMOTPSMSForm')) {
			Configuration::updateValue('LOGINBYMOBILE_TWOWAYFACTOR', Tools::getValue('LOGINBYMOBILE_TWOWAYFACTOR'));
			Configuration::updateValue('LOGINBYMOBILE_LOG', Tools::getValue('LOGINBYMOBILE_LOG'));
			Configuration::updateValue('LOGINBYMOBILE_SMS_MAX_COUNT', Tools::getValue('LOGINBYMOBILE_SMS_MAX_COUNT'));
			Configuration::updateValue('LOGINBYMOBILE_OTP_TIMEINTERVAL', Tools::getValue('LOGINBYMOBILE_OTP_TIMEINTERVAL'));
			Configuration::updateValue('LOGINBYMOBILE_WELCOMESMS_ENABLE', Tools::getValue('LOGINBYMOBILE_WELCOMESMS_ENABLE'));

			$this->setNewValueForLangConfiguration('LOGINBYMOBILE_SMS_TEXT');			
			$this->setNewValueForLangConfiguration('LOGINBYMOBILE_WELCOMESMS_TEXT');
			$this->setNewValueForLangConfiguration('LOGINBYMOBILE_PASSWORD_TEXT');
			$this->setNewValueForLangConfiguration('LOGINBYMOBILE_TWOWAYFACTOR_TEXT');
			
			Configuration::updateValue('LOGINBYMOBILE_CODE_LENGTH', Tools::getValue('LOGINBYMOBILE_CODE_LENGTH'));
			Configuration::updateValue('LOGINBYMOBILE_TWOWAYFACTOR', Tools::getValue('LOGINBYMOBILE_TWOWAYFACTOR'));
			Configuration::updateValue('LOGINBYMOBILE_ATTEMPTS', Tools::getValue('LOGINBYMOBILE_ATTEMPTS'));
			Configuration::updateValue('LOGINBYMOBILE_SESSION_TIME', Tools::getValue('LOGINBYMOBILE_SESSION_TIME'));

			$html .= $this->displayConfirmation($this->l('OTP SMS Settings Updated'));
			$this->tabName = 'renderOTPSMSForm';
		} else if (Tools::isSubmit('submitLBMSMSNotificationForm')) {
			
			$this->setNewValueForLangConfiguration('LOGINBYMOBILE_NNEWORDERA');
			$this->setNewValueForLangConfiguration('LOGINBYMOBILE_NNEWORDER');
			$this->setNewValueForLangConfiguration('LOGINBYMOBILE_NTRACKING');
			$this->setNewValueForLangConfiguration('LOGINBYMOBILE_NDELIVERED');
			$this->setNewValueForLangConfiguration('LOGINBYMOBILE_NSHIPPED');
			$this->setNewValueForLangConfiguration('LOGINBYMOBILE_NCANCELED');
			$this->setNewValueForLangConfiguration('LBM_SELLER_NEWORDER_TEXT');
			$this->setNewValueForLangConfiguration('LBM_SELLER_LOWSTOCK_TEXT');
			$this->setNewValueForLangConfiguration('LBM_FIRSTORDER_SMS_TEXT');
			
			
			Configuration::updateValue('LOGINBYMOBILE_NNEWORDERA_ENABLE', Tools::getValue('LOGINBYMOBILE_NNEWORDERA_ENABLE'));
			
			Configuration::updateValue('LBM_SELLER_NEWORDER_ENABLE', Tools::getValue('LBM_SELLER_NEWORDER_ENABLE'));
			Configuration::updateValue('LBM_SELLER_LOWSTOCK_ENABLE', Tools::getValue('LBM_SELLER_LOWSTOCK_ENABLE'));
			
			Configuration::updateValue('LBM_FIRSTORDER_CRON_ENABLE', Tools::getValue('LBM_FIRSTORDER_CRON_ENABLE'));
			Configuration::updateValue('LBM_FIRSTORDER_SMS_ENABLE', Tools::getValue('LBM_FIRSTORDER_SMS_ENABLE'));			
			Configuration::updateValue('LOGINBYMOBILE_NNEWORDER_ENABLE', Tools::getValue('LOGINBYMOBILE_NNEWORDER_ENABLE'));
			Configuration::updateValue('LOGINBYMOBILE_NTRACKING_ENABLE', Tools::getValue('LOGINBYMOBILE_NTRACKING_ENABLE'));
			Configuration::updateValue('LOGINBYMOBILE_NDELIVERED_ENABLE', Tools::getValue('LOGINBYMOBILE_NDELIVERED_ENABLE'));
			Configuration::updateValue('LOGINBYMOBILE_NSHIPPED_ENABLE', Tools::getValue('LOGINBYMOBILE_NSHIPPED_ENABLE'));
			Configuration::updateValue('LOGINBYMOBILE_NCANCELED_ENABLE', Tools::getValue('LOGINBYMOBILE_NCANCELED_ENABLE'));
			Configuration::updateValue('LOGINBYMOBILE_ADMINPHONE', Tools::getValue('LOGINBYMOBILE_ADMINPHONE'));
			Configuration::updateValue('LOGINBYMOBILE_ADMINCOUNTRY', Tools::getValue('LOGINBYMOBILE_ADMINCOUNTRY'));
			Configuration::updateValue('LOGINBYMOBILE_STATUS_CANCELLED', Tools::getValue('LOGINBYMOBILE_STATUS_CANCELLED'));
			Configuration::updateValue('LOGINBYMOBILE_STATUS_DELIVERED', Tools::getValue('LOGINBYMOBILE_STATUS_DELIVERED'));
			Configuration::updateValue('LOGINBYMOBILE_STATUS_ORDER', Tools::getValue('LOGINBYMOBILE_STATUS_ORDER'));
			Configuration::updateValue('LOGINBYMOBILE_STATUS_PC', Tools::getValue('LOGINBYMOBILE_STATUS_PC'));			
			Configuration::updateValue('LOGINBYMOBILE_STATUS_SHIPPED', Tools::getValue('LOGINBYMOBILE_STATUS_SHIPPED'));

			$html .= $this->displayConfirmation($this->l('Notifications'));
			$this->tabName = 'renderSMSNotificationsForm';
		} else if (Tools::isSubmit('savemblength')) {
			$countrylists = $this->getcountrylists();
			$countrymblength = array();
			$country_mobile_size_min = array();
			$country_enable_otp = array();
			$country_txn_sms = array();
			foreach ($countrylists as $key => $type) {
				$LBMCOUNTRY_ID = Tools::getValue('LBMCOUNTRY_ID_'.$type['id']);
				$countrymblength[$type['id']] = empty($LBMCOUNTRY_ID) ? 0 : $LBMCOUNTRY_ID;

				$LOGINBYMOBILE_MOB_MIN = Tools::getValue('LOGINBYMOBILE_MOB_MIN_'.$type['id']);
				$country_mobile_size_min[$type['id']] = empty($LOGINBYMOBILE_MOB_MIN) ? 0 : $LOGINBYMOBILE_MOB_MIN;

				$LOGINBYMOBILE_TXN = Tools::getValue('LOGINBYMOBILE_TXN_'.$type['id']);
				$country_txn_sms[$type['id']] = empty($LOGINBYMOBILE_TXN) ? 0 : $LOGINBYMOBILE_TXN;

				$LOGINBYMOBILE_OTP = Tools::getValue('LOGINBYMOBILE_OTP_'.$type['id']);
				$country_enable_otp[$type['id']] = empty($LOGINBYMOBILE_OTP) ? 0 : $LOGINBYMOBILE_OTP;
			}
			Configuration::updateValue('LOGINBYMOBILE_MBLENGTH', serialize($countrymblength));
			Configuration::updateValue('LOGINBYMOBILE_MBLENGTH_MIN', serialize($country_mobile_size_min));

			Configuration::updateValue('LOGINBYMOBILE_TXN', serialize($country_txn_sms));
			Configuration::updateValue('LOGINBYMOBILE_OTP', serialize($country_enable_otp));

			Configuration::updateValue('LOGINBYMOBILE_ENABLE_LOGIN_FEATURE', Tools::getValue('LOGINBYMOBILE_ENABLE_LOGIN_FEATURE'));
			Configuration::updateValue('LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN', Tools::getValue('LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN'));
			Configuration::updateValue('LOGINBYMOBILE_MOBILE_REG', Tools::getValue('LOGINBYMOBILE_MOBILE_REG'));
			Configuration::updateValue('LOGINBYMOBILE_ACCEPTZERO', Tools::getValue('LOGINBYMOBILE_ACCEPTZERO'));

			$html .= $this->displayConfirmation($this->l('Mobile Number Length Updated'));
			$this->tabName = 'setCountrymblForm';
		} else if (Tools::isSubmit('upgradeLBMModule')) {
			$this->upgradeLBMModule();
			$html .= $this->displayConfirmation($this->l('Hooks updated successfully. Merge Override Manually'));
			$this->tabName = 'upgradeLBMModuleTab';
		}
		if ($this->tabName) {
			$html .= '<script>$(document).ready(function() {$("#'.$this->tabName.'").addClass("active");$(".'.$this->tabName.'").parents(".nav-tabs > li").addClass("active");});</script>';
		} else {
			$html .= '<script>$(document).ready(function() {$("#renderForm").addClass("active");                    $(this).attr("href").indexOf("#renderForm").addClass("active");});</script>';
		}
		/* Provider Change - Start */
		$html .= '<ul class="nav nav-tabs" role="tablist">';
		$html .= '<li ><a class="renderForm" href="#renderForm" role="tab" data-toggle="tab">SETTINGS</a></li>';
		$html .= '<li><a class="setCountrymblForm" href="#setCountrymblForm" role="tab" data-toggle="tab"> Mobile Number</a></li>';
		$html .= '<li><a class="renderOTPSMSForm" href="#renderOTPSMSForm" role="tab" data-toggle="tab"> OTP / SMS</a></li>';
		$html .= '<li><a class="renderEmailForm" href="#renderEmailForm" role="tab" data-toggle="tab"> Email Field </a></li>';
		$html .= '<li><a class="renderSMSNotificationsForm" href="#renderSMSNotificationsForm" role="tab" data-toggle="tab"> Notifications </a></li>';
		$providerUcfirst = Tools::ucfirst($provider);
		$html .= '<li><a class="render'.$providerUcfirst.'Form" href="#render'.$providerUcfirst.'Form" role="tab" data-toggle="tab"> '.$providerUcfirst.' </a></li>';
		$html .= '</ul>';
		/* Provider Change - End */

		/* Provider Change - Start */
		$html .= '<div class="tab-content">';
		$html .= '<div class="tab-pane " id="renderForm">'.$this->renderForm_1_6().'</div>';
		$html .= '<div class="tab-pane" id="setCountrymblForm">'.$this->setCountrymblForm().'</div>';
		$html .= '<div class="tab-pane" id="renderOTPSMSForm">'.$this->renderOTPSMSForm().'</div>';
		$html .= '<div class="tab-pane" id="renderEmailForm">'.$this->renderEmailForm().'</div>';
		$html .= '<div class="tab-pane" id="renderSMSNotificationsForm">'.$this->renderSMSNotificationsForm().'</div>';
		$methodName = 'render'.$providerUcfirst.'Form';
		$html .= '<div class="tab-pane" id="render'.$providerUcfirst.'Form">'.$this->$methodName().'</div>';
		$html .= '</div>';
		/* Provider Change - End */

		$html .='<style>div.flash.fail {color: #cd0a0a;background-color: #fef1ec;border: #cd0a0a 1px solid;padding: 0.5em;margin: 0 3px;margin-top: 22px;margin-bottom: 12px;text-align: center;border-radius: 2px 2px 2px 2px;}</style>';

		return $html;
	}
	
	public function setNewValueForLangConfiguration($key)
	{
		$langid_value_array = array();
		foreach (Language::getLanguages(false) as $language) {
			$lang_id = $language['id_lang'];
			$key_langid = $key.'_'.$lang_id;
			$langid_value_array[$lang_id] = Tools::getValue($key_langid, '');
		}
		Configuration::updateValue($key, $langid_value_array);
	}
	
	public function setGlobalValueForLangConfiguration($key, $value)
	{
		$langid_value_array = array();
		foreach (Language::getLanguages(false) as $language) {
			$lang_id = $language['id_lang'];
			$langid_value_array[$lang_id] = $value;
		}
		Configuration::updateValue($key, $langid_value_array);
	}	

	protected function renderForm_1_6()
	{
		$fields_form = array(
							'form' => array(
								'legend' => array(
												'title' => $this->l('Settings'),
												'icon' => 'icon-cogs',
											),
								'input' => array(
												array(
													'type' => 'switch',
													'label' => $this->l('Delete Module Data Storage when uninstalled.'),
													'name' => 'LOGINBYMOBILE_DDB_ENABLE',
													'desc' => $this->l('(Customer registered & validated mobile numbers are stored in "mobilenumlist" table. Yes: Deletes "mobilenumlist" table, when "loginbymobile" module is uninstalled. Important: Make sure to take the Back-up of "mobilenumlist" table as it contains verified Client phone numbers which will not be found anywhere else in your Database. No: DO NOT deletes "mobilenumlist" table, even when the module is uninstalled)'),
													'is_bool' => true,
													'values' => array(
																	array(
																		'id' => 'active_on',
																		'value' => 1,
																		'label' => $this->l('Yes')
																	),
																	array(
																		'id' => 'active_off',
																		'value' => 0,
																		'label' => $this->l('No')
																	)
																),
												),
												array(
													'type' => 'select',
													'label' => $this->l('Whatsapp Provider:'),
													'name' => 'LOGINBYMOBILE_SMS_PROVIDER',
													'id' => 'LOGINBYMOBILE_SMS_PROVIDER',
													'options' => array(
																	'optiongroup' => array(
																						'query' => $this->getSMSProvider(),
																						'label' => 'name'
																					),
																	'options' => array(
																					'query' => 'query',
																					'id' => 'id',
																					'name' => 'name'
																				),
																),
												),
												
												array(
													'type' => 'switch',
													'label' => $this->l('Internally add Country prefix code with Mobile Number while sending message'),
													'name' => 'LOGINBYMOBILE_ADD_PREFIX',
													'desc' => $this->l('YES: (Default) Adds Country Prefix code to the Mobile Number before sending OTP message request to Whatsapp Provider No: Does NOT adds Country Prefix to the Mobile Number before sending it to the Whatsapp Provider. This field will soon be added as part of the Message Provider configurations'),
													'is_bool' => true,
													'values' => array(
														array(
														'id' => 'active_on',
														'value' => 1,
														'label' => $this->l('Yes')
														),
														array(
														'id' => 'active_off',
														'value' => 0,
														'label' => $this->l('No')
														)
													),
												),
											),
											'submit' => array(
															'title' => $this->l('Save'),
														),
										)
							);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitLoginModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
		.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
		'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
		'languages' => $this->context->controller->getLanguages(),
		'id_language' => $this->context->language->id,
		);

		return $helper->generateForm(array(
		$fields_form));
	}

	protected function renderGenericForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Generic Message Settings'),
					'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Simulation Mode? :'),
						'name' => 'LOGINBYMOBILE_GENERIC_SIMULATE',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('Username Parameter Name'),
						'name' => 'LOGINBYMOBILE_GENERIC_UNAME_TXT',
						'label' => $this->l('Username Parameter Name'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('Password Parameter Name'),
						'name' => 'LOGINBYMOBILE_GENERIC_PWD_TXT',
						'label' => $this->l('Password Parameter Name'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('Sender Parameter Name'),
						'name' => 'LOGINBYMOBILE_GENERIC_FROM_TXT',
						'label' => $this->l('Sender Parameter Name'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('To / Destination Parameter Name'),
						'name' => 'LOGINBYMOBILE_GENERIC_TO_TXT',
						'label' => $this->l('To Parameter Name'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('Message Parameter Name'),
						'name' => 'LOGINBYMOBILE_GENERIC_MSG_TXT',
						'label' => $this->l('Message Parameter Name'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('Unicode Parameter Name'),
						'name' => 'LOGINBYMOBILE_GENERIC_UNI_TXT',
						'label' => $this->l('Unicode Parameter Name'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('URL'),
						'name' => 'LOGINBYMOBILE_GENERIC_URL',
						'label' => $this->l('URL'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('Ask customer support or sales team'),
						'name' => 'LOGINBYMOBILE_GENERIC_UNAME',
						'label' => $this->l('Account Username'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('Ask customer support or sales team'),
						'name' => 'LOGINBYMOBILE_GENERIC_PWD',
						'label' => $this->l('Password'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'desc' => $this->l('Sender Id'),
						'name' => 'LOGINBYMOBILE_GENERIC_FROM',
						'label' => $this->l('SENDER ID'),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
			)
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitGenericForm';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
		.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
		'fields_value' => $this->getRenderGenericFormValues(),
		'languages' => $this->context->controller->getLanguages(),
		'id_language' => $this->context->language->id,
		);
		return $helper->generateForm(array($fields_form));
	}
	
	/* Provider Change - Start */

	protected function renderMsgbucketinForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Msgbucket India Settings'),
					'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'col' => 3,
						'type' => 'text',
						'name' => 'LOGINBYMOBILE_MSGBUCKETIN_KEY',
						'label' => $this->l('Msgbucket India API Token'),
					),
					
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
			)
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitMsgbucketinForm';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
		.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getRenderMsgbucketinFormValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);
		return $helper->generateForm(array($fields_form));
	}

	
	protected function renderMsgbucketusForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Msgbucket International Settings'),
					'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'col' => 3,
						'type' => 'text',
						'name' => 'LOGINBYMOBILE_MSGBUCKETUS_KEY',
						'label' => $this->l('Msgbucket International API Token'),
					),
					
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
			)
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitMsgbucketusForm';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
		.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getRenderMsgbucketusFormValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);
		return $helper->generateForm(array($fields_form));
	}

	
	/* Provider Change - End */


	protected function renderEmailForm()
	{
		$fields_form = array(
		'form' => array(
					'legend' => array(
									'title' => $this->l('Email Field Settings'),
									'icon' => 'icon-cogs',
								),
					'input' => array(
									array(
										'type' => 'switch',
                                        'label' => $this->l('Is Email required for Customer Registration?'),
										'name' => 'LOGINBYMOBILE_EMAIL_REQUIRED',
										'desc' => $this->l('(YES: Email Id is Required for Registration. NO: Email Id is Optional for Registration.Note: Mobile Number is always Required)'),
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'type' => 'switch',
										'label' => $this->l('Display Email Field in Customer Account Registration page'),
										'name' => 'LOGINBYMOBILE_DISP_MAIL_AC_PAGE',
										'desc' => $this->l('(YES:: Though optional, Email field will be displayed. NO:: Email field will NOT be displayed. Hint:: Works only if Email is set as OPTIONAL for Registration. Hint:: A temporary email will be generated and stored for the Customer.)'),
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 3,
										'type' => 'text',
										'desc' => $this->l('Customer Temporary Email Domain Name. examples: shop.com (or) furniturestore.com (or) fishmart.co.in. Works only if Email-id is made optional for Customer Registration. Temporary email id will be generated as <random_number>@<Email Domain Name> and stored in the Customer Profile'),
										'name' => 'LOGINBYMOBILE_MAIL_DOMAIN',
										'label' => $this->l('Customer Temporary Email Domain Name'),
									),
								),
							'submit' => array(
											'title' => $this->l('Save'),
										),
							)
						);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitLBMEmailForm';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
		.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
								'fields_value' => $this->getRenderEmailFormValues(),
								'languages' => $this->context->controller->getLanguages(),
								'id_language' => $this->context->language->id,
							);
		return $helper->generateForm(array($fields_form));
	}

	protected function renderSMSNotificationsForm()
	{
		$statuslist = $this->getOrderStatus();
		$fields_form = array(
		'form' => array(
					'legend' => array(
									'title' => $this->l('Message Notifications'),
									'icon' => 'icon-cogs',
								),
					'input' => array(
									array(
										'type' => 'switch',
										'label' => $this->l('New Order (Message to Admin):'),
										'name' => 'LOGINBYMOBILE_NNEWORDERA_ENABLE',
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('New Order Text (Message to Admin):'),
										'name' => 'LOGINBYMOBILE_NNEWORDERA',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {firstname}, {lastname}, {email}, {delivery_company}, {delivery_firstname}, {delivery_lastname}, {delivery_address1}, {delivery_address2}, {delivery_city}, {delivery_postal_code}, {delivery_country}, {delivery_state}, {delivery_phone}, {delivery_other}, {invoice_company}, {invoice_firstname}, {invoice_lastname}, {invoice_address1}, {invoice_address2}, {invoice_city}, {invoice_postal_code}, {invoice_country}, {invoice_state}, {invoice_phone}, {invoice_other}, {order_name}, {date}, {carrier}, {ref}, {payment}, {items}, {total_paid}, {total_products}, {total_discounts}, {total_shipping}, {total_wrapping}, {currency}, {message}, {shopname}, {shopurl}'),
									),
									array(
										'type' => 'switch',
										'label' => $this->l('New Order (Message to Seller):'),
										'name' => 'LBM_SELLER_NEWORDER_ENABLE',
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('New Order Text (Message to Seller):'),
										'name' => 'LBM_SELLER_NEWORDER_TEXT',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {order_reference}, {seller_name}, {customer_name}, {customer_email}, {ship_address_name}, {ship_address}, {city}, {state}, {country}, {zipcode}, {phone}, {seller_product_total}, {seller_shipping}, {seller_tax}, {final_total_price}'),
									),
									array(
										'type' => 'switch',
										'label' => $this->l('Low Stock Alert (Message to Seller):'),
										'name' => 'LBM_SELLER_LOWSTOCK_ENABLE',
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('Low Stock Alert Text (Message to Seller):'),
										'name' => 'LBM_SELLER_LOWSTOCK_TEXT',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {seller_name}, {product_name}, {mp_shop_name}, {mail_reason}, {category_name}, {product_price}, {quantity}, {last_quantity}, {ps_shop_name}'),
									),
									array(
										'type' => 'switch',
										'label' => $this->l('First Order Delivered - Send Instant Message to Customer:'),
										'name' => 'LBM_FIRSTORDER_SMS_ENABLE',
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									
									array(
										'type' => 'switch',
										'label' => $this->l('First Order Delivered - Send Message to Customer by CRON job:'),
										'name' => 'LBM_FIRSTORDER_CRON_ENABLE',
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('First Order Delivered - Message to Customer - Text:'),
										'name' => 'LBM_FIRSTORDER_SMS_TEXT',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {firstname}, {lastname}, {ref}, {order_id}, {tracking_number}, {carrier}, {carrier_url}, {order_state}, {shopname}'),
									),
									array(
										'type' => 'switch',
										'label' => $this->l('New Order (Whatsapp to Customer):'),
										'name' => 'LOGINBYMOBILE_NNEWORDER_ENABLE',
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('New Order Text (Whatsapp to Customer):'),
										'name' => 'LOGINBYMOBILE_NNEWORDER',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {firstname}, {lastname}, {email}, {delivery_company}, {delivery_firstname}, {delivery_lastname}, {delivery_address1}, {delivery_address2}, {delivery_city}, {delivery_postal_code}, {delivery_country}, {delivery_state}, {delivery_phone}, {delivery_other}, {invoice_company}, {invoice_firstname}, {invoice_lastname}, {invoice_address1}, {invoice_address2}, {invoice_city}, {invoice_postal_code}, {invoice_country}, {invoice_state}, {invoice_phone}, {invoice_other}, {order_name}, {date}, {carrier}, {ref}, {payment}, {items}, {total_paid}, {total_products}, {total_discounts}, {total_shipping}, {total_wrapping}, {currency}, {message}, {shopname}, {shopurl}'),
									),

									array(
										'type' => 'switch',
										'label' => $this->l('Tracking Number Notification to Customer:'),
										'name' => 'LOGINBYMOBILE_NTRACKING_ENABLE',
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('Tracking Number Notification Text:'),
										'name' => 'LOGINBYMOBILE_NTRACKING',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {firstname}, {lastname}, {tracking_number}, {carrier}, {carrier_url}, {ref}, {order_id}, {shopname}'),
									),

									array(
										'type' => 'switch',
										'label' => $this->l('Order Status to Delivered: Whatsapp to Customer:'),
										'name' => 'LOGINBYMOBILE_NDELIVERED_ENABLE',
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('Order Status to Delivered Text:'),
										'name' => 'LOGINBYMOBILE_NDELIVERED',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {firstname}, {lastname}, {ref}, {order_id}, {tracking_number}, {carrier}, {carrier_url}, {order_state}, {shopname}'),
									),

									array(
										'type' => 'switch',
										'label' => $this->l('Order Status to Shipped: Whatsapp to Customer:'),
										'name' => 'LOGINBYMOBILE_NSHIPPED_ENABLE',
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('Order Status to Shipped Text:'),
										'name' => 'LOGINBYMOBILE_NSHIPPED',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {firstname}, {lastname}, {ref}, {order_id}, {tracking_number}, {carrier}, {carrier_url}, {order_state}, {shopname}'),
									),

									array(
										'type' => 'switch',
										'label' => $this->l('Order Status to Canceled: Whatsapp to Customer:'),
										'name' => 'LOGINBYMOBILE_NCANCELED_ENABLE',
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('Order Status to Canceled Text:'),
										'name' => 'LOGINBYMOBILE_NCANCELED',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {firstname}, {lastname}, {ref}, {order_id}, {tracking_number}, {carrier}, {carrier_url}, {order_state}, {shopname}'),
									),
									array(
										'type' => 'select',
										'label' => $this->l('Admin Country'),
										'name' => 'LOGINBYMOBILE_ADMINCOUNTRY',
										'required' => true,
										'col' => '5',
										'class' => 'fixed-width-md',
										'options' => array(
											'query' => Country::getCountries((int)Context::getContext()->cookie->id_lang),
											'id' => 'id_country',
											'name' => 'name'
										),
										'desc' => $this->l('Select the Administrator Country')
									),
									array(
										'col' => 3,
										'type' => 'text',
										'desc' => $this->l('Admin Mobile Number'),
										'name' => 'LOGINBYMOBILE_ADMINPHONE',
										'label' => $this->l('Admin Mobile Number'),
									),
									array(
										'type' => 'select',
										'label' => $this->l('What is the equivalent Order status for New Order'),
										'required' => true,
										'name' => 'LOGINBYMOBILE_STATUS_ORDER',
										'options' => array(
											'query' => $statuslist,
											'id' => 'id',
											'name' => 'name',
										)
									),
									array(
										'type' => 'select',
										'label' => $this->l('What is the equivalent Order status for Payment Confirmed'),
										'required' => true,
										'name' => 'LOGINBYMOBILE_STATUS_PC',
										'options' => array(
											'query' => $statuslist,
											'id' => 'id',
											'name' => 'name',
										)
									),									
									array(
										'type' => 'select',
										'label' => $this->l('What is the equivalent Order status of Shipped'),
										'required' => true,
										'name' => 'LOGINBYMOBILE_STATUS_SHIPPED',
										'options' => array(
										'query' => $statuslist,
										'id' => 'id',
										'name' => 'name',
										)
									),
									array(
										'type' => 'select',
										'label' => $this->l('What is the equivalent Order status of Delivered'),
										'required' => true,
										'name' => 'LOGINBYMOBILE_STATUS_DELIVERED',
										'options' => array(
											'query' => $statuslist,
											'id' => 'id',
											'name' => 'name',
										)
									),
									array(
										'type' => 'select',
										'label' => $this->l('What is the equivalent Order status of Canceled'),
										'required' => true,
										'name' => 'LOGINBYMOBILE_STATUS_CANCELLED',
										'options' => array(
											'query' => $statuslist,
											'id' => 'id',
											'name' => 'name',
										)
									),
								),
							'submit' => array(
											'title' => $this->l('Save'),
										),
							)
						);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitLBMSMSNotificationForm';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
		.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
								'fields_value' => $this->getRenderSMSNotificationFormValues(),
								'languages' => $this->context->controller->getLanguages(),
								'id_language' => $this->context->language->id,
							);

		return $helper->generateForm(array(
		$fields_form));
	}



	protected function renderOTPSMSForm()
	{
		$fields_form = array(
		'form' => array(
					'legend' => array(
									'title' => $this->l('OTP SMS Settings'),
									'icon' => 'icon-cogs',
								),
					'input' => array(
									array(
										'type' => 'switch',
										'label' => $this->l('Enable OTP Log:'),
										'name' => 'LOGINBYMOBILE_LOG',
										'is_bool' => true,
										'values' => array(
											array(
												'id' => 'active_on',
												'value' => 1,
												'label' => $this->l('Yes')
											),
											array(
												'id' => 'active_off',
												'value' => 0,
												'label' => $this->l('No')
											)
										),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'desc' => $this->l('Keyword {OTP} will be replaced by OTP pin. For example, if your text is "Your Electronics Shop OTP is {OTP}. OTP message will be like "Your Electronics Shop OTP is 12345"'),
										'name' => 'LOGINBYMOBILE_SMS_TEXT',
										'lang' => true,
										'label' => $this->l('OTP Message Text'),
									),
									array(
										'col' => 3,
										'type' => 'text',
										'desc' => $this->l('Number of digits required in OTP. For example, 4 digit OTP will look like "1234"'),
										'name' => 'LOGINBYMOBILE_CODE_LENGTH',
										'label' => $this->l('OTP pin size (number of digits)'),
									),
									array(
										'col' => 3,
										'type' => 'text',
										'desc' => $this->l('Default is 20 seconds'),
										'name' => 'LOGINBYMOBILE_OTP_TIMEINTERVAL',
										'label' => $this->l('Time Interval between two OTP SMS (in seconds)'),
									),
									array(
										'col' => 3,
										'type' => 'text',
										'desc' => $this->l('Sometimes, OTP SMS may not reach Customer due to network issues. In this case Customer shall request for OTP SMS again. But In a particular session, a Customer is allowed to request OTP SMS NOT more than "Maximum Attempts" specified here. After maximum attempts, Customer shall either wait for an hour to resend SMS (or) open a new browser and register again'),
										'name' => 'LOGINBYMOBILE_SMS_MAX_COUNT',
										'label' => $this->l('Maximum attempts allowed to resend OTP SMS'),
									),
									array(
										'type' => 'switch',
										'label' => $this->l('Send Welcome Message to Customer on Successful Account Registration:'),
										'name' => 'LOGINBYMOBILE_WELCOMESMS_ENABLE',
										'desc' => $this->l('(A welcome message SMS will be sent to the Registered mobile number)'),
										'is_bool' => true,
										'values' => array(
														array(
															'id' => 'active_on',
															'value' => 1,
															'label' => $this->l('Yes')
															),
														array(
															'id' => 'active_off',
															'value' => 0,
															'label' => $this->l('No')
															)
													),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('Account Registration Welcome Message Text:'),
										'name' => 'LOGINBYMOBILE_WELCOMESMS_TEXT',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {shop} {customername}'),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('Password message text:'),
										'name' => 'LOGINBYMOBILE_PASSWORD_TEXT',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {shop},{customername},{pwd}'),
									),
									array(
										'type' => 'switch',
										'label' => $this->l('Enable Two Way Factor during Customer login:'),
										'name' => 'LOGINBYMOBILE_TWOWAYFACTOR',
										'desc' => $this->l('(Customer needs to confirm the OTP during every login)'),
										'is_bool' => true,
										'values' => array(
											array(
												'id' => 'active_on',
												'value' => 1,
												'label' => $this->l('Yes')
											),
											array(
												'id' => 'active_off',
												'value' => 0,
												'label' => $this->l('No')
											)
										),
									),
									array(
										'col' => 4,
										'type' => 'textarea',
										'label' => $this->l('Login OTP Message Text:'),
										'name' => 'LOGINBYMOBILE_TWOWAYFACTOR_TEXT',
										'lang' => true,
										'desc' => $this->l('You shall use the following key strings, which  will be replaced by the appropriate value {shop} {customername}'),
									),
									array(
										'col' => 3,
										'type' => 'textarea',
										'label' => $this->l('Number of attempts allowed to enter correct OTP:'),
										'name' => 'LOGINBYMOBILE_ATTEMPTS',
										'default' => 5,
										'desc' => $this->l('Once the Customer exceeds allowed attempts, he should again enter the user id and password from the begining.'),
									),
									array(
										'col' => 3,
										'type' => 'textarea',
										'label' => $this->l('Total time allowed for the Customer to complete the Login Process:'),
										'name' => 'LOGINBYMOBILE_SESSION_TIME',
										'suffix' => 'minutes',
										'desc' => $this->l('Beyond which, the Customer needs to start again by entering user id and password from the begining'),
									),
								),
							'submit' => array(
											'title' => $this->l('Save'),
										),
							)
						);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitLBMOTPSMSForm';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
		.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
								'fields_value' => $this->getRenderOTPSMSFormValues(),
								'languages' => $this->context->controller->getLanguages(),
								'id_language' => $this->context->language->id,
							);

		return $helper->generateForm(array(
		$fields_form));
	}

	public function setCountrymblForm()
	{
		$countrylists = $this->getcountrylists();
		$mobile_size_max = Configuration::get('LOGINBYMOBILE_MBLENGTH');
		$mobile_size_min = Configuration::get('LOGINBYMOBILE_MBLENGTH_MIN');
		$mobile_size_max_list = unserialize($mobile_size_max);
		$mobile_size_min_list = unserialize($mobile_size_min);
		$LOGINBYMOBILE_TXN = Configuration::get('LOGINBYMOBILE_TXN');
		$LOGINBYMOBILE_OTP = Configuration::get('LOGINBYMOBILE_OTP');
		$LOGINBYMOBILE_TXN_list = unserialize($LOGINBYMOBILE_TXN);
		$LOGINBYMOBILE_OTP_list = unserialize($LOGINBYMOBILE_OTP);

		foreach ($countrylists as $key => $type) {
			$countrylists[$key]['label'] = $type['name'];
			$countrylists[$key]['id'] = $type['id'];
			if (!isset($mobile_size_max_list[$type['id']])) {
				$mobile_size_max_list[$type['id']] = 0;
			}
			if (!isset($mobile_size_min_list[$type['id']])) {
				$mobile_size_min_list[$type['id']] = 0;
			}
			if (!isset($LOGINBYMOBILE_TXN_list[$type['id']]) || empty($LOGINBYMOBILE_TXN_list[$type['id']])) {
				$LOGINBYMOBILE_TXN_list[$type['id']] = 0;
			}
			if (!isset($LOGINBYMOBILE_OTP_list[$type['id']]) || empty($LOGINBYMOBILE_OTP_list[$type['id']])) {
				$LOGINBYMOBILE_OTP_list[$type['id']] = 0;
			}
		}
		$this->context->smarty->assign(
			array(
				'action' => AdminController::$currentIndex.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'countrylists' => $countrylists,
				'LOGINBYMOBILE_ENABLE_LOGIN_FEATURE' => Configuration::get('LOGINBYMOBILE_ENABLE_LOGIN_FEATURE'),
				'LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN' => Configuration::get('LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN'),
				'LOGINBYMOBILE_MOBILE_REG' => Configuration::get('LOGINBYMOBILE_MOBILE_REG'),
				'mobilenum_length_list' => $mobile_size_max_list,
				'mobile_size_min_list' => $mobile_size_min_list,
				'LOGINBYMOBILE_TXN_list' => $LOGINBYMOBILE_TXN_list,
				'LOGINBYMOBILE_OTP_list' => $LOGINBYMOBILE_OTP_list,
			)
		);
		$this->context->smarty->assign('LOGINBYMOBILE_ACCEPTZERO', Configuration::get('LOGINBYMOBILE_ACCEPTZERO'));
		return $this->display(__FILE__, 'views/templates/hook/country_mobile_admin_options.tpl');
	}

	protected function getConfigFormValues()
	{
		return array(
			'LOGINBYMOBILE_DDB_ENABLE' => Configuration::get('LOGINBYMOBILE_DDB_ENABLE'),
			'LOGINBYMOBILE_SMS_PROVIDER' => Configuration::get('LOGINBYMOBILE_SMS_PROVIDER'),
			'LOGINBYMOBILE_SMS_UNICODE' => Configuration::get('LOGINBYMOBILE_SMS_UNICODE'),
			'LOGINBYMOBILE_ADD_PREFIX' => Configuration::get('LOGINBYMOBILE_ADD_PREFIX'),
		);
	}

	

	protected function getRenderGenericFormValues()
	{
		return array(
			'LOGINBYMOBILE_GENERIC_URL' => Configuration::get('LOGINBYMOBILE_GENERIC_URL'),
			'LOGINBYMOBILE_GENERIC_UNAME' => Configuration::get('LOGINBYMOBILE_GENERIC_UNAME'),
			'LOGINBYMOBILE_GENERIC_PWD' => Configuration::get('LOGINBYMOBILE_GENERIC_PWD'),
			'LOGINBYMOBILE_GENERIC_SIMULATE' => Configuration::get('LOGINBYMOBILE_GENERIC_SIMULATE'),
			'LOGINBYMOBILE_GENERIC_FROM' => Configuration::get('LOGINBYMOBILE_GENERIC_FROM'),
			'LOGINBYMOBILE_GENERIC_UNAME_TXT' => Configuration::get('LOGINBYMOBILE_GENERIC_UNAME_TXT'),
			'LOGINBYMOBILE_GENERIC_PWD_TXT' => Configuration::get('LOGINBYMOBILE_GENERIC_PWD_TXT'),
			'LOGINBYMOBILE_GENERIC_FROM_TXT' => Configuration::get('LOGINBYMOBILE_GENERIC_FROM_TXT'),
			'LOGINBYMOBILE_GENERIC_TO_TXT' => Configuration::get('LOGINBYMOBILE_GENERIC_TO_TXT'),
			'LOGINBYMOBILE_GENERIC_MSG_TXT' => Configuration::get('LOGINBYMOBILE_GENERIC_MSG_TXT'),
			'LOGINBYMOBILE_GENERIC_UNI_TXT' => Configuration::get('LOGINBYMOBILE_GENERIC_UNI_TXT'),			
		);
	}	

	/* Provider Change - Start */

	protected function getRenderMsgbucketinFormValues()
	{
		return array(
			
			'LOGINBYMOBILE_MSGBUCKETIN_KEY' => Configuration::get('LOGINBYMOBILE_MSGBUCKETIN_KEY'),
			
		);
	}
	
	protected function getRenderMsgbucketusFormValues()
	{
		return array(
			
			'LOGINBYMOBILE_MSGBUCKETUS_KEY' => Configuration::get('LOGINBYMOBILE_MSGBUCKETUS_KEY'),
			
		);
	}
	
	/* Provider Change - End */


	protected function getRenderEmailFormValues()
	{
		return array(
					'LOGINBYMOBILE_EMAIL_REQUIRED' => Configuration::get('LOGINBYMOBILE_EMAIL_REQUIRED'),
					'LOGINBYMOBILE_DISP_MAIL_AC_PAGE' => Configuration::get('LOGINBYMOBILE_DISP_MAIL_AC_PAGE'),
					'LOGINBYMOBILE_MAIL_DOMAIN' => Configuration::get('LOGINBYMOBILE_MAIL_DOMAIN'),
				);
	}

	protected function getRenderOTPSMSFormValues()
	{
		return array(
			'LOGINBYMOBILE_WELCOMESMS_ENABLE' => Configuration::get('LOGINBYMOBILE_WELCOMESMS_ENABLE'),
			'LOGINBYMOBILE_SMS_TEXT' => Configuration::getInt('LOGINBYMOBILE_SMS_TEXT'),
			'LOGINBYMOBILE_LOG' => Configuration::get('LOGINBYMOBILE_LOG'),
			'LOGINBYMOBILE_CODE_LENGTH' => Configuration::get('LOGINBYMOBILE_CODE_LENGTH'),
			'LOGINBYMOBILE_SMS_MAX_COUNT' => Configuration::get('LOGINBYMOBILE_SMS_MAX_COUNT'),
			'LOGINBYMOBILE_OTP_TIMEINTERVAL' => Configuration::get('LOGINBYMOBILE_OTP_TIMEINTERVAL'),			
			'LOGINBYMOBILE_WELCOMESMS_TEXT' => Configuration::getInt('LOGINBYMOBILE_WELCOMESMS_TEXT'),
			'LOGINBYMOBILE_PASSWORD_TEXT' => Configuration::getInt('LOGINBYMOBILE_PASSWORD_TEXT'),
			'LOGINBYMOBILE_TWOWAYFACTOR' => Configuration::get('LOGINBYMOBILE_TWOWAYFACTOR'),
			'LOGINBYMOBILE_TWOWAYFACTOR_TEXT' => Configuration::getInt('LOGINBYMOBILE_TWOWAYFACTOR_TEXT'),
			'LOGINBYMOBILE_ATTEMPTS' => Configuration::get('LOGINBYMOBILE_ATTEMPTS'),
			'LOGINBYMOBILE_SESSION_TIME' => Configuration::get('LOGINBYMOBILE_SESSION_TIME'),
		);
	}

	protected function getRenderSMSNotificationFormValues()
	{
		return array(
			'LOGINBYMOBILE_NNEWORDERA_ENABLE' => Configuration::get('LOGINBYMOBILE_NNEWORDERA_ENABLE'),
			'LOGINBYMOBILE_NNEWORDERA' => Configuration::getInt('LOGINBYMOBILE_NNEWORDERA'),

			'LBM_SELLER_NEWORDER_ENABLE' => Configuration::get('LBM_SELLER_NEWORDER_ENABLE'),
			'LBM_SELLER_NEWORDER_TEXT' => Configuration::getInt('LBM_SELLER_NEWORDER_TEXT'),
			'LBM_SELLER_LOWSTOCK_ENABLE' => Configuration::get('LBM_SELLER_LOWSTOCK_ENABLE'),
			'LBM_SELLER_LOWSTOCK_TEXT' => Configuration::getInt('LBM_SELLER_LOWSTOCK_TEXT'),			
			
			'LBM_FIRSTORDER_SMS_ENABLE' => Configuration::get('LBM_FIRSTORDER_SMS_ENABLE'),
			'LBM_FIRSTORDER_CRON_ENABLE' => Configuration::get('LBM_FIRSTORDER_CRON_ENABLE'),			
			'LBM_FIRSTORDER_SMS_TEXT' => Configuration::getInt('LBM_FIRSTORDER_SMS_TEXT'),
			
			'LOGINBYMOBILE_NNEWORDER_ENABLE' => Configuration::get('LOGINBYMOBILE_NNEWORDER_ENABLE'),
			'LOGINBYMOBILE_NNEWORDER' => Configuration::getInt('LOGINBYMOBILE_NNEWORDER'),
			'LOGINBYMOBILE_NTRACKING_ENABLE' => Configuration::get('LOGINBYMOBILE_NTRACKING_ENABLE'),
			'LOGINBYMOBILE_NTRACKING' => Configuration::getInt('LOGINBYMOBILE_NTRACKING'),
			'LOGINBYMOBILE_NDELIVERED_ENABLE' => Configuration::get('LOGINBYMOBILE_NDELIVERED_ENABLE'),
			'LOGINBYMOBILE_NDELIVERED' => Configuration::getInt('LOGINBYMOBILE_NDELIVERED'),
			'LOGINBYMOBILE_NSHIPPED_ENABLE' => Configuration::get('LOGINBYMOBILE_NSHIPPED_ENABLE'),
			'LOGINBYMOBILE_NSHIPPED' => Configuration::getInt('LOGINBYMOBILE_NSHIPPED'),
			'LOGINBYMOBILE_NCANCELED_ENABLE' => Configuration::get('LOGINBYMOBILE_NCANCELED_ENABLE'),
			'LOGINBYMOBILE_NCANCELED' => Configuration::getInt('LOGINBYMOBILE_NCANCELED'),
			'LOGINBYMOBILE_ADMINPHONE' => Configuration::get('LOGINBYMOBILE_ADMINPHONE'),
			'LOGINBYMOBILE_ADMINCOUNTRY' => Configuration::get('LOGINBYMOBILE_ADMINCOUNTRY'),
			'LOGINBYMOBILE_STATUS_CANCELLED' => Configuration::get('LOGINBYMOBILE_STATUS_CANCELLED'),
			'LOGINBYMOBILE_STATUS_DELIVERED' => Configuration::get('LOGINBYMOBILE_STATUS_DELIVERED'),
			'LOGINBYMOBILE_STATUS_SHIPPED' => Configuration::get('LOGINBYMOBILE_STATUS_SHIPPED'),
			'LOGINBYMOBILE_STATUS_ORDER' => Configuration::get('LOGINBYMOBILE_STATUS_ORDER'),
			'LOGINBYMOBILE_STATUS_PC' => Configuration::get('LOGINBYMOBILE_STATUS_PC'),
		);
	}

/********************* Renderforms - End ***********************/

/********************* Utilities - Start ***********************/

	public function getCountriesForRegField() {
		$countries = array();
		$result = null;
		if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT c.`id_country`, cl.`name`, zz.`name` AS zone
				FROM `'._DB_PREFIX_.'country` c'.
				Shop::addSqlAssociation('country', 'c').'
				LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
				INNER JOIN (`'._DB_PREFIX_.'carrier_zone` cz INNER JOIN `'._DB_PREFIX_.'carrier` cr ON ( cr.id_carrier = cz.id_carrier AND cr.deleted = 0 )
				LEFT JOIN `'._DB_PREFIX_.'zone` zz ON cz.id_zone = zz.id_zone) ON zz.`id_zone` = c.`id_zone`
				WHERE 1 AND c.active = 1  ORDER BY cl.name ASC');
			foreach ($result as $country) {
				$countries[$country['id_country']] = $country['name'];
			}
		} else {
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT c.`id_country`, cl.`name` FROM `'._DB_PREFIX_.'country` c '.Shop::addSqlAssociation('country', 'c').'
			LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = c.`id_zone`)
			WHERE 1 AND c.active = 1 ORDER BY cl.name ASC');
			foreach ($result as $country) {
				$countries[$country['id_country']] = $country['name'];
			}
		}
		return $countries;
	}



    public function getOrderStatus()
    {
        $statuses_array = array();
        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $statuses_array[] = array(
                'id' => $status['id_order_state'],
                'name' => $status['name'].'('.$status['id_order_state'].')'
            );
        }
        return $statuses_array;
    }


/* Provider Change - Start */
/* 
Msgbucketin, MsgBbcketus,
*/
    public function getSMSProvider()
    {
        $sms_provider = array();
        $sms_provider[] = array(
            'id' => 'msgbucketin',
            'name' => 'Msgbucket India'
        );
		
		$sms_provider[] = array(
            'id' => 'msgbucketus',
            'name' => 'Msgbucket International'
        );

		
        return array(
                    array(
                        'name' => ('Select Whatsapp Provider'),
                        'query' => $sms_provider
                    ),
                );
    }

    public function getHooksInRegistrationForm()
    {
        $regHooks = array();
        $regHooks[] = array(
        'id' => 'displayCustomerAccountForm',
        'name' => 'Bottom of Registration Form'
        );
        $regHooks[] = array(
        'id' => 'displayCustomerAccountFormTop',
        'name' => 'Top of Registration Form'
        );
        return array(
            array(
                'name' => ('Select Position of Mobile/OTP fields in Registration Form'),
                'query' => $regHooks
            ),
        );
    }
/* Provider Change - End */

    public function getVectramindLang()
    {
        $vectramind_lang = array();
        $vectramind_lang[] = array(
            'id' => 0,
            'name' => 'English'
        );
        $vectramind_lang[] = array(
            'id' => 8,
            'name' => 'Arabic'
        );
        return array(
                    array(
                        'name' => ('Select Message Language'),
                        'query' => $vectramind_lang
                    ),
                );
    }

    public function upgradeLBMModuleForm()
    {
        $this->context->smarty->assign('action', AdminController::$currentIndex.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        return $this->display(__FILE__, 'upgradelbmmodule.tpl');
    }

    public function upgradeLBMModule()
    {
		   /* require_once(_PS_MODULE_DIR_.$this->name.'/upgrade/upgrade-17.0.0.php');
			if (function_exists('upgrade_module_6_1_3')) {
				$upgradeStatus = upgrade_module_6_1_3($this);
			}
			return $upgradeStatus; */
		$this->registerHook('actionObjectCustomerDeleteAfter');
		$this->tabsArray = array('AdminloginMobileLog' => 'Manage Customer Mobile', 'AdminMobileByCustomer' => 'Manage Mobile Numbers',);
		$this->parentTabClass = 'AdminloginMobileLog';
		$this->uninstallTabs();
		$this->parentTabClass = 'AdminMobileByCustomer';
		$this->uninstallTabs();
		$this->tabsArray = array('AdminMobileByCustomer' => 'Manage Mobile Numbers',);
		$this->parentTabClass = 'AdminMobileByCustomer';
		$this->installTabs();
		return true;
    }

    public function getcountrylists()
    {
            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                $getcountries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
            } else {
                $getcountries = Country::getCountries($this->context->language->id, true);
            }

        $countries = array();
        foreach ($getcountries as $country) {
            $countries[] = array('id' => (int)$country['id_country'], 'name' => (string)$country['name']);
        }
        return $countries;
    }
		
    private function addLbmFirstOrderDelivery($params)
    {
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'lbmorderdelivery` (
			`id_customer`, 
			`id_order`, 
			`phone_number`, 
			`id_country`, 
			`transactional`, 
			`unicode`, 
			`delivered_on`, 
			`email`,
			`message`, 
			`active`) 
			VALUES ('.
			(int)$params['id_customer']
			.','
			.(int)$params['id_order']
			.',\''
			.pSQL($params['phone_number'])
			.'\','
			.(int)$params['id_country']
			.','
			.(int)$params['transactional']
			.','
			.(int)$params['unicode']
			.',\''
			.pSQL($params['delivered_on'])
			.'\',\''
			.pSQL($params['email'])
			.'\',\''
			.pSQL($params['message'])
			.'\','
			.(int)$params['active'].')');
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

    protected function assignCountries($id_country)
    {

		//print_r($id_country);
		//die();
		$countries = null;
		if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
			$countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
		} else {
			$countries = Country::getCountries($this->context->language->id, true);
		}
		foreach ($countries as $country) {
			if ($country['id_country'] == $id_country) {
				$this->context->smarty->assign(array('call_prefix' => '+'.$country['call_prefix'],));
				break;
			}
		}

		$this->context->smarty->assign(array(
			'countries' => $countries,
			'sl_country' => (int)$id_country,
		));
    }

    public function mobileNumExists($otp_mobile_num, $return_id = false)
    {
        $result = Mobilenumlist::getIdByMobileNum($otp_mobile_num);
        return ($return_id ? (int)$result : (bool)$result);
    }

	public function validateExistingMobileCountryOTP($input_mobile_number, $input_id_country, $input_otp, $errors = array())
    {
		$verified = 0;
        $mobile_max_size_by_country = Configuration::get('LOGINBYMOBILE_MBLENGTH');
        $mobile_min_size_by_country = Configuration::get('LOGINBYMOBILE_MBLENGTH_MIN');
        $otp_by_country = Configuration::get('LOGINBYMOBILE_OTP');
		
        $mobile_max_size_by_country_list = unserialize($mobile_max_size_by_country);
        if (!empty($mobile_max_size_by_country_list[$input_id_country])) {
            if (Tools::strlen($input_mobile_number) > $mobile_max_size_by_country_list[$input_id_country]) {
                $errors['lbm_ca_mobile_number'] = $this->l('Max length allowed for mobile number is ').$mobile_max_size_by_country_list[$input_id_country];
            }
        }
		
        $mobile_min_size_by_country_list = unserialize($mobile_min_size_by_country);
        if (!empty($mobile_min_size_by_country_list[$input_id_country])) {
            if (Tools::strlen($input_mobile_number) < $mobile_min_size_by_country_list[$input_id_country]) {
                $errors['lbm_ca_mobile_number'] = $this->l('Min length allowed for mobile number is ').$mobile_min_size_by_country_list[$input_id_country];
            }
        }
		
        $otp_by_country_list = unserialize($otp_by_country);
		if (empty($input_mobile_number)) {
			$errors['lbm_ca_mobile_number'] = $this->l('Enter Mobile Number.');
		} else if (!Validate::isPhoneNumber($input_mobile_number)) {
			$errors['lbm_ca_mobile_number'] = $this->l('Invalid Mobile Number.');
			// TO DO :: mobileNumExists should take country id as well
		} else {
			if (!empty($otp_by_country_list[$input_id_country])) {
				if (empty($input_otp)) {
					$errors['lbm_ca_otp'] = $this->l('Kindly verify your Mobile Number. request OTP and Verify.');
				} else {
					$session_key = trim(Tools::getValue('session_key_reg'));
					if (empty($session_key)) {
						$errors['lbm_ca_otp'] = $this->l('Your session details are missing. Kindly request new OTP and verify');
					} else {
						$sessionRow = Lbmsession::getLbmSession($session_key);
						if (isset($sessionRow) && is_array($sessionRow) && !empty($sessionRow)) {
							$last_session_gen = $sessionRow['last_session_gen'];
							if ((strtotime($last_session_gen.'+'.($min_time = (int)Configuration::get('LOGINBYMOBILE_SESSION_TIME')).' minutes') - time()) < 0) {
								$errors['lbm_ca_otp'] = sprintf($this->l('Session expired. Please try again. Due to security reasons, you need to complete login process within %d minutes'), (int)$min_time);
								$lbmpageid = 'root';
								//redirect
							} else {
								$session_mobile_number = $sessionRow['mobile_num'];
								if (Validate::isPhoneNumber($session_mobile_number)) {
									$session_id_country = $sessionRow['id_country'];
									$session_otp = $sessionRow['twowayfactor'];
									if ($session_otp == $input_otp
										&& $session_mobile_number == $input_mobile_number
										&& $session_id_country == $input_id_country) {
										$params = array();
										$params['session_key'] = $session_key;
										$params['active'] = 1;
										Lbmsession::updateLbmSessionFlag($params);
										$verified = 1;
										//set session as verified
									} else {
										$session_attempt = (int)$sessionRow['attempts'];
										$allowed_attempts = (int)Configuration::get('LOGINBYMOBILE_ATTEMPTS');
										if ($session_attempt > $allowed_attempts) {
											$errors['lbm_ca_otp'] = $this->l('You have exceeded maximum allowed attempts. Kindly try again');
											$lbmpageid = 'root';
											Lbmsession::clearLbmSession($session_key);
											//get him out. retry
										} else {
											if ($session_otp != $input_otp) {
												$errors['lbm_ca_otp'] = $this->l('Incorrect Key. Kindly verify your Message correctly. You have ').($allowed_attempts - $session_attempt).' more attempts';
											}
											if ($session_mobile_number != $input_mobile_number) {
												$errors['lbm_ca_mobile_number'] = $this->l('Did you change mobile number? Kindly request new OTP and verify again. You have ').($allowed_attempts - $session_attempt).' more attempts';
											}
											if ($session_id_country != $input_id_country) {
												$errors['lbm_ca_id_country'] = $this->l('Did you change Country? Kindly request new OTP and verify again. You have ').($allowed_attempts - $session_attempt).' more attempts';
											}

											$lbmpageid = 'ca';
											$params = array();
											$params['session_key'] = $session_key;
											$params['attempts'] = $sessionRow['attempts'] + 1;
											Lbmsession::updateLbmSessionAttempts($params);
											//give him a chance
										}
									}
								} else {
									$errors['lbm_ca_mobile_number'] = $this->l('Technical issue. Session corrupted. try once again.');
									$lbmpageid = 'root';
								}						
							}
						} else {
							$errors['lbm_ca_otp'] = $this->l('Technical issue. Session duplication. For security reason, kindly try again.');
							$lbmpageid = 'root';						
						}
					}
				}				
			}			
        }
        $error_result = array(
			'hasError' => !empty($errors),
			'errors' => $errors,
			'verified' => $verified,
        );
        return $error_result;
	}	
	
    public function validateNewMobileCountryOTP($input_mobile_number, $input_id_country, $input_otp, $errors = array())
    {
		$verified = 0;
        $mobile_max_size_by_country = Configuration::get('LOGINBYMOBILE_MBLENGTH');
        $mobile_min_size_by_country = Configuration::get('LOGINBYMOBILE_MBLENGTH_MIN');
        $otp_by_country = Configuration::get('LOGINBYMOBILE_OTP');
		
        $mobile_max_size_by_country_list = unserialize($mobile_max_size_by_country);
        if (!empty($mobile_max_size_by_country_list[$input_id_country])) {
            if (Tools::strlen($input_mobile_number) > $mobile_max_size_by_country_list[$input_id_country]) {
                $errors['lbm_ca_mobile_number'] = $this->l('Max length allowed for mobile number is ').$mobile_max_size_by_country_list[$input_id_country];
            }
        }
		
        $mobile_min_size_by_country_list = unserialize($mobile_min_size_by_country);
        if (!empty($mobile_min_size_by_country_list[$input_id_country])) {
            if (Tools::strlen($input_mobile_number) < $mobile_min_size_by_country_list[$input_id_country]) {
                $errors['lbm_ca_mobile_number'] = $this->l('Min length allowed for mobile number is ').$mobile_min_size_by_country_list[$input_id_country];
            }
        }
		
        $otp_by_country_list = unserialize($otp_by_country);
		if (empty($input_mobile_number)) {
			$errors['lbm_ca_mobile_number'] = $this->l('Enter Mobile Number.');
		} else if (!Validate::isPhoneNumber($input_mobile_number)) {
			$errors['lbm_ca_mobile_number'] = $this->l('Invalid Mobile Number.');
			// TO DO :: mobileNumExists should take country id as well
		} else if ($this->mobileNumExists($input_mobile_number)) {
			$errors['lbm_ca_mobile_number'] = $this->l('An account using this Mobile Number has already been registered. Please register a different Mobile Number (or) click forgot password. ');
		// Add aditional common validations as else if above this line	
		// Specific validations proceeds down		
		} else {
			if (!empty($otp_by_country_list[$input_id_country])) {
				if (empty($input_otp)) {
					$errors['lbm_ca_otp'] = $this->l('your activation code is required.');
				} else {
					$session_key = trim(Tools::getValue('session_key_reg'));
					if (empty($session_key)) {
						$errors['lbm_ca_otp'] = $this->l('Your session details are missing. Kindly request new OTP and verify');
					} else {
						$sessionRow = Lbmsession::getLbmSession($session_key);
						if (isset($sessionRow) && is_array($sessionRow) && !empty($sessionRow)) {
							$last_session_gen = $sessionRow['last_session_gen'];
							if ((strtotime($last_session_gen.'+'.($min_time = (int)Configuration::get('LOGINBYMOBILE_SESSION_TIME')).' minutes') - time()) < 0) {
								$errors['lbm_ca_otp'] = sprintf($this->l('Session expired. Please try again. Due to security reasons, you need to complete login process within %d minutes'), (int)$min_time);
								$lbmpageid = 'root';
								//redirect
							} else {
								$session_mobile_number = $sessionRow['mobile_num'];
								if (Validate::isPhoneNumber($session_mobile_number)) {
									$session_id_country = $sessionRow['id_country'];
									$session_otp = $sessionRow['twowayfactor'];
									if ($session_otp == $input_otp
										&& $session_mobile_number == $input_mobile_number
										&& $session_id_country == $input_id_country) {
										$params = array();
										$params['session_key'] = $session_key;
										$params['active'] = 1;
										Lbmsession::updateLbmSessionFlag($params);
										$verified = 1;
										//set session as verified
									} else {
										$session_attempt = (int)$sessionRow['attempts'];
										$allowed_attempts = (int)Configuration::get('LOGINBYMOBILE_ATTEMPTS');
										if ($session_attempt > $allowed_attempts) {
											$errors['lbm_ca_otp'] = $this->l('You have exceeded maximum allowed attempts. Kindly try again');
											$lbmpageid = 'root';
											Lbmsession::clearLbmSession($session_key);
											//get him out. retry
										} else {
											if ($session_otp != $input_otp) {
												$errors['lbm_ca_otp'] = $this->l('Incorrect Key. Kindly verify your Message correctly. You have ').($allowed_attempts - $session_attempt).' more attempts';
											}
											if ($session_mobile_number != $input_mobile_number) {
												$errors['lbm_ca_mobile_number'] = $this->l('Did you change mobile number? Kindly request new OTP and verify again. You have ').($allowed_attempts - $session_attempt).' more attempts';
											}
											if ($session_id_country != $input_id_country) {
												$errors['lbm_ca_id_country'] = $this->l('Did you change Country? Kindly request new OTP and verify again. You have ').($allowed_attempts - $session_attempt).' more attempts';
											}											

											$lbmpageid = 'ca';
											$params = array();
											$params['session_key'] = $session_key;
											$params['attempts'] = $sessionRow['attempts'] + 1;
											Lbmsession::updateLbmSessionAttempts($params);
											//give him a chance
										}
									}
								} else {
									$errors['lbm_ca_mobile_number'] = $this->l('Technical issue. Session corrupted. try once again.');
									$lbmpageid = 'root';
								}						
							}
						} else {
							$errors['lbm_ca_otp'] = $this->l('Technical issue. Session duplication. For security reason, kindly try again.');
							$lbmpageid = 'root';						
						}
					}
				}				
			}			
        }
        $error_result = array(
			'hasError' => !empty($errors),
			'errors' => $errors,
			'verified' => $verified,
        );
        return $error_result;
	}

    public function processAccountRegistration($email, $input_mobile_number, $input_id_country, $input_otp)
    {
        $errors = array();
        if ($email == 'temp_@loginbymobile.com') {
            $errors['email'] = $this->l('An account using this Email has already been registered. Please register a different Email');
        }
		$error_result = $this->validateNewMobileCountryOTP($input_mobile_number, $input_id_country, $input_otp, $errors);
        return $error_result;
    }

    public function processIdentityUpdate($input_id_country, $input_otp, $input_mobile_number)
    {
        $errors = array();
        $id_customer = $this->context->customer->id;		
        $onrecord_mobile_number = '';
        $onrecord_mobile_isverified = 0;
        $onrecord_id_country = '';
		$error_result = array(
			'hasError' => 0,
			'errors' => array(),
			'verified' => 1,
        );
        $id_onrecord = Mobilenumlist::getIdByCustomer($id_customer);
        if (!empty($id_onrecord)) {
            $onrecord_data = new Mobilenumlist($id_onrecord);
            $onrecord_mobile_number = $onrecord_data->otp_mobile_num;
            $onrecord_mobile_isverified = $onrecord_data->otp_flag;
            $onrecord_id_country = $onrecord_data->id_country;
        }

		if ($onrecord_mobile_number == $input_mobile_number && $onrecord_id_country == $input_id_country) {
			// No update to mobile number required. No action required
			if (!$onrecord_mobile_isverified) {
				$error_result = $this->validateExistingMobileCountryOTP($input_mobile_number, $input_id_country, $input_otp, $errors);
				if (!$error_result['hasError']) {
					if ($error_result['verified']) {
						$this->updateMobileNum($input_mobile_number, $id_customer, 1, $input_id_country);
					}
				}				
			}
		} else {
			$error_result = $this->validateNewMobileCountryOTP($input_mobile_number, $input_id_country, $input_otp, $errors);
			if (!$error_result['hasError']) {
				if ($error_result['verified']) {
					$this->updateMobileNum($input_mobile_number, $id_customer, 1, $input_id_country);
				} else {
					$this->updateMobileNum($input_mobile_number, $id_customer, 0, $input_id_country);
				}
			}			
		}
		return $error_result;
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
			$mobileNumList->id_lang = (int)Context::getContext()->language->id;
            $mobileNumList->id_customer = $id_customer;
            $mobileNumList->id_country = $country_id;
            $mobileNumList->save();
    }
/********************* Utilities - End ***********************/
}
