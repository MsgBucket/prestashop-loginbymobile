{*
* Please do not edit or add any code in this file without the permission of MsgBucket
*
* @author    MsgBucket
* @copyright MsgBucket
* @license   https://www.msgbucket.com
* Prestashop version 1.7+
* loginbymobile 17.0.0
* Sep 2018
*}
<div>
<div class="input-group js-parent-focus">
	<span id="lbm_ca_form_call_prefix"  class="tablecelldisplay lbmcallprefix">{$call_prefix}</span>
	<input
	type="number" pattern="\d*"
		class="form-control"
		style = "border-radius:0px !important;"
		id="lbm_ca_mobile_number"
		name="lbm_ca_mobile_number"
		value="{if isset($lbm_ca_mobile_number)}{$lbm_ca_mobile_number}{/if}"
		maxlength="10" required/>
	<span class="input-group-btn">
	  <button
		class="btn"
		type="button"
		id="send_otp_ca_form"
		data-action="send_otp_ca_form"
		onclick="addResendOTPEvent_regform(event);"
		data-text-show="{l s='Send OTP' mod='loginbymobile'}"
		data-text-hide="{l s='Re-Sent OTP' mod='loginbymobile'}"
	  >
		{l s='Send OTP' mod='loginbymobile'}
	  </button>
	</span>
</div>
<span style="padding-top: 10px; display: none;" id="send_otp_success" >{l s='OTP Sms Sent Successfully' mod='loginbymobile'}</span>
<span style="padding-top: 10px; display: none;" id="send_otp_error" ></span>
</div>
		<input type="hidden" id="session_key_reg" name="session_key_reg" value="{$session_key_reg}" />