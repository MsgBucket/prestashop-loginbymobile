{*
* Please do not edit or add any code in this file without the permission of MsgBucket
*
* @author    MsgBucket
* @copyright MsgBucket
* @license   http://www.MsgBucket.com
* Prestashop version 1.6+
* loginbymobile 6.4.4
* May 2017
*}

<form id="module_form_22" class="defaultForm form-horizontal trackdotzot" action="{$action|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
    <div class="panel" id="fieldset_0_2_2">
        <div class="panel-heading">
            <i class="icon-cogs"></i>{l s='Set the length of Mobile Number for every Country. Do NOT count the country call prefix code.' mod='loginbymobile'}
        </div>
        <div class="form-wrapper">
            <table id="payment_table" class="std">
                <thead>
                    <tr>
                        <th class="paymentlabel first_item col-lg-2">{l s='Country' mod='loginbymobile'}</th>
                        <th class="paymentlabel item col-lg-5">{l s='Minimum Mobile Length?' mod='loginbymobile'}</th>
                        <th class="paymentlabel item col-lg-5">{l s='Maximum Mobile Length?' mod='loginbymobile'}</th>
                        <th class="paymentlabel item col-lg-5">{l s='Transactional SMS?' mod='loginbymobile'}</th>
                        <th class="paymentlabel item col-lg-5">{l s='Enable OTP?' mod='loginbymobile'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$countrylists key="paymentlist_key" item="countrylist"}
                        <tr id="{$countrylist.id|escape:'htmlall':'UTF-8'}">
                            <td>
                                <div class="col-lg-12">
                                    <label class="control-label">
                                        {$countrylist.label|escape:'htmlall':'UTF-8'}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="col-lg-12">
                                    <input type="text" name="LOGINBYMOBILE_MOB_MIN_{$countrylist.id|escape:'htmlall':'UTF-8'}" id="LOGINBYMOBILE_MOB_MIN_{$countrylist.id|escape:'htmlall':'UTF-8'}" value="{$mobile_size_min_list[$countrylist.id|escape:'htmlall':'UTF-8']}" class="">
                                </div>
                            </td>
                            <td>
                                <div class="col-lg-12">
                                    <input type="text" name="LBMCOUNTRY_ID_{$countrylist.id|escape:'htmlall':'UTF-8'}" id="LBMCOUNTRY_ID_{$countrylist.id|escape:'htmlall':'UTF-8'}" value="{$mobilenum_length_list[$countrylist.id|escape:'htmlall':'UTF-8']}" class="">
                                </div>
                            </td>
                            <td>
                                <div class="col-lg-12">
                                    <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="LOGINBYMOBILE_TXN_{$countrylist.id|escape:'htmlall':'UTF-8'}" id="LOGINBYMOBILE_TXN_{$countrylist.id|escape:'htmlall':'UTF-8'}_on" value="1" {if $LOGINBYMOBILE_TXN_list[$countrylist.id|escape:'htmlall':'UTF-8'] > 0} checked="checked"{/if}/>
                                                {strip}
                                                <label for="LOGINBYMOBILE_TXN_{$countrylist.id|escape:'htmlall':'UTF-8'}_on">
                                                        {l s='Yes' mod='loginbymobile'}
                                                </label>
                                                {/strip}
                                            <input type="radio" name="LOGINBYMOBILE_TXN_{$countrylist.id|escape:'htmlall':'UTF-8'}" id="LOGINBYMOBILE_TXN_{$countrylist.id|escape:'htmlall':'UTF-8'}_off" value="0" {if $LOGINBYMOBILE_TXN_list[$countrylist.id|escape:'htmlall':'UTF-8'] == 0} checked="checked"{/if}/>
                                                {strip}
                                                <label for="LOGINBYMOBILE_TXN_{$countrylist.id|escape:'htmlall':'UTF-8'}_off">
                                                        {l s='No' mod='loginbymobile'}
                                                </label>
                                                {/strip}
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="col-lg-12">
                                    <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="LOGINBYMOBILE_OTP_{$countrylist.id|escape:'htmlall':'UTF-8'}" id="LOGINBYMOBILE_OTP_{$countrylist.id|escape:'htmlall':'UTF-8'}_on" value="1" {if $LOGINBYMOBILE_OTP_list[$countrylist.id|escape:'htmlall':'UTF-8'] > 0} checked="checked"{/if}/>
                                                {strip}
                                                <label for="LOGINBYMOBILE_OTP_{$countrylist.id|escape:'htmlall':'UTF-8'}_on">
                                                        {l s='Yes' mod='loginbymobile'}
                                                </label>
                                                {/strip}
                                            <input type="radio" name="LOGINBYMOBILE_OTP_{$countrylist.id|escape:'htmlall':'UTF-8'}" id="LOGINBYMOBILE_OTP_{$countrylist.id|escape:'htmlall':'UTF-8'}_off" value="0" {if $LOGINBYMOBILE_OTP_list[$countrylist.id|escape:'htmlall':'UTF-8'] == 0} checked="checked"{/if}/>
                                                {strip}
                                                <label for="LOGINBYMOBILE_OTP_{$countrylist.id|escape:'htmlall':'UTF-8'}_off">
                                                        {l s='No' mod='loginbymobile'}
                                                </label>
                                                {/strip}
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                        <tr>
                            <td colspan=3>
                                <div class="col-lg-12">
                                    <p class="help-block">
                                        {l s='Set "0" if validation is not required' mod='loginbymobile'}
                                    </p>
                                </div>
                            </td>
                        </tr>
                </tbody>
            </table>
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Allow Customers Login by Mobile Number' mod='loginbymobile'}
                </label>
                <div class="col-lg-9 ">
                    <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="LOGINBYMOBILE_ENABLE_LOGIN_FEATURE" id="LOGINBYMOBILE_ENABLE_LOGIN_FEATURE_on" value="1" {if $LOGINBYMOBILE_ENABLE_LOGIN_FEATURE > 0} checked="checked"{/if}/>
                                {strip}
                                <label for="LOGINBYMOBILE_ENABLE_LOGIN_FEATURE_on">
                                        {l s='Yes' mod='loginbymobile'}
                                </label>
                                {/strip}
                            <input type="radio" name="LOGINBYMOBILE_ENABLE_LOGIN_FEATURE" id="LOGINBYMOBILE_ENABLE_LOGIN_FEATURE_off" value="0" {if $LOGINBYMOBILE_ENABLE_LOGIN_FEATURE == 0} checked="checked"{/if}/>
                                {strip}
                                <label for="LOGINBYMOBILE_ENABLE_LOGIN_FEATURE_off">
                                        {l s='No' mod='loginbymobile'}
                                </label>
                                {/strip}
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Allow Customers Login by Email' mod='loginbymobile'}
                </label>
                <div class="col-lg-9 ">
                    <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN" id="LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN_on" value="1" {if $LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN > 0} checked="checked"{/if}/>
                                {strip}
                                <label for="LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN_on">
                                        {l s='Yes' mod='loginbymobile'}
                                </label>
                                {/strip}
                            <input type="radio" name="LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN" id="LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN_off" value="0" {if $LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN == 0} checked="checked"{/if}/>
                                {strip}
                                <label for="LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN_off">
                                        {l s='No' mod='loginbymobile'}
                                </label>
                                {/strip}
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>			
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Mobile number required for Customer account registration' mod='loginbymobile'}
                </label>
                <div class="col-lg-9 ">
                    <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="LOGINBYMOBILE_MOBILE_REG" id="LOGINBYMOBILE_MOBILE_REG_on" value="1" {if $LOGINBYMOBILE_MOBILE_REG > 0} checked="checked"{/if}/>
                                {strip}
                                <label for="LOGINBYMOBILE_MOBILE_REG_on">
                                        {l s='Yes' mod='loginbymobile'}
                                </label>
                                {/strip}
                            <input type="radio" name="LOGINBYMOBILE_MOBILE_REG" id="LOGINBYMOBILE_MOBILE_REG_off" value="0" {if $LOGINBYMOBILE_MOBILE_REG == 0} checked="checked"{/if}/>
                                {strip}
                                <label for="LOGINBYMOBILE_MOBILE_REG_off">
                                        {l s='No' mod='loginbymobile'}
                                </label>
                                {/strip}
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Accept Zero as Prefix in Mobile Number' mod='loginbymobile'}
                </label>
                <div class="col-lg-9 ">
                    <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="LOGINBYMOBILE_ACCEPTZERO" id="LOGINBYMOBILE_ACCEPTZERO_on" value="1" {if $LOGINBYMOBILE_ACCEPTZERO > 0} checked="checked"{/if}/>
                                {strip}
                                <label for="LOGINBYMOBILE_ACCEPTZERO_on">
                                        {l s='Yes' mod='loginbymobile'}
                                </label>
                                {/strip}
                            <input type="radio" name="LOGINBYMOBILE_ACCEPTZERO" id="LOGINBYMOBILE_ACCEPTZERO_off" value="0" {if $LOGINBYMOBILE_ACCEPTZERO == 0} checked="checked"{/if}/>
                                {strip}
                                <label for="LOGINBYMOBILE_ACCEPTZERO_off">
                                        {l s='No' mod='loginbymobile'}
                                </label>
                                {/strip}
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>			
        </div>
        <div class="panel-footer">
            <button type="submit" value="1" id="savemblengthsubmit_btn" name="savemblength" class="button pull-right"> <i class="process-icon-save"></i> Save </button>
        </div>
    </div>
</form>