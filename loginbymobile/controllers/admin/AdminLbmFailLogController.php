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

require_once(_PS_MODULE_DIR_.'loginbymobile/classes/lbmfaillog.php');
class AdminLbmFailLogController extends ModuleAdminController
{

    public $module;
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
        $this->table = 'lbmfaillog';
        $this->className = 'LbmFailLog';
        $this->identifier = 'id_lbmfaillog';
        $this->allow_export = true;
        $this->name = 'AdminLbmFailLogController';
        $this->bootstrap = true;
        parent::__construct();
        $this->bulk_actions = array(
                'deleteAllLogs' => array(
                        'text' => $this->l('Delete Selected Logs'),
                        'icon' => 'icon-close text-success',
                    ),
            );
        $this->fields_list = array(
                'id_lbmfaillog' => array(
                    'title' => $this->l('Log ID'),
                    'align' => 'text-center',
                    'class' => 'fixed-width-xs'
                ),
                'providername' => array(
                        'title' => $this->l('Provider'),
                        'align' => 'text-center',
                        'class' => 'fixed-width-xs'
                    ),
                'timestamp' => array(
                        'title' => $this->l('Timestamp'),
                        'align' => 'text-center',
                        'type' => 'datetime',
                        'filter_key' => 'timestamp',
                        'class' => 'fixed-width-md'
                    ),
            );
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->l('Failure Log');
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

    public function renderForm()
    {
        $this->fields_form = array(
                'legend' => array(
                        'title' => $this->l('Fail Log Record Detail'),
                        'icon' => 'icon-cogs'
                    ),
                'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('Provider'),
                            'name' => 'providername',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Request'),
                            'name' => 'request',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Response'),
                            'name' => 'response',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Timestamp'),
                            'name' => 'timestamp',
                        ),
                    ),
            );

        if (!($obj = $this->loadObject(true))) {
            return;
        }
        foreach ($this->fields_form['input'] as $inputfield) {
            $this->fields_value[$inputfield['name']] = $obj->$inputfield['name'];
        }
        return parent::renderForm();
    }

    protected function processBulkDeleteAllLogs()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $object = new $this->className();
            $object->deleteSelection($this->boxes);
        }
        return true;
    }
    public function renderList()
    {
        unset($this->toolbar_btn['new']);
        return parent::renderList();
    }
}
