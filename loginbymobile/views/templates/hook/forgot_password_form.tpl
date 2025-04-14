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

<hr>
<div id="lbm_fp_title" class="forgot-password">
	<span>
	  {l s='(Or) Reset by Mobile Number' mod='loginbymobile'}
	</span>
</div>
<hr>
<section id="lbm_fp_section" class="login-form">
    <div id="lbm_fp_error" class="alert alert-danger error" style="display: none;"></div>
	<header>
      <p class="send-renew-password-link">Please enter the mobile number you used to register. You will receive a one time code in SMS to reset your password.</p>
    </header>
    <form id="lbm_fp_form" method="post">
        <section>
            {if $countries|@count > 1}
                <div class="form-group row">
                    <label for="lbm_fp_id_country" class="col-md-3 form-control-label">
                        {l s='Country' mod='loginbymobile'}
                    </label>
                    <div class="col-md-6">
                        <select
                            class="form-control form-control-select js-country"
                            name="lbm_fp_id_country" id="lbm_fp_id_country">
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
		    id="lbm_fp_id_country"
                    name="lbm_fp_id_country"
                    value="{$sl_country}"/>
            {/if}


            <div class="form-group row">
                <label for="lbm_fp_mobile_number" class="col-md-3 form-control-label">
                    {l s='Mobile Number' mod='loginbymobile'}
                </label>
		<div class="col-md-6 lbm_mobile_div">
				<div class="input-group js-parent-focus">
                    <span id="lbm_fp_form_call_prefix">{$call_prefix}</span>
                    <input
                	type="number" pattern="\d*"
                        class="form-control"
                        id="lbm_fp_mobile_number"
                        name="lbm_fp_mobile_number"
                        maxlength="10" required/>
					<span class="input-group-btn">
					  <button
						class="btn"
						type="button"
						id="send_otp_fp_form"
						data-action="send_otp_fp_form"
						onclick="addResendOTPEvent_fpform(event);"
						data-text-show="{l s='Send OTP' mod='loginbymobile'}"
						data-text-hide="{l s='Re-Send OTP' mod='loginbymobile'}"
					  >
						{l s='Send OTP' mod='loginbymobile'}
					  </button>
					</span>
				</div>
				<span style="padding-top: 10px; display: none;" id="send_otp_success" >{l s='OTP Sms Sent Successfully' mod='loginbymobile'}</span>
		</div>
            </div>
		<input type="hidden" id="session_key" name="session_key" value="{$session_key}" />
		<div class="form-group row">
			<label for="lbm_fp_otp" class="col-md-3 form-control-label">
				{l s='OTP' mod='loginbymobile'}
			</label>
			<div class="col-md-6">
			  <div class="input-group js-parent-focus">
				<input
				  class="form-control js-child-focus js-visible-password"
				  id="lbm_fp_otp"
				  name="lbm_fp_otp"
				  type="number" pattern="\d*"
				  value="{if isset($lbm_fp_otp)}{$lbm_fp_otp}{/if}" required >
			  </div>
			</div>
		</div>
        </section>

        <footer class="form-footer text-sm-center clearfix">
            {*<button id="submit-fp-form-mobile" class="btn btn-primary" data-link-action="sign-in" type="submit" class="form-control-submit" onclick="fpByMobileNumber(event);">
            {l s='Send Password' mod='loginbymobile'}
			</button>*}
			
            <button id="submit-fp-form-mobile" type="submit" class="btn btn-primary" data-link-action="sign-in" class="form-control-submit">
            {l s='Send Password' mod='loginbymobile'}
			</button>			
			<div id='submit-fp-form-mobile-loading'><img src='
			{$lbmLoadingImgsBaseDir}img/loadingAnimation.gif'/></div>
        </footer>

    </form>
</section>