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

class AdminMobileByCustomerController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'customer';
        $this->className = 'Customer';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('view');
        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;
        $this->_use_found_rows = false;

        parent::__construct();

        $this->bulk_actions = array(
			'unlinkMobileNumbers' => array(
					'text' => $this->l('Unlink Mobile Numbers'),
					'icon' => 'icon-close text-success',
				),
		);


        $this->fields_list = array(
                                'id_customer' => array(
                                    'title' => $this->l('ID'),
                                    'align' => 'text-center',
									'remove_onclick' => true,
                                    'class' => 'fixed-width-xs'
                                ),
                                'firstname' => array(
									'remove_onclick' => true,
                                    'title' => $this->l('First name')
                                ),
                                'lastname' => array(
									'remove_onclick' => true,
                                    'title' => $this->l('Last name')
                                ),
                                'email' => array(
									'remove_onclick' => true,
                                    'title' => $this->l('Email address')
                                ),
                                'otp_mobile_num' => array(
									'remove_onclick' => true,
                                    'title' => $this->l('Mobile')
                                ),
                                'otp_flag' => array(
                                    'title' => $this->l('Verified'),
									'remove_onclick' => true,
                                    'active' => 'isMobileVerified',
                                    'type' => 'bool',
                                ),
                                'id_country' => array(
									'remove_onclick' => true,
                                    'title' => $this->l('Country ID')
                                ),
                                'country_name' => array(
									'remove_onclick' => true,
                                    'title' => $this->l('Country')
                                ),
                            );

        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'mobilenumlist` mobile ON (a.`id_customer` = mobile.`id_customer`) ';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'country_lang` contl ON (mobile.`id_country` = contl.`id_country` AND contl.`id_lang` = '.$this->context->language->id.') ';
        $this->_select = ' a.`firstname`, a.`lastname`, a.`email`, mobile.`id_country`, mobile.`otp_mobile_num`, mobile.`otp_flag`, contl.`name` as country_name ';
    }

    protected function processBulkUnlinkMobileNumbers()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
			$customerIdsString = implode(',', $this->boxes);
			if (!empty($customerIdsString)) {
				Mobilenumlist::deleteCustomerRecords($customerIdsString);
			}
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
        }
    }

    public function processIsMobileVerified()
    {
        require_once(_PS_MODULE_DIR_.'loginbymobile/classes/mobilenumlist.php');
        $id_mobilenumlist = Mobilenumlist::getIdByCustomer($this->id_object);
        if (empty($id_mobilenumlist) || $id_mobilenumlist == 0) {
            $this->errors[] = Tools::displayError('Update Customer Mobile Number First.');
        } else {
            $mobileNumlist = new Mobilenumlist($id_mobilenumlist);
            if (!Validate::isLoadedObject($mobileNumlist)) {
                $this->errors[] = Tools::displayError('An error occurred while updating database.');
            }
            $mobileNumlist->otp_flag = $mobileNumlist->otp_flag ? 0 : 1;
            if (!$mobileNumlist->update()) {
                $this->errors[] = Tools::displayError('An error occurred while updating database.');
            }
        }
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['lbm_settings'] = array(
                'href' => $this->context->link->getAdminLink('AdminModules', false)
                            .'&configure=loginbymobile'
                            .'&token='.Tools::getAdminTokenLite('AdminModules')
                            .'&module_name=loginbymobile',
                'desc' => $this->l('Settings :: Login By Mobile', null, null, false),
                'icon' => 'process-icon-configure'
            );
        parent::initPageHeaderToolbar();
    }

    public function displayEditLink($token = null, $id_customer = 0)
    {
		//http://localhost/1744/admin481oj3cm0/index.php?controller=AdminMobileByCustomer&id_customer=1&updatecustomer&token=bb7a78f3104fa2ba280e0a5b786f0f9f
        $link =$this->context->link->getAdminLink('AdminMobileByCustomer', true).'&id_customer='.(int)$id_customer.'&updatecustomer&new=1';
        $href = $link;
        $action = 'Update Mobile';
        $icon = 'icon-cogs';
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_.'loginbymobile/views/templates/admin/_configure/helpers/list/list_action_generic.tpl', $this->context->smarty);

        $tpl->assign(array(
            'href' => $href,
            'action' => $action,
            'icon' => $icon,
            'target' => '_self',
        ));

        return $tpl->fetch();
    }

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();
        if ($this->display == 'edit') {
            if (($customer = $this->loadObject(true)) && Validate::isLoadedObject($customer)) {
                $this->toolbar_title[] = sprintf($this->l('Editing Customer: %s'), Tools::substr($customer->firstname, 0, 1).'. '.$customer->lastname);
            } else {
                $this->toolbar_title[] = $this->l('Error Fetching Customer');
            }
            if (count($this->toolbar_title) > 0 && version_compare(_PS_VERSION_, '1.6.0.9', '>')) {
                $this->addMetaTitle($this->toolbar_title[count($this->toolbar_title) - 1]);
            }
        }
    }

    public function renderForm()
    {
        if ($this->display == 'edit') {
            if (($customer = $this->loadObject(true)) && Validate::isLoadedObject($customer)) {
                $helper = new HelperForm();
                $helper->tpl_vars = array(
                    'id_language' => $this->context->language->id,
                );
                $helper->languages = $this->context->controller->getLanguages();
                $helper->table = 'mobilenumlist';
                $helper->fields_value = $this->getConfigFormValues($customer->id);
                $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
                $helper->currentIndex = $this->context->link->getAdminLink('AdminMobileByCustomer', false);
                $helper->token = Tools::getAdminTokenLite('AdminMobileByCustomer');
                return $helper->generateForm(array($this->getConfigForm()));
            }
        }
    }

    public function getConfigForm()
    {
        return array(
                    'form' => array(
                        'legend' => array(
                                        'title' => $this->l('Update Registered Mobile for Customer'),
                                        'icon' => 'icon-cogs',
                                    ),
                        'input' => array(
                                        array(
                                            'type' => 'hidden',
                                            'label' => $this->l('Customer Id'),
                                            'name' => 'lbm_id_customer',
                                        ),
                                        array(
                                            'type' => 'select',
                                            'label' => $this->l('Assign Country'),
                                            'name' => 'lbm_id_country',
                                            'required' => true,
                                            'col' => '5',
                                            'class' => 'fixed-width-md',
                                            'options' => array(
                                                'query' => Country::getCountries((int)Context::getContext()->cookie->id_lang),
                                                'id' => 'id_country',
                                                'name' => 'name'
                                            ),
                                            'hint' => $this->l('Assign Country to this Customer')
                                        ),
                                        array(
                                            'col' => 3,
                                            'type' => 'text',
                                            'desc' => $this->l('Unique Mobile Number'),
                                            'name' => 'lbm_mobile_number',
                                            'label' => $this->l('Mobile Number'),
                                        ),
                                        array(
                                            'type' => 'switch',
                                            'label' => $this->l('Mobile Number Verified?.'),
                                            'name' => 'lbm_mobile_verified',
                                            'desc' => $this->l('(Is mobile number verified?)'),
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
                        'buttons' => array(
                                        array(
                                            'href' => $this->context->link->getAdminLink('AdminMobileByCustomer'),
                                            'title' => $this->l('Cancel'),
                                            'icon' => 'process-icon-back'
                                        ),
                                    ),
                    ),
                );
    }

    public function getConfigFormValues($id_customer)
    {
        require_once(_PS_MODULE_DIR_.'loginbymobile/classes/mobilenumlist.php');
        $id_mobilenumlist = Mobilenumlist::getIdByCustomer($id_customer);

        if (!empty($id_mobilenumlist) && $id_mobilenumlist != 0) {
            $mobileNumlist = new Mobilenumlist($id_mobilenumlist);
            if (Validate::isLoadedObject($mobileNumlist)) {
                return array(
                        'lbm_id_customer' => $id_customer,
                        'lbm_id_country' => $mobileNumlist->id_country,
                        'lbm_mobile_number' => $mobileNumlist->otp_mobile_num,
                        'lbm_mobile_verified' => $mobileNumlist->otp_flag,
                        );
            }
        }
        return array(
                'lbm_id_customer' => $id_customer,
                'lbm_id_country' => (int)Context::getContext()->country->id,
                'lbm_mobile_number' => '',
                'lbm_mobile_verified' => 0,
                );
    }
    public function postProcess()
    {
        if (Tools::getIsset('isMobileVerified'.$this->table)) {
            $this->processIsMobileVerified();
        } else if (Tools::isSubmit('submitAddmobilenumlist')) {
            require_once(_PS_MODULE_DIR_.'loginbymobile/classes/mobilenumlist.php');
            $id_customer = Tools::getValue('lbm_id_customer');
            $id_country = Tools::getValue('lbm_id_country');
            $mobile_number = Tools::getValue('lbm_mobile_number');
            $mobile_verified = Tools::getValue('lbm_mobile_verified');
            $id_mobilenumlist = Mobilenumlist::getIdByCustomer($id_customer);

            if (!empty($id_mobilenumlist) && $id_mobilenumlist != 0) {
                $temp_id_mobilenumlist = Mobilenumlist::getIdByMobileNum($mobile_number);
                if (empty($temp_id_mobilenumlist) || $temp_id_mobilenumlist == 0 || $id_mobilenumlist == $temp_id_mobilenumlist) {
                    $mobileNumlist = new Mobilenumlist($id_mobilenumlist);
                    $mobileNumlist->id_customer = $id_customer;
                    $mobileNumlist->id_country = $id_country;
                    $mobileNumlist->otp_mobile_num = $mobile_number;
					$mobileNumlist->id_lang = (int)Context::getContext()->language->id;
                    $mobileNumlist->otp_flag = $mobile_verified;
                    $mobileNumlist->save();
                    $this->redirect_after = $this->context->link->getAdminLink('AdminMobileByCustomer');
                } else {
                    $this->errors[] = Tools::displayError('Mobile Number already Registered by a different Customer.');
                }
            } else {
                $temp_id_mobilenumlist = Mobilenumlist::getIdByMobileNum($mobile_number);
                if (empty($temp_id_mobilenumlist) || $temp_id_mobilenumlist == 0) {
                    $mobileNumlist = new Mobilenumlist();
                    $mobileNumlist->id_customer = $id_customer;
                    $mobileNumlist->id_country = $id_country;
                    $mobileNumlist->otp_mobile_num = $mobile_number;
                    $mobileNumlist->otp_flag = $mobile_verified;
					$mobileNumlist->id_lang = (int)$this->context->language->id;

                    $mobileNumlist->add();
                    $this->redirect_after = $this->context->link->getAdminLink('AdminMobileByCustomer');
                } else {
                    $this->errors[] = Tools::displayError('Mobile Number already Registered by a different Customer.');
                }
            }
        } else if (Tools::getIsset('deletecustomer')) {
            require_once(_PS_MODULE_DIR_.'loginbymobile/classes/mobilenumlist.php');
            $id_customer = Tools::getValue('id_customer');
            $id_mobilenumlist = Mobilenumlist::getIdByCustomer($id_customer);
            if (!empty($id_mobilenumlist) && $id_mobilenumlist != 0) {
                $mobileNumlist = new Mobilenumlist($id_mobilenumlist);
                $mobileNumlist->delete();
            }
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
        } else if (Tools::getIsset('viewcustomer')) {
            $id_customer = Tools::getValue('id_customer');
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminCustomers').'&id_customer='.$id_customer.'&viewcustomer');
        }
        parent::postProcess();
    }

    public function renderList()
    {
        unset($this->toolbar_btn['new']);
        return parent::renderList();
    }

}
