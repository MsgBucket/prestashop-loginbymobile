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

class LoginbyMobileSendSMSModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        require_once(_PS_MODULE_DIR_.'loginbymobile/loginbymobile.php');
        $loginbymobile = new loginbymobile();
        if (Tools::isSubmit('SubmitSmsSend')) {
            $id_country = Tools::getValue('lbm_id_country');
            $lbm_result = $loginbymobile->processSmsSend(Tools::getValue('otp_mobile_num'), $id_country);
            if (isset($lbm_result['hasError']) && $lbm_result['hasError']) {
                $errors = $lbm_result['errors'];
                foreach ($errors as $error) {
                    $this->errors[] = $error;
                }
            }
            $return = array(
				'hasError' => !empty($this->errors),
				'errors' => $this->errors,
				'page' => $lbm_result,
				'token' => Tools::getToken(false)
			);
            die(Tools::jsonEncode($return));
        }
    }
}