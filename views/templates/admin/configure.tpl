{*
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2021
*  @license   Single domain
*}

<div class="panel">
    <h3><i class="icon icon-credit-card"></i> {l s='B2B Ecommerce' mod='b2becommerce'}</h3>
    <p>
        <strong>{l s='Here is features overview!' mod='b2becommerce'}</strong>
    </p>
    <ul>
        {foreach from=$mod_features item=feature}
            <ol><strong>{$feature.label|escape:'htmlall':'UTF-8'}</strong>: <span class="text-muted">{$feature.desc|escape:'htmlall':'UTF-8'}</span></ol>
        {/foreach}
    </ul>
</div>
