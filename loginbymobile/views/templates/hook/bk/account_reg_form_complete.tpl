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

<section id="lbm_ca_section" class="login-form">
    <div id="lbm_ca_error" class="alert alert-danger error" style="display: none;"></div>
	<p>{l s='Register and verify your mobile number' mod='loginbymobile'}</p>
	<section>
		{if $countries|@count > 1}
			<div class="form-group row">
				<label for="lbm_ca_id_country" class="col-md-3 form-control-label">
					{l s='Country' mod='loginbymobile'}
				</label>
				<div class="col-md-6">
					<select
						class="form-control form-control-select js-country"
						name="lbm_ca_id_country" id="lbm_ca_id_country">
						{foreach from=$countries item=v}
							<option
								value="{$v.id_country}"
								call_prefix="{$v.call_prefix}" {if (isset($sl_country) && $sl_country == $v.id_country)}selected="selected"{/if}>
									{$v.name}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
		{else}
			<input
				type="hidden"
				id="lbm_ca_id_country"
				name="lbm_ca_id_country"
				value="{$sl_country}"/>
		{/if}


		<div class="form-group row">
			<label for="lbm_ca_mobile_number" class="col-md-3 form-control-label">
				{l s='Mobile Number' mod='loginbymobile'}
			</label>
			<div class="col-md-6 lbm_mobile_div">
				<div class="input-group js-parent-focus">
					<span id="lbm_ca_form_call_prefix">{$call_prefix}</span>
					<input
					type="number" pattern="\d*"
						class="form-control"
						id="lbm_ca_mobile_number"
						name="lbm_ca_mobile_number"
						value="{if isset($lbm_ca_mobile_number)}{$lbm_ca_mobile_number}{/if}"
						maxlength="10" required/>
				</div>
					<span class="input-group-btn" style="padding-top: 3px;">
					  <button
						class="btn"
						type="button"
						id="send_otp_ca_form"
						data-action="send_otp_ca_form"
						onclick="addResendOTPEvent_regform(event);"
						data-text-show="{l s='Send OTP' mod='loginbymobile'}"
						data-text-hide="{l s='Re-Sent OTP' mod='loginbymobile'}"
					  >
						{l s='Send OTP SMS' mod='loginbymobile'}
					  </button>
					</span>				
				<span style="padding-top: 10px; display: none;" id="send_otp_success" >{l s='OTP Sms Sent Successfully' mod='loginbymobile'}</span>
				<span style="padding-top: 10px; display: none;" id="send_otp_error" ></span>

			</div>
		</div>
		<input type="hidden" id="session_key_reg" name="session_key_reg" value="{$session_key_reg}" />
		<div class="form-group row">
			<label for="lbm_ca_otp" class="col-md-3 form-control-label">
				{l s='OTP' mod='loginbymobile'}
			</label>
			<div class="col-md-6">
			  <div class="input-group js-parent-focus">
				<input
				  class="form-control js-child-focus js-visible-password"
				  id="lbm_ca_otp"
				  name="lbm_ca_otp"
				  type="number" pattern="\d*"
				  value="{if isset($lbm_ca_otp)}{$lbm_ca_otp}{/if}" required >
			  </div>
			  <span id="otp_mobile_num_errorspan" class="opcerrormsg"></span>
			</div>
		</div>
	</section>
</section>
<hr>