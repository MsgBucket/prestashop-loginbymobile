<?php
/**
 * Please do not edit or add any code in this file without the permission of MsgBucket
 *
 * @author    MsgBucket
 * @copyright MsgBucket
 * @license   http://www.MsgBucket.com
 * Prestashop version 1.6+
 * trackdelhivery 3.0.9
 * June 2017
 */

require_once(_PS_MODULE_DIR_.'loginbymobile/classes/lbmsmsprovider.php');
class AdminLbmSmsProviderController extends ModuleAdminController
{
    protected $position_identifier = 'id_delhiverypickupaddress';
    public function __construct()
    {
        $this->explicitSelect = false;
        $this->context = Context::getContext();
        $this->id_lang = $this->context->language->id;
        $this->lang = false;
        $this->ajax = 1;
        $this->path = _MODULE_DIR_.'loginbymobile';
        $this->default_form_language = $this->context->language->id;
        $this->table = 'lbmsmsprovider';
        $this->className = 'Lbmsmsprovider';
        $this->identifier = 'id_lbmsmsprovider';
        $this->allow_export = true;
        $this->bootstrap = true;
        parent::__construct();
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Bulk Delete'),
				'icon' => 'icon-power-off text-success',
			),
		);
        $this->fields_list = array(
            'id_lbmsmsprovider' => array('title' => $this->l('Id'),'class' => 'fixed-width-sm'),
            'alias' => array('title' => $this->l('Message Provider'),'class' => 'fixed-width-sm'),
            'processing_type' => array('title' => $this->l('Processing Type'),'class' => 'fixed-width-sm'),
            'live' => array('title' => $this->l('Live'),'class' => 'fixed-width-sm', 'active' => 'status', 'type' => 'bool'),
        );
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->l('Manage Message Providers');
        $this->page_header_toolbar_btn['lbm_settings'] = array(
            'href' => $this->context->link->getAdminLink('AdminModules', false).'&configure=loginbymobile'
                  .'&token='.Tools::getAdminTokenLite('AdminModules')
                  .'&module_name=loginbymobile',
            'desc' => $this->l('Settings :: Login By Mobile', null, null, false),
            'icon' => 'process-icon-configure'
        );		
        parent::initPageHeaderToolbar();
    }

    public function getTitleHtml($title)
    {
		$this->context->smarty->assign('title', $title);
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_.'loginbymobile/views/templates/admin/title.tpl', $this->context->smarty);
        return $tpl->fetch();
    }
	
    public function renderForm()
    {
        $this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Message Provider'),
				'icon' => 'icon-cogs'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Message Provider Name'),
					'name' => 'alias',
					'required' => true,
					'hint' => $this->l('Message Provider Name'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Send Message URL'),
					'name' => 'url',
				),				
				array(
					'type' => 'select',
					'label' => $this->l('Processing Method'),
					'name' => 'processing_type',
					'id' => 'processing_type',
					'options' => array(
						'optiongroup' => array(
							'query' => $this->getSMSProcessingType(),
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
					'label' => $this->l('Live?'),
					'name' => 'live',
					'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Available')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('Already Used')
									)
								),
				),				
                array(
                    'type' => 'html',
                    'name' => 'smstitleparam',
                    'html_content' => $this->getTitleHtml('Message Provider Parameter Names'),
                ),
				array(
					'type' => 'text',
					'label' => $this->l('User Name Key'),
					'name' => 'user_name_key',
					'hint' => $this->l('Enter KEY name. Check send Message URL'),
				),
				array(
				'type' => 'text',
				'label' => $this->l('Password Key'),
				'name' => 'password_key',
				),
				array(
				'type' => 'text',
				'label' => $this->l('Sender ID Key'),
				'name' => 'sender_id_key',
				),
				array(
				'type' => 'text',
				'label' => $this->l('Destination Mobile / To Key'),
				'name' => 'to_key',
				),
				array(
				'type' => 'text',
				'label' => $this->l('Message Key'),
				'name' => 'message_key',
				),
				array(
				'type' => 'text',
				'label' => $this->l('Unicode Key'),
				'name' => 'unicode_key',
				),
				array(
				'type' => 'text',
				'label' => $this->l('Flash Key'),
				'name' => 'flash_key',
				),
				array(
				'type' => 'text',
				'label' => $this->l('Transactional Key'),
				'name' => 'transactional_key',
				),
				array(
				'type' => 'text',
				'label' => $this->l('1nd Additional Parameter Key'),
				'name' => 'addon_one_key',
				),

				array(
				'type' => 'text',
				'label' => $this->l('2nd Additional Parameter Key'),
				'name' => 'addon_two_key',
				),
                array(
                    'type' => 'html',
                    'name' => 'smstitle',
                    'html_content' => $this->getTitleHtml('Message Provider Parameter Values'),
                ),				
				array(
					'type' => 'text',
					'label' => $this->l('User Name'),
					'name' => 'user_name',
					'hint' => $this->l('Enter Value'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Password'),
					'name' => 'password',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Sender ID'),
					'name' => 'sender_id',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Unicode'),
					'name' => 'unicode',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Flash'),
					'name' => 'flash',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Transactional'),
					'name' => 'transactional',
				),
				array(
					'type' => 'text',
					'label' => $this->l('1st Additional Parameter Value'),
					'name' => 'addon_one',
				),
				array(
					'type' => 'text',
					'label' => $this->l('2nd Additional Parameter Value'),
					'name' => 'addon_two',
				),
			),
			'submit' => array(
							'title' => $this->l('Save'),
						)
		);

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        foreach ($this->fields_form['input'] as $inputfield) {
            if ($inputfield['name'] === 'expirydatestr') {
                //$expirydate = date("Y-m-d h:i:sa", $obj->$inputfield['name'], 'Asia/Kolkata');
                $expirydate = 0;
                if (empty($obj->$inputfield['name'])) {
                    $expirydate = date("d-m-Y h:i:s");
                } else {
                    $expirydate = date("d-m-Y h:i:s", $obj->$inputfield['name']);
                }
                $this->fields_value[$inputfield['name']] = $expirydate;
            } elseif ($inputfield['name'] === 'smstitleparam' || $inputfield['name'] === 'smstitle') {
            } else {
				$fieldName = $inputfield["name"];
				$this->fields_value[$fieldName] = $obj->$fieldName;
            }
        }

        return parent::renderForm();
    }
	
    public function getSMSProcessingType()
    {
        $sms_processing_type = array(
			array('id' => 'cURL', 'name' => 'cURL'),
			array('id' => 'SOAP', 'name' => 'SOAP'),
			array('id' => 'Special', 'name' => 'Special'),			
		);
        return array(
			array(
				'name' => ('Select Message Processing Type'),
				'query' => $sms_processing_type
			),
		);
    }	
}
