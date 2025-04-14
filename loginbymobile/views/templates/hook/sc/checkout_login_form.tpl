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
<input type="hidden" id="back" name="back" value="order"/>