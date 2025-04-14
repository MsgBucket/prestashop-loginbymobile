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
    setDefaultValues();
    triggerCountryChangeEvents();
    enableMobileNumberZeroPrefixValidations();
    if (LOGINBYMOBILE_ENABLE_LOGIN_FEATURE == '0') {
        hideLoginByMobileForm();
    }
    if (LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN == '0') {
        hideLoginByEmailForm();
    }
	signinByMobileNumberLF();
});

function setDefaultValues()
{
    $("#lbm_lf_mobile_number").attr( "maxlength", mobilenum_length[$("#lbm_lf_id_country").val()]);
    $("#lbm_reg_call_prefix").text(call_prefix);
    $("#submit-login-form-mobile-loading").hide();
}

function triggerCountryChangeEvents()
{
    $('#lbm_lf_id_country').on('change',function() {
        var call_prefix = $(this).find(':selected').attr('call_prefix');
        $("#lbm_reg_call_prefix").text('+'+call_prefix);
        var lbm_lf_id_country = $(this).val();
        $("#lbm_lf_mobile_number").attr( "maxlength", mobilenum_length[lbm_lf_id_country]);
    });
}

function enableMobileNumberZeroPrefixValidations()
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
    //$("#lbm_lf_mobile_number").keydown(function(event) {
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

function hideLoginByMobileForm()
{
    $("#lbm_lf_section").hide();
}

function hideLoginByEmailForm()
{
    $("#login-form").hide();
    $("#lbm_lf_title").hide();

}

function signinByMobileNumberLF()
{
	$(document).off('submit', '#lbm_login_form').on('submit', '#lbm_login_form', function(e) {
	e.preventDefault();
	e.stopPropagation();
    //if (tfotpcount === 1) {
    //    tfotpcount++;
        $("#submit-login-form-mobile").hide();
        $("#submit-login-form-mobile-loading").show();
        e.preventDefault();
        var lbm_lf_id_country = $("#lbm_lf_id_country").val();
        var lbm_lf_mobile_number = $("#lbm_lf_mobile_number").val();
		if (LOGINBYMOBILE_ACCEPTZERO == 0) {
			lbm_lf_mobile_number = lbm_lf_mobile_number.replace(/^0+/, '');
		}
		
		var lbm_login_form = $("#lbm_login_form");
		var back = lbm_login_form.find('input[name="back"]').val();		
		
		var lbm_lf_password = $("#lbm_lf_password").val();
//alert(lbm_lf_mobile_number);
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
				submitLoginFormMobile: true,
                lbm_lf_id_country: lbm_lf_id_country,
				back: back,
                lbm_lf_mobile_number: lbm_lf_mobile_number,
                lbm_lf_password: encodeURIComponent(lbm_lf_password),
            },
            success: function(jsonData)
            {
                if (jsonData.hasError) {
					$("#submit-login-form-mobile").show();
					$("#submit-login-form-mobile-loading").hide();
                    var errors = '<b>'+txtThereis+' '+jsonData.errors.length+' '+'errors'+':</b><ol>';
                    for(error in jsonData.errors) {
                        if (error != 'indexOf') {
                            errors += '<li>'+jsonData.errors[error]+'</li>';
                        }
                    }
					errors += '</ol>';
					$('#lbm_lf_error').html(errors).slideDown('slow');
					if (typeof bindUniform !=='undefined') {
						bindUniform();
					}
					if (typeof bindStateInputAndUpdate !=='undefined') {
						bindStateInputAndUpdate();
					}
					document.location = '#lbm_lf_error';
                } else {
					static_token = jsonData.token;
					if (jsonData.lbmpageid == 'verifysecurekey') {
						$('.login_form_overlay').show();
						$('#logintwofactor').fadeIn('fast');
						$("#logintwofactor_success").hide();
						$("#logintwofactor_errors").hide();
						$("#getphoneform").css('display', 'none');
						var that = $("#logintwofactor");
						that.find('input[name="session_key"]').val(jsonData.session_key);
					} else if (jsonData.lbmpageid == 'redirect') {
						window.location.href = jsonData.redirectlink;
					}  else {
						//(jsonData.lbmpageid == 'root')
						$('.login_form_overlay').hide();
						//$("#login_form").show();
						$("#logintwofactor").css('display', 'none');
						$("#getphoneform").css('display', 'none');
					}
                }
				//enableSubmitFlag(LOGINBYMOBILE_OTP_TIMEINTERVAL);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                $("#submit-login-form-mobile").show();
                $("#submit-login-form-mobile-loading").hide();
                error = "TECHNICAL ERROR: unable to load result.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
				//enableSubmitFlag(LOGINBYMOBILE_OTP_TIMEINTERVAL);
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
    //}
}

function enableSubmitFlag(time)
{
    setTimeout(function() {
        tfotpcount = 1;
    }, time);
}

/*
    $.fn.popcenter = function () {
        this.css("position","absolute");
        this.css("top", ( jQuery(window).height() - this.height() ) / 2+jQuery(window).scrollTop() + "px");
        this.css("left", ( jQuery(window).width() - this.width() ) / 2+jQuery(window).scrollLeft() + "px");
        return this;
      }
*/