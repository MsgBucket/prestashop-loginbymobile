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
	$(document).on('click', '#logintwofactor .cross', function(e) {
		e.preventDefault();
		$('.login_form_overlay').hide();
        $("#submit-login-form-mobile").show("slow");
        $("#submit-login-form-mobile-loading").hide();
		$('#logintwofactor').fadeOut('fast');
	});
	$(document).on('click', '#getphoneform .cross', function(e) {
		e.preventDefault();
		$('.login_form_overlay').hide();
        $("#submit-login-form-mobile").show("slow");
        $("#submit-login-form-mobile-loading").hide();
		$('#getphoneform').fadeOut('fast');
	});
	enableTwoFactorAuth();
	$("#logintwofactor").hide();
	$("#getphoneform").hide();
	//addTFResendOTPEvent();

	resetlbm_otp_counter();
	//live_otp_time_count = LOGINBYMOBILE_OTP_TIMEINTERVAL;
	//$('#lbm_otp_counter').html(live_otp_time_count);
	start_lbm_otp_counter();
	$("#lbmResendTwoFactorOTP").on('click' , function() {
		sendTFOTPSMS();
		$("#lbmResendTwoFactorOTP").hide();
		$("#lbm_otp_temp_message").show();
		start_lbm_otp_counter();
	});
});

    function addTFResendOTPEvent()
    {
        var tfdelay = 0;
        $(document).off('click', '#lbmResendTwoFactorOTP').on('click', '#lbmResendTwoFactorOTP', function(e) {
            e.preventDefault();
            if (tfotpcount == 1) {
                tfotpcount++;
                resendTFOTP(tfdelay);
                enableTFOTP(LOGINBYMOBILE_OTP_TIMEINTERVAL);
                tfdelay = 10000;
            }
        });
    }

    function resendTFOTP(time)
    {
        $("#logintwofactor_success").hide();
        $("#lbmResendTwoFactorOTP").hide();
        sendTFOTPSMS();
    }
    function enableTFOTP(time)
    {
        setTimeout(function() {
            $("#logintwofactor_success").hide();
            $("#lbmResendTwoFactorOTP").fadeIn("slow");
            tfotpcount = 1;
        }, time);
    }

    function sendTFOTPSMS() {
        var session_key = $("#logintwofactor").find('input[name="session_key"]').val();

        $.ajax({
            type: 'POST',
            url: prestashop['urls']['base_url'],
            async: true,
            cache: false,
            dataType: "json",
            data:
            {
                controller: 'LBMAuth',
                module: 'loginbymobile',
                ajax: true,
                fc: 'module',
				twowayfactoraction: 'requestsecurekey',
                session_key: session_key,
            },
            success: function(jsonData)
            {
                if (jsonData.hasError)
                {
					$("#submit-login-form-mobile").show();
					$("#submit-login-form-mobile-loading").hide();

                    var errors = '<b>There are '+jsonData.errors.length+' '+'errors'+':</b><ol>';
                    for(var error in jsonData.errors) {
                        //IE6 bug fix
                        if (error !== 'indexOf') {
                            errors += '<li>'+jsonData.errors[error]+'</li>';
                        }
                    }
                    errors += '</ol>';
                    if (jsonData.lbmpageid == 'root') {
                        $('.login_form_overlay').hide();
                        $("#logintwofactor").hide();
                        $("#getphoneform").hide();
                        $('#lbm_lf_error').html(errors).slideDown('slow');
                    }
                } else {
                    $('#logintwofactor_success').html(timeIntervalMsg).slideDown('slow');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('.login_form_overlay').hide();
                $("#logintwofactor").hide();
                $("#getphoneform").hide();
                $('#lbm_lf_error').html('Error while sending SMS').slideDown('slow');
            }
        });
    }

    function enableTwoFactorAuth()
    {
		//$(document).off('click', '#submit-login').on('click', '#submit-login', function(e) {
        $(document).off('click', '#button-login').on('click', '#button-login', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var login_email = '';
            var passwd = '';
            var login_form = $("#checkoutLogin");
			login_email = login_form.find('input[name="supercheckout_email"]').val();
			passwd = login_form.find('input[name="supercheckout_password"]').val();			
			if ($.isNumeric(login_email)) {
				signinByMobileNumberLF(login_email, passwd);
			} else {
				var back = login_form.find('input[name="back"]').val();
				$.ajax({
					type: 'POST',
					url: prestashop['urls']['base_url'],
					async: true,
					cache: false,
					dataType: "json",
					data:
					{
						controller: 'LBMAuth',
						module: 'loginbymobile',
						ajax: true,
						fc: 'module',
						twowayfactoraction: 'loginbyemail',
						email: encodeURIComponent(login_email),
						passwd: encodeURIComponent(passwd),
					},
					success: function(jsonData)
					{
						if (jsonData.hasError)
						{
							$("#submit-login-form-mobile").show();
							$("#submit-login-form-mobile-loading").hide();

							var errors = '<div id="lbm_lf_email_error" class="alert alert-danger error"><b>'+txtThereis+' '+jsonData.errors.length+' '+'errors'+':</b><ol>';
							for (var error in jsonData.errors)
								//IE6 bug fix
								if (error !== 'indexOf')
									errors += '<li>'+jsonData.errors[error]+'</li>';
							errors += '</ol></div>';
							login_form.before(errors).slideDown('slow');
						}
						else
						{
							static_token = jsonData.token;
							if (jsonData.lbmpageid == 'getphone') {
								$("#logintwofactor").hide();
								$('.login_form_overlay').css('width','100%');
								$('.login_form_overlay').css('height','100%');
								$('.login_form_overlay').show("slow");
								$('#getphoneform').fadeIn('fast');
								//$("#getphoneform").show("slow");
								var that = $("#getphoneform");
								that.find('input[name="session_key"]').val(jsonData.session_key);
								that.find('select[name="lbm_getPhone_country"]').val(jsonData.id_country);
							} else if (jsonData.lbmpageid == 'verifysecurekey') {
								$('.login_form_overlay').css('width','100%');
								$('.login_form_overlay').css('height','100%');
								$('.login_form_overlay').show("slow");
								$('#logintwofactor').fadeIn('fast');
								//$("#logintwofactor").show("slow");
								$("#getphoneform").hide();
								var that = $("#logintwofactor");
								that.find('input[name="session_key"]').val(jsonData.session_key);
							} else if (jsonData.lbmpageid == 'redirect') {
								supercheckoutlogin();
								/*if (back == 'order-opc') {
									static_token = jsonData.token;
									$('.login_form_overlay').hide();
									updateNewAccountToAddressBlock(that.attr('data-adv-api'));
								} else {
									window.location.href = jsonData.redirectlink;
								}*/
							}  else {
								//(jsonData.lbmpageid == 'root')
								$('.login_form_overlay').hide();
								$("#login_form").show("slow");
								$("#logintwofactor").hide();
								$("#getphoneform").hide();
							}
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						if (textStatus !== 'abort')
						{
							error = "TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
							alert(error);
						}
					}
				});
			}
        });

        $(document).off('click', '#lbmSubmitPhone').on('click', '#lbmSubmitPhone', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var that = $("#getphoneform");
            var session_key = that.find('input[name="session_key"]').val();
            var back = that.find('input[name="back"]').val();
            var lbm_getPhone_country = that.find('select[name="lbm_getPhone_country"]').val();
            var lbmphonenumber = that.find('input[name="lbmphonenumber"]').val();

			$.ajax({
				type: 'POST',
				url: prestashop['urls']['base_url'],
				async: true,
				cache: false,
				dataType: "json",
				data:
				{
					controller: 'LBMAuth',
					module: 'loginbymobile',
					ajax: true,
					fc: 'module',
					back: back,
					twowayfactoraction: 'getphone',
					session_key: session_key,
					lbmphonenumber: lbmphonenumber,
					lbm_id_country: lbm_getPhone_country
				},
                success: function(jsonData)
                {
                    if (jsonData.hasError)
                    {
						$("#submit-login-form-mobile").show();
						$("#submit-login-form-mobile-loading").hide();

                        var errors = '<b>There are '+jsonData.errors.length+' '+'errors'+':</b><ol>';
                        for(var error in jsonData.errors)
                            //IE6 bug fix
                            if (error !== 'indexOf')
                                errors += '<li>'+jsonData.errors[error]+'</li>';
                        errors += '</ol>';
                        if (jsonData.lbmpageid == 'getphone') {
                            //$("#login_form").hide();
                            $("#logintwofactor").hide();
                            $("#getphoneform").show("slow");
                            $('#getphoneform_errors').html(errors).slideDown('slow');
                        } else {
                        //jsonData.lbmpageid == 'root'
                            $('.login_form_overlay').hide();
                            //$("#login_form").show("slow");
                            $("#logintwofactor").hide();
                            $("#getphoneform").hide();
                            $('#lbm_lf_error').html(errors).slideDown('slow');
                        }
                    }
                    else
                    {
                        if (jsonData.lbmpageid == 'redirect') {
                            if (back == 'order-opc') {
                                static_token = jsonData.token;
                                $('.login_form_overlay').hide();
                                updateNewAccountToAddressBlock(that.attr('data-adv-api'));
                            } else {
                                window.location.href = jsonData.redirectlink;
                            }
                        }
                        // update token
                        static_token = jsonData.token;
                        $('.login_form_overlay').css('width','100%');
                        $('.login_form_overlay').css('height','100%');
                        $('.login_form_overlay').show("slow");
                        $("#getphoneform").hide();
                        $('#logintwofactor').fadeIn('fast');
                        //$("#logintwofactor").show("slow");
                        var that = $("#logintwofactor");
                        that.find('input[name="session_key"]').val(jsonData.session_key);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    if (textStatus !== 'abort')
                    {
                        error = "TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
                        alert(error);
                    }
                }
            });
        });

        $(document).off('click', '#lbmSubmitSecureKey').on('click', '#lbmSubmitSecureKey', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var that = $(this);
            var that1 = $("#logintwofactor");
            var back = that1.find('input[name="back"]').val();
            var session_key = that1.find('input[name="session_key"]').val();
            var logintwofactorotp = that1.find('input[name="logintwofactorotp"]').val();

			$.ajax({
				type: 'POST',
				url: prestashop['urls']['base_url'],
				async: true,
				cache: false,
				dataType: "json",
				data:
				{
					controller: 'LBMAuth',
					module: 'loginbymobile',
					ajax: true,
					fc: 'module',
					back: back,
					twowayfactoraction: 'verifysecurekey',
					session_key: session_key,
					logintwofactorotp: logintwofactorotp
				},
                success: function(jsonData)
                {
                    if (jsonData.hasError)
                    {
						$("#submit-login-form-mobile").show();
						$("#submit-login-form-mobile-loading").hide();
                        var errors = '<b>There are '+jsonData.errors.length+' '+'errors'+':</b><ol>';
                        for (var error in jsonData.errors) {
                            //IE6 bug fix
                            if (error !== 'indexOf') {
                                errors += '<li>'+jsonData.errors[error]+'</li>';
                            }
                        }
                        errors += '</ol>';

                        if (jsonData.lbmpageid == 'root') {
                            $('.login_form_overlay').hide();
                            //$("#login_form").show("slow");
                            $("#logintwofactor").hide();
                            $("#getphoneform").hide();
                            $('#lbm_lf_error').html(errors).slideDown('slow');
                        } else if (jsonData.lbmpageid == 'getphone') {
                            //$("#login_form").hide();
                            $("#logintwofactor").hide();
                            $("#getphoneform").show("slow");
                            $('#getphoneform_errors').html(errors).slideDown('slow');
                        } else {
                        //verifysecurekey
                            //$("#login_form").hide();
                            $("#logintwofactor").show("slow");
                            $("#getphoneform").hide();
                            $('#logintwofactor_errors').html(errors).slideDown('slow');
                        }
                    }
                    else
                    {
                        if (back == 'order-opc') {
                            static_token = jsonData.token;
                            $('.login_form_overlay').hide();
                            $("#logintwofactor").hide();
                            $("#getphoneform").hide();
                            updateNewAccountToAddressBlock(that.attr('data-adv-api'));
                        } else {
                            window.location.href = jsonData.redirectlink;
                        }
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    if (textStatus !== 'abort')
                    {
                        error = "TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
                        alert(error);
                        alert(errorThrown);
                    }
                }
            });
        });

        $(document).on('click', '#logintwofactor .cross', function(e) {
            e.preventDefault();
            $('.login_form_overlay').hide();
            $('#logintwofactor').fadeOut('fast');
        });
        $(document).on('click', '#getphoneform .cross', function(e) {
            e.preventDefault();
            $('.login_form_overlay').hide();
            $('#getphoneform').fadeOut('fast');
        });

    }

    function overrideCreateAccountFormSubmit()
    {
        $(document).off('submit', '#create-account_form').on('submit', '#create-account_form', function(e) {
            e.preventDefault();

                if (ajaxcreateotp == 1) {
                    ajaxcreateotp++;
                    submitLBMFunction();
                    resetCreateOTP(1000);
                }

        });
    }

	function enableMobileNumberZeroPrefixValidationsTF()
	{
		if (LOGINBYMOBILE_ACCEPTZERO == 0) {
			$(document).off('keyup', '#lbm_lf_mobile_number').on('keyup', '#lbm_lf_mobile_number', function(event) {
				var input = event.currentTarget.value;
				if (input.search(/^0/) != -1) {
					input = input.replace(/^0+/, '');
					$(this).val(input);					
					alert(zeroprefixmsg);
				}
			});
		}
		//For numeric
		$(document).off('keydown', '#lbm_lf_mobile_number').on('keydown', '#lbm_lf_mobile_number', function(event) {
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

    function resetCreateOTP(time)
    {
        setTimeout(function() {
        ajaxcreateotp = 1;
        }, time);
    }

	function resetlbm_otp_counter() {
		stop_lbm_otp_counter();
		live_otp_time_count = LOGINBYMOBILE_OTP_TIMEINTERVAL;
		$('#lbm_otp_counter').html(live_otp_time_count);
	}

	function start_lbm_otp_counter() {
		if (live_otp_time_count<=0) {
		   resetlbm_otp_counter();
		   start_lbm_otp_counter();
		} else {
		   lbm_otp_lbm_otp_counter = setInterval(lbm_otp_counter_tick,1000);
		}
	}

	function stop_lbm_otp_counter() {
		clearInterval(lbm_otp_lbm_otp_counter);
	}

	function lbm_otp_counter_tick() {
		live_otp_time_count--;
		$('#lbm_otp_counter').html(live_otp_time_count);
		if (live_otp_time_count == 0) {
			stop_lbm_otp_counter();
			$("#lbmResendTwoFactorOTP").show();
			$("#lbm_otp_temp_message").hide();
		}
	}

