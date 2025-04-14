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

<script type="text/javascript">
    var mobilenum_length = [];
    {foreach from=$mobilenum_length item="length" key="country_id"}
        {if $length == 0 }
            mobilenum_length[{$country_id}] = 20;
        {else}
            mobilenum_length[{$country_id}] = {$length};
        {/if}
    {/foreach}

    var mobile_size_min_list = [];
    {foreach from=$mobile_size_min_list item="length" key="country_id"}
        {if $length == 0 }
            mobile_size_min_list[{$country_id}] = 2;
        {else}
            mobile_size_min_list[{$country_id}] = {$length};
        {/if}
    {/foreach}

	var country_call_prefix_list = [];
	var country_name_list = [];
    {foreach from=$countries item="country" key="count"}
        country_call_prefix_list[{$country.id_country}] = {$country.call_prefix};
        country_name_list[{$country.id_country}] = '{$country.name}';
    {/foreach}

	var default_country = {$sl_country};

    var LOGINBYMOBILE_OTP_list = [];
    {foreach from=$LOGINBYMOBILE_OTP_list item="otp" key="country_id"}
        LOGINBYMOBILE_OTP_list[{$country_id}] = {$otp};
    {/foreach}
	var default_country_otp_eligible = LOGINBYMOBILE_OTP_list[default_country];
    var LOGINBYMOBILE_OTP_TIMEINTERVAL = {$LOGINBYMOBILE_OTP_TIMEINTERVAL};

	/* Enable Mobile Login Feature */
    var LOGINBYMOBILE_ENABLE_LOGIN_FEATURE = {$LOGINBYMOBILE_ENABLE_LOGIN_FEATURE};

	/* Enable Mobile Registration while new account */
    var LOGINBYMOBILE_MOBILE_REG = "{$LOGINBYMOBILE_MOBILE_REG}";

	var LOGINBYMOBILE_EMAIL_REQUIRED = "{$LOGINBYMOBILE_EMAIL_REQUIRED}";

	/* Enable Two Layer Security - Sends SMS OTP during login and needs to be confirmed*/
	var LOGINBYMOBILE_TWOWAYFACTOR = "{$LOGINBYMOBILE_TWOWAYFACTOR}";

	/* Display email id in the Second page of Registration. v1.6 */
	var LOGINBYMOBILE_DISP_MAIL_AC_PAGE = '{$LOGINBYMOBILE_DISP_MAIL_AC_PAGE}';

	/* Accept Zero in front of the Mobile Number */
	var LOGINBYMOBILE_ACCEPTZERO = '{$LOGINBYMOBILE_ACCEPTZERO}';

	/* Enable Login by Email Id */
	var LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN = '{$LOGINBYMOBILE_ENABLE_EMAIL_SIGNIN}';
	
	var LOGINBYMOBILE_MAIL_DOMAIN = '{$LOGINBYMOBILE_MAIL_DOMAIN}';

    var lbmajax = "{$lbmajax}";

    var twowayfactoraction = "{$twowayfactoraction}";

	var lbmLoadingImgsBaseDir = "{$lbmLoadingImgsBaseDir}";

	var lbm_back = '{$lbm_back}';

	var orig_back = '{$orig_back}';

	var call_prefix = '{$call_prefix}';

	var ajaxotp = 1;

	var ajaxcreateotp = 1;

	var tfotpcount = 1;

    var otp_sent_yes = "{l s='We have sent OTP SMS to your registered Mobile Number. Kindly wait for ' mod='loginbymobile'}" + LOGINBYMOBILE_OTP_TIMEINTERVAL + "{l s=' seconds to request OTP SMS again.' mod='loginbymobile'}";

    var otp_sent_no = "{l s='Kindly click SEND OTP button to get OTP SMS to your registered Mobile Number.' mod='loginbymobile'}";

    var mobile_min_size_msg = "{l s='Mobile number is too short. Enter valid mobile number' mod='loginbymobile'}";

    var txtThereis = "{l s='There is' mod='loginbymobile'}";

    var timeIntervalMsg = "{l s='SMS sent. You shall resend OTP after ' mod='loginbymobile'}" + LOGINBYMOBILE_OTP_TIMEINTERVAL + "{l s=' seconds' mod='loginbymobile'}";

	var zeroprefixmsg = "{l s='0 - Prefix is not required. Directly enter your phone number' mod='loginbymobile'}";

	var live_otp_time_count = 0;

	var lbm_otp_lbm_otp_counter = null;
	
</script>