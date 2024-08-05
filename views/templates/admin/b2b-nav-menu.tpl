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

<li id="tab-ecommerce-nav">
    <section class="nav-wrap">
        <!-- start accordion nav block -->
        <nav class="acnav" role="navigation">
            <!-- start level 1 -->
            <ul class="acnav__list acnav__list--level1">
                <!-- start group 1 -->
                <li class="has-children">
                    <div class="acnav__label b2b_main link-levelone">
                      <i class="icon-{$b2b_nav.main.icon|escape:'htmlall':'UTF-8'}"></i>
                      <span>{$b2b_nav.main.label|escape:'htmlall':'UTF-8'}</span>
                    </div>
                    <!-- start level 2 -->
                    <ul class="acnav__list acnav__list--level2">
                        <li>
                          <a class="acnav__link acnav__link--level2 link-levelone" href="{$b2b_nav.home.link|escape:'htmlall':'UTF-8'}">
                            <i class="icon-{$b2b_nav.home.icon|escape:'htmlall':'UTF-8'}"></i>
                            <span>{$b2b_nav.home.label|escape:'htmlall':'UTF-8'}</span>
                          </a>
                        </li>
                        {if isset($b2b_nav.child) AND $b2b_nav.child}
                            {foreach from=$b2b_nav.child item=child key=index}
                                <li class="has-children link-levelone maintab {if $child.has_submenu}has_submenu{/if}">
                                    <div class="acnav__label acnav__label--level2"
                                      {if $child.has_submenu}data-submenu="b2b-{$index|escape:'htmlall':'UTF-8'}"{/if}>
                                      <i class="icon-{$child.icon|escape:'htmlall':'UTF-8'}"></i>
                                      <span>{$child.label|escape:'htmlall':'UTF-8'}</span>
                                    </div>
                                    {if $child.has_submenu}
                                      <ul id="collapse-b2b-{$index}" class="submenu panel-collapse acnav__list acnav__list--level3 {if $ps_new}ps_17{else}ps_16{/if}">
                                          {if isset($child.submenu) AND $child.submenu}
                                            {foreach from=$child.submenu item=submenu}
                                            <li>
                                              <a class="acnav__link acnav__link--level3"
                                              href="{$submenu.sub_menu_link|escape:'htmlall':'UTF-8'}">
                                                {$submenu.label|escape:'htmlall':'UTF-8'}
                                              </a>
                                            </li>
                                            {/foreach}
                                          {/if}
                                      </ul>
                                    {/if}
                                </li>
                            {/foreach}
                        {/if}
                    </ul>
                </li>
            </ul>
            <!-- end level 1 -->
        </nav>
        <!-- end accordion nav block -->
    </section>
</li>

<script type="text/javascript">
  var ps_new = parseInt("{$ps_new|intval|escape:'htmlall':'UTF-8'}");
  var confirm_disable_label = "{l s='Are you sure you want to Disable this feature' mod='b2becommerce' js=1}?";
  if ($('#tab-AdminDashboard, #maintab-AdminDashboard').length) {
    $('#tab-AdminDashboard, #maintab-AdminDashboard').after($('#tab-ecommerce-nav'));
  } else {
    $('#nav-sidebar').find('ul.main-menu').prepend($('#tab-ecommerce-nav'));
  }

  $(document).on('click', '.acnav__label', function () {
    var label = $(this);
    if (!ps_new) {
      if (!label.hasClass('acnav__label--level2') ||
        (label.hasClass('acnav__label--level2') && $('.page-sidebar').hasClass('mobile-nav')) ||
        $('.page-sidebar').hasClass('page-sidebar-closed')) {
      console.log($('.page-sidebar').hasClass('mobile-nav'))
        navAccordion(label);
      }
    } else {
      navAccordion(label);
    }
  });

  $(document).on('click', '.confirm_disable', function(event) {
    var confirmStatus = parseInt($(this).attr('data-confirm'));
    if (!confirmStatus) {
      event.preventDefault();
      var isConfirmed = confirm(confirm_disable_label);

      if (isConfirmed) {
        $(this).attr('data-confirm', 1);
        $(this).trigger('click');
      }
    }
  });

  function navAccordion(label) {
    var parent = label.parent('.has-children');
    var list = label.siblings('.acnav__list');

    if ( parent.hasClass('is-open') ) {
      list.slideUp('fast');
      parent.removeClass('is-open');
    } else {
      list.slideDown('fast');
      parent.addClass('is-open');
    }
  }
</script>
{literal}
<style type="text/css">
#tab-ecommerce-nav a {
  text-decoration: none;
}
#tab-ecommerce-nav a:hover {
  text-decoration: underline;
}
#tab-ecommerce-nav .nav-wrap {
  width: 100%;
  margin: 1em auto 0;
}
#tab-ecommerce-nav .acnav {
  width: 100%;
}
#tab-ecommerce-nav .acnav__list {
  padding: 0;
  margin: 0;
  list-style: none;
}
#tab-ecommerce-nav .acnav__list--level1 {
  border-top: 1px;
  border-bottom: 1px;
  border-style: solid;
  border-color: #888;
  border-left: 0px;
  border-right: 0px;
}
#tab-ecommerce-nav .has-children.is-open .b2b_main.link-levelone > span {
  color: #25B9D7;
}
#tab-ecommerce-nav .has-children > .acnav__label::before {
  display: inline-block;
  font-family: FontAwesome
  font-size: inherit;
  text-rendering: auto;
  transition: transform .3s;
}
#tab-ecommerce-nav .acnav__link,
#tab-ecommerce-nav .acnav__label {
  display: block;
  padding: 1em;
  padding-left: 1.5em;
  margin: 0;
  cursor: pointer;
  color: #fcfcfc;
  background: #363A41;
  box-shadow: inset 0 -1px #999;
  transition: color .25s ease-in, background-color .25s ease-in;
}
#tab-ecommerce-nav .acnav__link:focus,
#tab-ecommerce-nav .acnav__link:hover,
#tab-ecommerce-nav .acnav__label:focus,
#tab-ecommerce-nav .acnav__label:hover {
  color: #e3e3e3;
  background: #202226;
}
#tab-ecommerce-nav .acnav__link--level2,
#tab-ecommerce-nav .acnav__label--level2 {
  padding-left: 2em;
  background: #202226;
}
#tab-ecommerce-nav .acnav__link--level2:focus,
#tab-ecommerce-nav .acnav__link--level2:hover,
#tab-ecommerce-nav .acnav__label--level2:focus,
#tab-ecommerce-nav .acnav__label--level2:hover {
  background: #202226;
}
#tab-ecommerce-nav .acnav__link--level3,
#tab-ecommerce-nav .acnav__label--level3 {
  padding-left: 3em;
  background: #202226;
}
#tab-ecommerce-nav .acnav__link--level3:focus,
#tab-ecommerce-nav .acnav__link--level3:hover,
#tab-ecommerce-nav .acnav__label--level3:focus,
#tab-ecommerce-nav .acnav__label--level3:hover {
  background: #202226;
}
#tab-ecommerce-nav .acnav__link--level4,
#tab-ecommerce-nav .acnav__label--level4 {
  padding-left: 7em;
  background: #202226;
}
#tab-ecommerce-nav .acnav__link--level4:focus,
#tab-ecommerce-nav .acnav__link--level4:hover,
#tab-ecommerce-nav .acnav__label--level4:focus,
#tab-ecommerce-nav .acnav__label--level4:hover {
  background: #202226;
}
#tab-ecommerce-nav .acnav__list--level2,
#tab-ecommerce-nav .acnav__list--level3,
#tab-ecommerce-nav .acnav__list--level4 {
  display: none;
}
#tab-ecommerce-nav .is-open > .acnav__list--level2,
#tab-ecommerce-nav .is-open > .acnav__list--level3,
#tab-ecommerce-nav .is-open > .acnav__list--level4 {
  display: block;
}

.page-sidebar.page-sidebar-closed #tab-ecommerce-nav ul li.has-children ul.submenu,
.page-sidebar.page-sidebar-closed #tab-ecommerce-nav ul li.has-children ul li .acnav__link span,
.page-sidebar.page-sidebar-closed #tab-ecommerce-nav ul li.has-children .acnav__label span {
  display: none!important;
}
/*.page-sidebar.page-sidebar-closed #tab-ecommerce-nav ul li.has-children ul.submenu.panel-collapse.acnav__list.acnav__list--level3.ps_16 {
  display: none!important;
}*/

.page-sidebar.page-sidebar-closed.mobile-nav #tab-ecommerce-nav ul li.has-children.link-levelone.has_submenu.hover.is-open ul.submenu.ps_16 {
  display: block!important;
}
.page-sidebar.page-sidebar-closed #tab-ecommerce-nav ul li.has-children.link-levelone.has_submenu.-hover ul.submenu.ps_17 {
  top: -10px !important;
  height: 0px;
  display: block!important;
}

.page-sidebar.page-sidebar-closed #nav-sidebar ul.menu li.maintab.hover ul.submenu.ps_16 {
  position: absolute;
  display: block!important;
  width: 200px;
  top: 0;
  z-index: 600;
  left: 50px;
}

@media only screen and (max-width: 480px) {
  .page-sidebar.page-sidebar-closed #tab-ecommerce-nav ul li.has-children ul.submenu,
  .page-sidebar.page-sidebar-closed #tab-ecommerce-nav ul li.has-children ul li .acnav__link span,
  .page-sidebar.page-sidebar-closed #tab-ecommerce-nav ul li.has-children .acnav__label span {
    display: block!important;
  }
}
</style>
{/literal}