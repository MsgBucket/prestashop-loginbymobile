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
	$("#login_form_content").append(co_lf_country_mobile);
	$('input[name="back"]').val('index');
  //Onepagecheckoutps change - uncomment this line
	$('#login_form_content #login_email').removeAttr("data-validation");
});