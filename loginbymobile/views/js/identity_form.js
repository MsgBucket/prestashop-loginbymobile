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

$(document).ready(function() {
	var loadtime_mobile_number = $("input[name='lbm_ca_mobile_number']").val();
	var loadtime_id_country = $("select[name='lbm_ca_id_country']").val();
	initializeOtpFieldsForIdentity(loadtime_mobile_number, loadtime_id_country);
	
	$("input[name='lbm_ca_mobile_number']").on("change",function() {
		// make OTP required or optional based on whether Mobile Number is updated
		var new_mobile_number = $(this).val();
		var new_id_country = $("select[name='lbm_ca_id_country']").val();
		initializeOtpFieldsForIdentity(new_mobile_number, new_id_country);
	});

	$("select[name='lbm_ca_id_country']").off('change').on("change",function() {
		var new_id_country = $(this).val();
		var call_prefix = country_call_prefix_list[new_id_country];
		$("#lbm_ca_form_call_prefix").text("+"+call_prefix);

		$("#lbm_ca_mobile_number").attr( "maxlength", mobilenum_length[new_id_country]);
		var new_mobile_number = $("input[name='lbm_ca_mobile_number']").val();
		initializeOtpFieldsForIdentity(new_mobile_number, new_id_country);
	});
});

function initializeOtpFieldsForIdentity(new_mobile_number, new_id_country)
{
	if ((original_mobile_number != new_mobile_number) || (original_id_country != new_id_country)) {
		if (LOGINBYMOBILE_OTP_list[new_id_country]) {
			$("#send_otp_ca_form").parent().show();
			$("input[name='lbm_ca_otp']").parent().parent().show();
			$("input[name='lbm_ca_otp']").prop('required',true);
		} else {
			$("#send_otp_ca_form").parent().hide();
			$("input[name='lbm_ca_otp']").parent().parent().hide();
			$("input[name='lbm_ca_otp']").prop('required',false);
		}
	} else {
		$("#send_otp_ca_form").parent().hide();
		$("input[name='lbm_ca_otp']").parent().parent().hide();
		$("input[name='lbm_ca_otp']").prop('required',false);
	}
	
}
