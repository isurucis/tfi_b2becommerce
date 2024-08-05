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

{if isset($smarty.get.st)}
    <p class="alert alert-success success">
        {if $smarty.get.st eq 1}
            {l s='Feature enabled successfully.' mod='b2becommerce'}
        {elseif $smarty.get.st eq 2}
            {l s='Feature disabled successfully.' mod='b2becommerce'}
        {elseif $smarty.get.st eq 3}
            {l s='Feature installed successfully.' mod='b2becommerce'}
        {elseif $smarty.get.st eq 4}
            {l s='Feature uninstalled successfully.' mod='b2becommerce'}
        {/if}
    </p>
{/if}

<div id="fme-nav-wrap">
    <div id="fme-nav-menu">
        <ul>
            <a class="nav-link b2becommerce-page home"
            id="b2becommerce_home"
            href="{$b2b_nav.home.link|escape:'htmlall':'UTF-8'}">
                <li class="inner-nav">
                    <i class="icon-{$b2b_nav.home.icon|escape:'htmlall':'UTF-8'}"></i> {$b2b_nav.home.label|escape:'htmlall':'UTF-8'}
                </li>
            </a>
            |
            {if isset($b2b_nav.child) AND $b2b_nav.child}
                {foreach from=$b2b_nav.child item=child key=index}
                    <span class="nav-link">
                        <li id="configuration" class="inner-nav config">
                            <i class="icon-{$child.icon|escape:'htmlall':'UTF-8'}"></i> {$child.label|escape:'htmlall':'UTF-8'}
                            {if $child.has_submenu}
                                <ul class="b2becommerce">
                                    {if isset($child.submenu) AND $child.submenu}
                                        {foreach from=$child.submenu item=submenu}
                                        <a class="b2becommerce-page"
                                        id="b2becommerce_{$submenu.id|escape:'htmlall':'UTF-8'}"
                                        href="{$submenu.sub_menu_link|escape:'htmlall':'UTF-8'}">
                                            <li class="nav-row">{$submenu.label|escape:'htmlall':'UTF-8'}</li>
                                        </a>
                                        {/foreach}
                                    {/if}
                                </ul>
                            {/if}
                        </li>
                    </span>
                {/foreach}
            {/if}
        </ul>
    </div>
</div>

{literal}
<style type="text/css">
    .selected-nav {
        background: #3f485b none repeat scroll 0 0;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
        color: #fff!important;
        opacity: 1;
    }
    #fme-nav-wrap {
        margin-bottom: 15px;
    }
    #fme-nav-menu {
        background: #fff none repeat scroll 0 0;
        border-radius: 4px;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
    }
    #fme-nav-menu a.home li {
        color: #00AFF0;
    }
    #fme-nav-wrap a:link {
        color: #4a4a4a;
        text-decoration: none;
    }
    #fme-nav-wrap a:visited {
        color: inherit;
        text-decoration: none;
    }
    #fme-nav-wrap a:hover {
        text-decoration: none;
    }
    #fme-nav-wrap a:active {
        text-decoration: none;
    }
    #fme-nav-wrap ul{
        display: inline;
        list-style: outside none none;
        margin: 0;
        padding: 15px 4px 17px 0;
        text-align: left;
    }
    #fme-nav-wrap ul li {
        color: #4a4a4a;
        cursor: pointer;
        display: inline-block;
        font: bold 12px/18px sans-serif;
        margin-right: -4px;
        padding: 15px 10px;
        position: relative;
    }
    #fme-nav-wrap ul li:hover {
        background: #282B30 none repeat scroll 0 0;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
        color: #ddd;
        opacity: 1;
    }
    #fme-nav-wrap ul li ul {
        box-shadow: none;
        display: none;
        left: 0;
        opacity: 0;
        padding: 0;
        position: absolute;
        top: 48px;
        /*visibility: hidden;*/
        width: 225px;
    }
    #fme-nav-wrap ul li ul li {
        background: #363A41 none repeat scroll 0 0;
        color: #ccc;
        display: block;
        z-index: 999;
    }
    #fme-nav-wrap ul li ul li:hover,  #fme-nav-wrap ul li ul > a.selected li {
        background: #2eacce none repeat scroll 0 0;
        color: #fff;
        z-index: 999;
    }
    #fme-nav-wrap ul li:hover ul {
        display: block;
        opacity: 1;
    }
    #affilate-dashboard .af-icons {
        font-size: 100px;
    }
    #affilate-dashboard a:hover {
        color: #32cd32;
    }
</style>
{/literal}
