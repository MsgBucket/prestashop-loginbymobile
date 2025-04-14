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

function upgrade_module_17_0_8($module)
{
    $module->registerHook('actionDispatcherBefore');
    return true;
}
