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

<script type="text/javascript">
  var labels = { 
    disable: "{l s='Do you really want to disable this feature?' mod='b2becommerce' js=1}",
    uninstall: "{l s='Do you really want to uninstall this feature?' mod='b2becommerce' js=1}"
  }
</script>
<div class="btn-group-action">
    <div{if !$ps_new} class="btn-group"{/if}>
        <a href="javascript:;" title="{$modDetail.label|escape:'htmlall':'UTF-8'}" class="btn {if $mod_install_status AND $mod_status}btn-success{elseif $mod_install_status AND !$mod_status}btn-warning{elseif !$mod_install_status AND !$mod_status}btn-danger{else}btn-default{/if}{if $ps_new} btn-group{/if}">
          <i class="icon icon-{$modDetail.icon|escape:'htmlall':'UTF-8'}"></i> {$modDetail.label|escape:'htmlall':'UTF-8'}
        </a>
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
          <i class="icon-caret-down"></i>&nbsp;
        </button>
        <ul class="dropdown-menu">
          {if $mod_install_status eq 1}
            <li>
                <a href="javascript:void(0);"
                  class="feature_action"
                  data-action="{$mod_action_path|escape:'htmlall':'UTF-8'}&mod={$mod|escape:'htmlall':'UTF-8'}&b2b_action={if $mod_status eq 1}disableFeature{else}enableFeature{/if}"
                  value="{$mod|escape:'htmlall':'UTF-8'}"
                  id="submit_btn_{$mod|escape:'htmlall':'UTF-8'}"
                  {if $mod_status eq 1}data-action-type="disable"{else}data-action-type="enable"{/if}>
                  {if $mod_status eq 1}
                    <i class="icon icon-off"></i> {l s='Disable Feature' mod='b2becommerce'}
                  {else}
                    <i class="icon icon-off"></i> {l s='Enable Feature' mod='b2becommerce'}
                  {/if}
                </a>
            </li>
            <li>
                <a href="javascript:void(0);"
                  class="feature_action"
                  data-action-type="uninstall"
                  data-action="{$mod_action_path|escape:'htmlall':'UTF-8'}&mod={$mod|escape:'htmlall':'UTF-8'}&b2b_action=uninstallFeature"
                  value="{$mod|escape:'htmlall':'UTF-8'}"
                  id="submit_btn_{$mod|escape:'htmlall':'UTF-8'}">
                  <i class="icon-minus-sign-alt"></i> {l s='Uninstall Feature' mod='b2becommerce'}
                </a>
            </li>
            {if $modDetail.upgrade} 
              <li style="background: #FBBB22;">
                  <a href="javascript:void(0);"
                    class="feature_action"
                    data-action-type="upgrade"
                    data-action="{$mod_action_path|escape:'htmlall':'UTF-8'}&mod={$mod|escape:'htmlall':'UTF-8'}&b2b_action=upgradeFeature"
                    value="{$mod|escape:'htmlall':'UTF-8'}"
                    id="submit_btn_{$mod|escape:'htmlall':'UTF-8'}">
                    <i class="icon-refresh"></i> {l s='Upgrade Feature' mod='b2becommerce'}
                  </a>
              </li>
            {/if}
          {else}
            <li>
                <a href="javascript:void(0);"
                  class="feature_action"
                  data-action-type="install"
                  data-action="{$mod_action_path|escape:'htmlall':'UTF-8'}&mod={$mod|escape:'htmlall':'UTF-8'}&b2b_action=installFeature"
                  value="{$mod|escape:'htmlall':'UTF-8'}"
                  id="submit_btn_{$mod|escape:'htmlall':'UTF-8'}">
                  <i class="icon-plus-sign-alt"></i> {l s='Install Feature' mod='b2becommerce'}
                </a>
            </li>
          {/if}
          <li class="divider"></li>
          <li>
              <a href="javascript:void(0);">
                <i class="icon icon-info-circle"> </i> <strong>{l s='version' mod='b2becommerce'} {$mod_version|escape:'htmlall':'UTF-8'}</strong>
              </a>
          </li>
        </ul>
    </div>
</div>

{if $modDetail.upgrade}
  <br>
  <div class="col-lg-6">
    <p class="alert alert-warning">
      {l s='A newer version is available for upgrade.' mod='b2becommerce'}&nbsp;
      <a href="javascript:void(0);"
      class="feature_action btn btn-info"
      data-action-type="upgrade"
      data-action="{$mod_action_path|escape:'htmlall':'UTF-8'}&mod={$mod|escape:'htmlall':'UTF-8'}&b2b_action=upgradeFeature"
      value="{$mod|escape:'htmlall':'UTF-8'}"
      id="submit_btn_{$mod|escape:'htmlall':'UTF-8'}">
      <i class="icon-refresh"></i> {l s='Upgrade Now' mod='b2becommerce'}
    </a>
    </p>
  </div>
  <span class="clearfix"></span>
{/if}