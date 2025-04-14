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

	<div id="getphoneform" class="twf_div col-xs-12 col-md-6">
		<span class="cross" title="{l s='Close window' mod='loginbymobile'}"></span>
		<form method="post" id="getphoneform_form" class="twf_form">
			<h1 class="page-heading step-num">{l s='Register Mobile Number' mod='loginbymobile'}</h1>
			<hr>
			<fieldset>
				<h3 class="page-subheading">{l s='Select your country and Enter your Mobile number' mod='loginbymobile'}</h3>
				<div id="getphoneform_content">
					<!-- Error return block -->
					<div id="getphoneform_errors" class="alert alert-danger" style="display:none;"></div>
					<!-- END Error return block -->
					<input type="hidden" name="session_key" value="" />

					<p class="form-group" {if $countries|@count < 2}style="display:none;"{/if}>
						<label for="lbm_getPhone_country">{l s='Country' mod='loginbymobile'} <sup>*</sup></label>
						<select name="lbm_getPhone_country" id="lbm_getPhone_country" class="form-control">
								{foreach from=$countries item=v}
									<option value="{$v.id_country}"{if (isset($smarty.post.id_country) AND $smarty.post.id_country == $v.id_country) OR (!isset($smarty.post.id_country) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name}</option>
								{/foreach}
						</select>
					</p>
					<p class="form-group">
						<label for="lbmphonenumber">{l s='Mobile Number' mod='loginbymobile'}</label>
						<input class="form-control" id="lbmphonenumber" name="lbmphonenumber" type="number" pattern="\d*"/>
					</p>
					<p class="submit">
						{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back}" />{/if}
						<button type="submit" id="lbmSubmitPhone" name="lbmSubmitPhone" class="button btn btn-default button-medium"><span><i class="icon-lock left"></i>{l s='Submit & Get Verification Code' mod='loginbymobile'}</span></button>
					</p>
				</div>
			</fieldset>
		</form>
	</div>

	<div id="logintwofactor" class="twf_div col-xs-12 col-md-6">
		<span class="cross" title="{l s='Close window' mod='loginbymobile'}"></span>
		<form method="post" id="logintwofactor_form" class="twf_form box">
			<h1 class="page-heading step-num"> {l s='Secure Key Verification' mod='loginbymobile'}</h1>
			<hr>
			<fieldset>
				<h3 class="page-subheading">{l s='Enter secure key received in your registered Mobile' mod='loginbymobile'}</h3>
				<div id="logintwofactor_content">
					<!-- Error return block -->
					<div id="logintwofactor_errors" class="alert alert-danger" style="display:none;"></div>
					<div id="logintwofactor_success" class="alert alert-success" style="display:none;"></div>
					<!-- END Error return block -->
					<input type="hidden" name="session_key" value="" />
					<p class="form-group">
						<label for="logintwofactorotp">{l s='Secure Key' mod='loginbymobile'}</label>
						<input class="form-control" id="logintwofactorotp" name="logintwofactorotp"/>
					</p>
					<a id="lbmResendTwoFactorOTP" style="display:none;" href="#" class="lost_password">{l s='Resend Secure Key?' mod='loginbymobile'}</a>
					<div id="lbm_otp_temp_message">Resend OTP in <span id='lbm_otp_counter'></span> seconds</div>
					<p class="submit">
						{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back}" />{/if}
						<button type="submit" id="lbmSubmitSecureKey" name="lbmSubmitSecureKey" class="button btn btn-default button-medium"><span><i class="icon-lock left"></i>{l s='Verify' mod='loginbymobile'}</span></button>
					</p>
				</div>
			</fieldset>
		</form>
	</div>
<div class="login_form_overlay"></div>
