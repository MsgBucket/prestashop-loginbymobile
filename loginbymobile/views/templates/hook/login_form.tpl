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

<section id="lbm_lf_section" class="login-form">
    <div id="lbm_lf_error" class="alert alert-danger error" style="display: none;"></div>
	<div id="lbm_lf_title">
		<span>
		  {l s='(Or) Sign-in By Mobile Number' mod='loginbymobile'}
		</span>
	</div>
    <form id="lbm_login_form" method="post">
        <section>
            {if $countries|@count > 1}
                <div class="form-group row">
                    <label for="lbm_lf_id_country" class="col-md-3 form-control-label">
                        {l s='Country' mod='loginbymobile'}
                    </label>
                    <div class="col-md-6">
                        <select
                            class="form-control form-control-select js-country"
                            name="lbm_lf_id_country" id="lbm_lf_id_country">
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
		    id="lbm_lf_id_country"
                    name="lbm_lf_id_country"
                    value="{$sl_country}"/>
            {/if}
	    {if isset($back)}
	    	<input type="hidden" name="back" value="{$back}" />
	    {/if}	    

            <div class="form-group row">
                <label for="lbm_lf_mobile_number" class="col-md-3 form-control-label">
                    {l s='Mobile Number' mod='loginbymobile'}
                </label>
		<div class="col-md-6 tabledisplay">
                    <span id="lbm_reg_call_prefix" class="tablecelldisplay lbmcallprefix">{$call_prefix}</span>
                    <input
                	type="number" pattern="\d*"
                        class="form-control"
                        id="lbm_lf_mobile_number"
                        name="lbm_lf_mobile_number"
                        maxlength="10" required />
		</div>
            </div>

            <div class="form-group row">
                <label for="lbm_lf_password" class="col-md-3 form-control-label">
                    {l s='Password' mod='loginbymobile'}
                </label>
                <div class="col-md-6">
                  <div class="input-group js-parent-focus">
                    <input
                      class="form-control js-child-focus js-visible-password"
					  id="lbm_lf_password"
                      name="lbm_lf_password"
                      type="password"
                      value=""
                      pattern=".{literal}{{/literal}5,{literal}}{/literal}"
                       required >
                    <span class="input-group-btn">
                      <button
                        class="btn"
                        type="button"
                        data-action="show-password"
                        data-text-show="{l s='Show' mod='loginbymobile'}"
                        data-text-hide="{l s='Hide' mod='loginbymobile'}"
                      >
                        {l s='Show' mod='loginbymobile'}
                      </button>
                    </span>
                  </div>
                </div>
            </div>
			<div class="forgot-password">
				<a href="{$link_password_recovery}" rel="nofollow">{l s='Forgot your password?' mod='loginbymobile'}</a>
			</div>
        </section>

        <footer class="form-footer text-sm-center clearfix">
            {*<button id="submit-login-form-mobile" class="btn btn-primary" data-link-action="sign-in" type="submit" class="form-control-submit" onclick="signinByMobileNumberLF(event);">*}
            <button id="submit-login-form-mobile" class="btn btn-primary" data-link-action="sign-in" type="submit" class="form-control-submit" type="submit" >			
            {l s='Sign in with Mobile' mod='loginbymobile'}
			</button>
			<div id='submit-login-form-mobile-loading'><img src='
			{$lbmLoadingImgsBaseDir}img/loadingAnimation.gif'/></div>
        </footer>

    </form>
</section>
<hr>