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

require_once(_PS_MODULE_DIR_.'loginbymobile/classes/lbmsmslog.php');
class AdminLbmSMSLogController extends ModuleAdminController
{
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
        $this->table = 'lbmsmslog';
        $this->className = 'LbmSMSLog';
        $this->identifier = 'id_lbmsmslog';
        $this->allow_export = true;
        $this->bootstrap = true;

        parent::__construct();
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Bulk Delete'),
                'icon' => 'icon-power-off text-success',
            ),
        );

        $this->fields_list = array(
            'id_lbmsmslog' => array('title' => $this->l('ID'), 'remove_onclick' => true,),
            
            'environment' => array('title' => $this->l('Live'), 'remove_onclick' => true, 'type' => 'bool',),
            'unicode' => array('title' => $this->l('Unicode'), 'remove_onclick' => true, 'type' => 'bool',),
            'transactional' => array('title' => $this->l('Transactional'), 'remove_onclick' => true, 'type' => 'bool',),
            'provider' => array('title' => $this->l('Provider'), 'remove_onclick' => true,),
            'destination' => array('title' => $this->l('Number'), 'remove_onclick' => true,),
            'message' => array('title' => $this->l('Message'), 'remove_onclick' => true,),
            'sent_at' => array('title' => $this->l('Sent At'), 'remove_onclick' => true, 'type' => 'datetime', 'filter_key' => 'a!sent_at',),
            
        );
		$this->_orderWay = 'DESC';
		//$this->orderBy = "";

    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->l('Message Log');
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

    public function renderList()
    {
        unset($this->toolbar_btn['new']);
        return parent::renderList();
    }
}
