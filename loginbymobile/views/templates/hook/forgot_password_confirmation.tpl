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

<section id="lbm_fp_conf_section" class="login-form alert alert-success">
	<header>
      <p class="send-renew-password-link">{l s='Password has been sent to your registered phone number ' mod='loginbymobile'}{$lbm_fp_mobile_number}({$lbm_fp_country_name}){l s='. You will be redirected to the Login Page now.' mod='loginbymobile'}</p>
    </header>

	<footer class="form-footer text-sm-center clearfix">
	<ul>
    <li><a href="{$lbm_fp_lf_url nofilter}">{l s='Back to Login' mod='loginbymobile'}</a></li>
	</ul>
	</footer>
</section>
