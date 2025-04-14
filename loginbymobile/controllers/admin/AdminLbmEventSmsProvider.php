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

require_once(_PS_MODULE_DIR_.'loginbymobile/classes/lbmevent.php');
class AdminLbmEventSmsProviderController extends ModuleAdminController
{
    protected $position_identifier = 'id_lbmevent';
    public function __construct()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->explicitSelect = false;
        $this->context = Context::getContext();
        $this->id_lang = $this->context->language->id;
        $this->lang = false;
        $this->ajax = 1;
        $this->path = _MODULE_DIR_.'loginbymobile';
        $this->default_form_language = $this->context->language->id;
        $this->table = 'lbmevent';
        $this->className = 'Lbmevent';
        $this->identifier = 'id_lbmevent';
        $this->allow_export = true;
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'id_lbmevent';
		$this->event_name_array = array();
		$this->sms_provider_array = array();
		
        $event_names = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT * FROM `'._DB_PREFIX_.'lbmevent` event WHERE 1 ORDER BY `event_name` ASC');
        foreach ($event_names as $event_name) {
            $this->event_name_array[$event_name['event_name']] = $event_name['event_name'];
        }
		
        $sms_providers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT * FROM `'._DB_PREFIX_.'lbmsmsprovider` event WHERE 1 ORDER BY `alias` ASC');
        foreach ($sms_providers as $sms_provider) {
            $this->sms_provider_array[$sms_provider['id_lbmsmsprovider']] = $sms_provider['alias'];
        }		
		
        parent::__construct();
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Bulk Delete'),
                'icon' => 'icon-power-off text-success',
            ),
        );
        $this->fields_list = array(
            'event_name' => array(
                'title' => $this->l('Event Name'),
                'type' => 'text',
                'remove_onclick' => true,
                'class' => 'fixed-width-md',
            ),
            'hook' => array(
                'title' => $this->l('Hook Name'),
                'type' => 'text',
                'remove_onclick' => true,
                'class' => 'fixed-width-md',
            ),			
            'alias' => array(
                'title' => $this->l('Message Provider'),
                'type' => 'select',
                'list' => $this->sms_provider_array,
                'filter_key' => 'smsprovider!alias',
                'filter_type' => 'string',
                'remove_onclick' => true,
                'order_key' => 'alias',
                'class' => 'fixed-width-md',
            ),
            'active' => array('title' => $this->l('Active'),'class' => 'fixed-width-sm', 'active' => 'status', 'type' => 'bool'),
        );
		
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'lbmsmsprovider` smsprovider ON (a.`id_lbmsmsprovider` = smsprovider.`id_lbmsmsprovider`) ';
        $this->_select .= 'smsprovider.`alias`';		
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->l('Manage Message Events');
        $this->page_header_toolbar_btn['lbm_settings'] = array(
            'href' => $this->context->link->getAdminLink('AdminModules', false).'&configure=loginbymobile'
                  .'&token='.Tools::getAdminTokenLite('AdminModules')
                  .'&module_name=loginbymobile',
            'desc' => $this->l('Settings :: Login By Mobile', null, null, false),
            'icon' => 'process-icon-configure'
        );		
        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                            'title' => $this->l('Message Events'),
                            'icon' => 'icon-cogs'
                        ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Event Name'),
                    'name' => 'event_name',
                    'required' => true,
                    'hint' => $this->l('Event Name'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Hook Name'),
                    'name' => 'hook',
                    'required' => true,
                    'hint' => $this->l('Hook Name'),
                ),
				array(
					'type' => 'select',
					'label' => $this->l('Message Providers'),
					'name' => 'id_lbmsmsprovider',
					'id' => 'id_lbmsmsprovider',
					'options' => array(
						'optiongroup' => array(
							'query' => $this->getSMSProviders(),
							'label' => 'name'
						),
						'options' => array(
							'query' => 'query',
							'id' => 'id_lbmsmsprovider',
							'name' => 'alias'
						),
					),
				),				
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable?'),
                    'name' => 'active',
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
            )
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        foreach ($this->fields_form['input'] as $inputfield) {
			$fieldName = $inputfield["name"];
			$this->fields_value[$fieldName] = $obj->$fieldName;
        }
        return parent::renderForm();
    }
	
    public function getSMSProviders()
    {
        $sms_providers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT * FROM `'._DB_PREFIX_.'lbmsmsprovider` WHERE 1 ORDER BY `alias` ASC');
        return array(
			array(
				'name' => ('Select Message Provider'),
				'query' => $sms_providers
			),
		);
    }
	
}
