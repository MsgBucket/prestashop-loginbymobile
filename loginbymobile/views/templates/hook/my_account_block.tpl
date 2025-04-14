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

{if isset($link_module_lbm)}
<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="verify-mobile-link" href="{$link_module_lbm}">
  <span class="link-item">
	<i class="material-icons phonelink_setup">&#xe0de;</i>
	{l s='Register Mobile Number' mod='loginbymobile'}
  </span>
</a>
{/if}