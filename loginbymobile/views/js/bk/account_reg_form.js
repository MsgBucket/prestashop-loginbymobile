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

	/* Remove Default Field */
	//$("select[name='lbm_ca_id_country']").parent().parent().remove();
	//$("#customer-form").find("input[name='send_otp_ca_form']").parent().remove();
	//$("#customer-form").find("input[id != 'lbm_ca_otp'][name='lbm_ca_otp']").parent().parent().remove();
	//$("#customer-form").find("input[name='lbm_ca_mobile_number']").parent().parent().replaceWith(account_reg_form_mobile);
	$("input[id != 'lbm_ca_otp'][name='lbm_ca_otp']").parent().parent().remove();
	$("input[name='lbm_ca_mobile_number']").parent().parent().replaceWith(account_reg_form_mobile);

  //Onepagecheckoutps change - uncomment this line
	//$("#form_customer").find("input[name='lbm_ca_mobile_number']").replaceWith(account_reg_form_mobile);	
	//$("#customer-form").find("input[id != 'lbm_ca_otp'][name='lbm_ca_otp']").parent().parent().remove();

	/* Remove Default Field - End */

	var selected_country_callprefix = country_call_prefix_list[selected_country];
	var selected_country_otp_eligible = LOGINBYMOBILE_OTP_list[selected_country];
	
	var email_obj = $("#customer-form").find("input[name='email']");
	var email = email_obj.val();
	if (LOGINBYMOBILE_EMAIL_REQUIRED == 0) {
		email_obj.prop('required',false);
		if (LOGINBYMOBILE_DISP_MAIL_AC_PAGE == 0) {
			email_obj.parent().parent().hide();
		} else {
			var temp_mobile_number = $("#lbm_ca_mobile_number").val();
			var temp_email_id = selected_country_callprefix + "_" + temp_mobile_number + '@' +LOGINBYMOBILE_MAIL_DOMAIN;			
			if (email == "lbm_temp@loginbymobile.com" || email == temp_email_id) {
				email_obj.val("");
			}
		}
	}

	$("#lbm_ca_mobile_number").attr( "maxlength", mobilenum_length[$("select[name='lbm_ca_id_country']").val()]);

	$("#lbm_ca_form_call_prefix").text("+"+selected_country_callprefix);

	if (LOGINBYMOBILE_MOBILE_REG == "1") {
		if (LOGINBYMOBILE_OTP_list[$("select[name='lbm_ca_id_country']").val()]) {
			$("#send_otp_ca_form").parent().show();
			$("input[name='lbm_ca_otp']").parent().parent().show();
			$("input[name='lbm_ca_otp']").prop('required',true);
		} else {
			$("#send_otp_ca_form").parent().hide();
			$("input[name='lbm_ca_otp']").parent().parent().hide();
			$("input[name='lbm_ca_otp']").prop('required',false);
		}
		triggerCountryChangeEvents_regform();
		if (selected_country_otp_eligible) {
			$("input[name='lbm_ca_otp']").parent().parent().show();
		} else {
			$("input[name='lbm_ca_otp']").parent().parent().hide();
		}
    enableMobileNumberZeroPrefixValidationsCA();
	}
});

function addResendOTPEvent_regform(e)
{
	e.preventDefault();
	if (ajaxotp == 1) {
		ajaxotp++;
		resendOTPFunction_regform();
		enableOTP_regform(parseInt(LOGINBYMOBILE_OTP_TIMEINTERVAL)*1000);
	}
}

function enableOTP_regform(time)
{
	setTimeout(function() {
  		$("#send_otp_error").hide();
		$("#send_otp_success").slideUp("slow");
		$("#send_otp_ca_form").slideDown("slow");
		ajaxotp = 1;
	}, time);
}

function resendOTPFunction_regform()
{
	$("#send_otp_ca_form").hide();
	$("input[name='lbm_ca_otp']").parent().parent().fadeIn("slow");
	var ca_save_button_obj = $("#customer-form").find('button[data-action="save-customer"]');
	ca_save_button_obj.parent().fadeIn("slow");
	$("#submitGuestAccount").parent().fadeIn("slow");
	submitResendSmsFunction_regform();
}


function submitResendSmsFunction_regform() {
	$("#create_account_error").html("").hide();
	var lbm_ca_mobile_number = $("#lbm_ca_mobile_number").val();
	var lbm_ca_id_country = $("select[name='lbm_ca_id_country']").val();
  var session_key = $('input[name="session_key_reg"]').val();
	if (parseInt(lbm_ca_mobile_number.length) >= parseInt(mobile_size_min_list[lbm_ca_id_country])) {
		$.ajax({
			type: "POST",
			url: prestashop['urls']['base_url'],
			async: true,
			cache: false,
			dataType : "json",
			data:
			{
				controller: "AccountRegistration",
				module: "loginbymobile",
				ajax: true,
				fc: "module",
				caAction: 'casendsms',
				otp_mobile_num: lbm_ca_mobile_number,
				lbm_id_country: lbm_ca_id_country,
				session_key_reg: session_key,
				back: $("input[name=back]").val(),
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
                    var errors = '<b>'+txtThereis+' '+jsonData.errors.length+' '+'errors'+':</b><ol>';
                    for(error in jsonData.errors) {
                        if (error != 'indexOf') {
                            errors += '<li>'+jsonData.errors[error]+'</li>';
                        }
                    }
					errors += '</ol>';
					$('#send_otp_error').html(errors).slideDown('slow');
					$('#send_otp_success').hide();
					if (jsonData.lbmpageid == 'root') {
						setTimeout(function() {
							window.location.href = jsonData.redirectlink;
						}, 5000);
					}
				} else {
					$("#send_otp_error").hide();
          $('input[name="session_key_reg"]').val(jsonData.session_key);
          	$('#send_otp_success').html(timeIntervalMsg).slideDown('slow');
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				error = "TECHNICAL ERROR: unable to load result.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + "Text status: " + textStatus;
				if (!!$.prototype.fancybox)
				{
					$.fancybox.open([
					{
						type: "inline",
						autoScale: true,
						minHeight: 30,
						content: "<p class="+"fancybox-error"+">" + error + "</p>"
					}],
					{
						padding: 20
					});
				} else {
					alert(error);
				}
			}
		});
	} else {
		$("#send_otp_error").html("<ol>" + mobile_min_size_msg + "</ol>").show();
	}
}

function triggerCountryChangeEvents_regform()
{
	$("select[name='lbm_ca_id_country']").on("change",function() {
		var lbm_ca_id_country = $(this).val();
		var call_prefix = country_call_prefix_list[lbm_ca_id_country];
		$("#lbm_ca_form_call_prefix").text("+"+call_prefix);

		$("#lbm_ca_mobile_number").attr( "maxlength", mobilenum_length[lbm_ca_id_country]);

		if (LOGINBYMOBILE_OTP_list[lbm_ca_id_country]) {
			$("#send_otp_ca_form").parent().show();
			$("input[name='lbm_ca_otp']").parent().parent().show();
			$("input[name='lbm_ca_otp']").prop('required',true);
		} else {
			$("#send_otp_ca_form").parent().hide();
			$("input[name='lbm_ca_otp']").parent().parent().hide();
			$("input[name='lbm_ca_otp']").prop('required',false);
		}
	});

}

function enableMobileNumberZeroPrefixValidationsCA()
{
if (LOGINBYMOBILE_ACCEPTZERO == 0) {
    $(document).off('keyup', '#lbm_ca_mobile_number').on('keyup', '#lbm_ca_mobile_number', function(event) {
        var input = event.currentTarget.value;
        if (input.search(/^0/) != -1) {
			input = input.replace(/^0+/, '');
			$(this).val(input);			
            alert(zeroprefixmsg);
        }
    });
}
    //For numeric
    $(document).off('keydown', '#lbm_ca_mobile_number').on('keydown', '#lbm_ca_mobile_number', function(event) {
    //$("#lbm_fp_mobile_number").keydown(function(event) {
        // Allow only backspace and delete
        if ( event.keyCode == 46 || event.keyCode == 8) {
            // let it happen, don't do anything
        } else {
            // Ensure that it is a number and stop the keypress
            if ((event.keyCode !==9) && ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105))) {
                event.preventDefault();
            } else {
		          if (LOGINBYMOBILE_ACCEPTZERO == 0) {
                if ($.trim($(this).val()) == '') {
                    if (event.keyCode == 48 || event.keyCode == 96) {
                        alert(zeroprefixmsg);
                        event.preventDefault();
                    }
                }
              }
            }
        }
    });
}
