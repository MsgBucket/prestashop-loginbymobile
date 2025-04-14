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

require_once(_PS_MODULE_DIR_.'loginbymobile/controllers/front/LBMSMSSender.php');

class LoginbymobileLbmcronModuleFrontController extends ModuleFrontController
{
    public function postProcess() {
		
        $deliveredOrders = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'lbmorderdelivery` WHERE `active` = 1');
		$provider = Configuration::get('LOGINBYMOBILE_SMS_PROVIDER');
		$subject = "First Order Delivered";
		$lbm_sms_sender = new LBMSMSSender();
		$count = count($deliveredOrders);
		$orderIdsProcessedEmail = "";		
		$orderIdsProcessed = "";
		foreach($deliveredOrders as &$order) {
			if (empty($order['phone_number'])) {
				$email_id = $order['email'];
				$orderIdsProcessedEmail = $orderIdsProcessedEmail.",".$order['id_order'];
				if (Validate::isEmail($email_id)) {
					$customer = new Customer($order->id_customer);
					$order['firstname'] = $customer->firstname;
					$order['lastname'] = $customer->lastname;
					Mail::Send((int)Context::getContext()->language->id, "order_merchant_comment", Mail::l('Order Delivered Survey'), $order, $email_id,
					$customer->firstname." ".$customer->lastname, null, null, null, null, dirname(__FILE__).'/mails/', false, (int)Context::getContext()->shop->id);
				}				
			} else {
				$smsErrors = $lbm_sms_sender->getProviderAndSendSMS($provider, $order['phone_number'], $order['message'], $subject, (int)$order['id_country'], (int)$order['transactional'], (int)$order['unicode'], $subject);

				Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'lbmorderdelivery` SET `active` = 0 WHERE `id_order` = '.(int)$order['id_order']);
				$orderIdsProcessed = $orderIdsProcessed.",".$order['id_order'];
				sleep(1);				
			}			
		}
        $return = array(
			'processed_records_count' => $count,
			'orders_processed' => $orderIdsProcessed,
			'orders_processed_email' => $orderIdsProcessedEmail,
        );
        die(Tools::jsonEncode($return));		
    }
}
