/*
* Please do not edit or add any code in this file without the permission of MsgBucket
*
* @author    MsgBucket
* @copyright MsgBucket
* @license   https://www.msgbucket.com
* Prestashop version 1.7+
* loginbymobile 17.0.0
* Sep 2018
*/

$(document).ready(function() {
	$("#checkout-login-form").append(co_lf_country_mobile);
	$('input[name="back"]').val('order');
  //Onepagecheckoutps change - uncomment this line
	$('#txt_login_email').removeAttr("data-validation");
});