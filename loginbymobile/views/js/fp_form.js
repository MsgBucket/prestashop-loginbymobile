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

	$("#content").find("form").after(fp_country_mobile);
	if (LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN == '0') {
		hideLoginByEmailForm();
	}
	var selected_country_callprefix = country_call_prefix_list[selected_country];
	var selected_country_otp_eligible = LOGINBYMOBILE_OTP_list[selected_country];

	$("#lbm_fp_mobile_number").attr( "maxlength", mobilenum_length[$("#lbm_fp_id_country").val()]);

	$("#lbm_fp_form_call_prefix").text("+"+selected_country_callprefix);
	$("#submit-fp-form-mobile-loading").hide();

	//if (LOGINBYMOBILE_OTP_list[$("select[name='lbm_fp_id_country']").val()]) {
	if (LOGINBYMOBILE_OTP_list[$("#lbm_fp_id_country").val()]) {		
		$("#send_otp_fp_form").parent().show();
		$("input[name='lbm_fp_otp']").parent().parent().parent().show();
		$("input[name='lbm_fp_otp']").prop('required',true);
	} else {
		$("#send_otp_fp_form").parent().hide();
		$("input[name='lbm_fp_otp']").parent().parent().parent().hide();
		$("input[name='lbm_fp_otp']").prop('required',false);
	}
	triggerCountryChangeEvents_fpform();
	if (selected_country_otp_eligible) {
		$("input[name='lbm_fp_otp']").parent().parent().parent().show();
	} else {
		$("input[name='lbm_fp_otp']").parent().parent().parent().hide();
	}
	enableMobileNumberZeroPrefixValidationsFP();
	fpByMobileNumber();
});

function hideLoginByEmailForm()
{
	$("#content").find("hr").remove();
	$("form.forgotten-password").hide();
    $("#lbm_fp_title").hide();
}

function addResendOTPEvent_fpform(e)
{
	e.preventDefault();
	if (ajaxotp == 1) {
		ajaxotp++;
		resendOTPFunction_fpform();
		enableOTP_fpform(parseInt(LOGINBYMOBILE_OTP_TIMEINTERVAL)*1000);
	}
}

function enableOTP_fpform(time)
{
	setTimeout(function() {
		$("#lbm_fp_error").hide();
		$("#send_otp_success").slideUp("slow");
		$("#send_otp_fp_form").slideDown("slow");
		ajaxotp = 1;
	}, time);
}

function resendOTPFunction_fpform()
{
	$("#send_otp_fp_form").hide();
	$("input[name='lbm_fp_otp']").parent().parent().parent().fadeIn("slow");
	$("#submit-fp-form-mobile").parent().fadeIn("slow");
	submitResendSmsFunction_fpform();
}

function submitResendSmsFunction_fpform() {
	$("#lbm_fp_error").html("").hide();
	var lbm_fp_mobile_number = $("#lbm_fp_mobile_number").val();
	var lbm_fp_id_country = $("#lbm_fp_id_country").val();
    var session_key = $("#lbm_fp_form").find('input[name="session_key"]').val();

	if (parseInt(lbm_fp_mobile_number.length) >= parseInt(mobile_size_min_list[lbm_fp_id_country])) {
		$.ajax({
			type: "POST",
			url: prestashop['urls']['base_url'],
			async: true,
			cache: false,
			dataType : "json",
			data:
			{
				controller: "ForgotPassword",
				module: "loginbymobile",
				ajax: true,
				fc: "module",
				fpAction: 'fpsendsms',
				otp_mobile_num: lbm_fp_mobile_number,
				lbm_id_country: lbm_fp_id_country,
				session_key: session_key,
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
					$('#lbm_fp_error').html(errors).slideDown('slow');
					$('#send_otp_success').hide();
					if (jsonData.lbmpageid == 'root') {
						setTimeout(function() {
							window.location.href = jsonData.redirectlink;
						}, 5000);
					}
				} else {
					$("#lbm_fp_error").hide();
                    var that = $("#lbm_fp_form");
                    that.find('input[name="session_key"]').val(jsonData.session_key);
					$("#lbm_fp_error").hide();
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
		$("#lbm_fp_error").html("<ol>" + mobile_min_size_msg + "</ol>").show();
	}
}

function triggerCountryChangeEvents_fpform()
{
	//$("select[name='lbm_fp_id_country']").on("change",function() {
	$("#lbm_fp_id_country").on("change",function() {		
		var lbm_fp_id_country = $(this).val();
		var call_prefix = country_call_prefix_list[lbm_fp_id_country];
		$("#lbm_fp_form_call_prefix").text("+"+call_prefix);

		$("#lbm_fp_mobile_number").attr( "maxlength", mobilenum_length[lbm_fp_id_country]);

		if (LOGINBYMOBILE_OTP_list[lbm_fp_id_country]) {
			$("#send_otp_fp_form").parent().show();
			$("input[name='lbm_fp_otp']").parent().parent().parent().show();
			$("input[name='lbm_fp_otp']").prop('required',true);
		} else {
			$("#send_otp_fp_form").parent().hide();
			$("input[name='lbm_fp_otp']").parent().parent().parent().hide();
			$("input[name='lbm_fp_otp']").prop('required',false);
		}
	});

}

function enableMobileNumberZeroPrefixValidationsFP()
{
if (LOGINBYMOBILE_ACCEPTZERO == 0) {
    $(document).off('keyup', '#lbm_fp_mobile_number').on('keyup', '#lbm_fp_mobile_number', function(event) {
        var input = event.currentTarget.value;
        if (input.search(/^0/) != -1) {
			input = input.replace(/^0+/, '');
			$(this).val(input);			
            alert(zeroprefixmsg);
        }
    });
}
    //For numeric
    $(document).off('keydown', '#lbm_fp_mobile_number').on('keydown', '#lbm_fp_mobile_number', function(event) {
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

function fpByMobileNumber()
{
	$(document).off('submit', '#lbm_fp_form').on('submit', '#lbm_fp_form', function(e) {
	e.preventDefault();
	e.stopPropagation();	
    //if (tfotpcount === 1) {
    //    tfotpcount++;
		var session_key = $("#lbm_fp_form").find('input[name="session_key"]').val();
        $("#submit-fp-form-mobile").hide();
        $("#submit-fp-form-mobile-loading").show();

        var lbm_fp_id_country = $("#lbm_fp_id_country").val();
        var lbm_fp_mobile_number = $("#lbm_fp_mobile_number").val();
		var lbm_fp_otp = $("#lbm_fp_otp").val();
//alert(lbm_fp_mobile_number);
        $.ajax({
            type: 'POST',
            url: prestashop['urls']['base_url'],
            async: true,
            cache: false,
            dataType: "json",
            data:
            {
                controller: 'ForgotPassword',
                module: 'loginbymobile',
                ajax: true,
                fc: 'module',
				fpAction: 'fpbymobile',
                lbm_fp_id_country: lbm_fp_id_country,
				session_key: session_key,
				lbm_fp_otp: lbm_fp_otp,
                lbm_fp_mobile_number: lbm_fp_mobile_number,
            },
            success: function(jsonData)
            {
                if (jsonData.hasError) {
					$("#submit-fp-form-mobile").show();
					$("#submit-fp-form-mobile-loading").hide();
                    var errors = '<b>'+txtThereis+' '+jsonData.errors.length+' '+'errors'+':</b><ol>';
                    for(error in jsonData.errors) {
                        if (error != 'indexOf') {
                            errors += '<li>'+jsonData.errors[error]+'</li>';
                        }
                    }
					errors += '</ol>';
					$('#lbm_fp_error').html(errors).slideDown('slow');
					document.location = '#lbm_fp_error';
					if (jsonData.lbmpageid == 'root') {
						setTimeout(function() {
							window.location.href = jsonData.redirectlink;
						}, 5000);
					}
                } else {
					$('#lbm_fp_section').replaceWith(jsonData.page);
					//if (jsonData.lbmpageid == 'root') {
						setTimeout(function() {
							window.location.href = jsonData.redirectlink;
						}, 5000);
					//}
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                $("#submit-fp-form-mobile").show();
                $("#submit-fp-form-mobile-loading").hide();
                error = "TECHNICAL ERROR: unable to load result.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
                if (!!$.prototype.fancybox)
                {
                    $.fancybox.open([
                    {
                        type: 'inline',
                        autoScale: true,
                        minHeight: 30,
                        content: "<p class='fancybox-error'>" + error + '</p>'
                    }],
                    {
                        padding: 20
                    });
                } else {
                    alert(error);
                }
            }
        });
    });		
}