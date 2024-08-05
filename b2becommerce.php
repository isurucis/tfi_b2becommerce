<?php
/**
 * DISCLAIMER.
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2023
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class B2becommerce extends Module
{
    public $b2bNav = [];

    protected $ajax = false;

    protected $b2bModules = [];

    protected $menuControllers = [];

    public function __construct()
    {
        $this->name = 'b2becommerce';
        $this->tab = 'front_office_features';
        $this->version = '2.1.0';
        $this->author = 'FMM Modules';
        $this->need_instance = 1;

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();
        $this->module_key = 'c419eb34b33310edb8f198d6adbbb446';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';
        $this->displayName = $this->l('B2B Ecommerce');
        $this->description = $this->l('
            This module enables the powerful feature of Business to Business Ecommerce in your shop.
        ');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall my module?');

        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];

        $this->menuControllers = [
            'AdminB2BCustomers',
            'AdminB2BCustomFields',
            'AdminB2BProfiles',
            'AdminQuoteFields',
            'AdminProductQuotes',
            'AdminProductQuotation',
            'AdminQuotationMessages',
            'AdminRestrictCustomerGroup',
            'AdminRestrictPaymentMethod',
        ];
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update.
     */
    public function install()
    {
        return parent::install() && $this->registerHook([
            'displayAdminListBefore',
            'displayBackOfficeHeader',
            'displayAdminNavBarBeforeEnd',
            'actionAdminControllerInitAfter',
        ]);
    }

    public function uninstall()
    {
        if (parent::uninstall()) {
            $this->removeFeatureModules();

            return true;
        }

        return false;
    }

    public function disable($force_all = false)
    {
        $result = parent::disable($force_all);
        if ($result || true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            $result &= $this->processDisableAll();
        }

        return $result;
    }

    public function removeFeatureModules()
    {
        $result = true;
        set_time_limit(0);
        foreach (array_keys($this->b2bModules) as $module) {
            if (Validate::isLoadedObject($moduleInstance = Module::getInstanceByName($module))) {
                if (Module::isInstalled($moduleInstance->name)) {
                    $moduleInstance->uninstall();
                }
                $moduleDir = _PS_MODULE_DIR_ . str_replace(['.', '/', '\\'], ['', '', ''], $module);
                $this->recursiveDeleteDir($moduleDir);
                if (!file_exists($moduleDir)) {
                    $result &= true;
                } else {
                    $result &= false;
                }
            }
        }

        return $result;
    }

    /**
     * Load the configuration form.
     */
    public function getContent()
    {
        if (!$this->active) {
            $this->context->controller->informations[] = $this->l('Enable module to see all B2B features.');
        } else {
            $this->ajax = (bool) Tools::getValue('ajax');

            $output = $this->getB2bMenu();

            $output .= $this->display(__FILE__, 'views/templates/admin/info.tpl');

            $this->context->smarty->assign([
                'mod_features' => $this->b2bModules,
                'ps_new' => (int) (Tools::version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? true : false),
            ]);

            $output .= $this->context->smarty->fetch(
                $this->local_path . 'views/templates/admin/configure.tpl'
            );

            return $output . $this->renderForm();
        }
    }

    public function hookActionAdminControllerInitAfter()
    {
        $this->initB2bMod();
    }

    public function ajaxProcessB2bFeature()
    {
        exit(json_encode(
            $this->processB2bFeature(Tools::getValue('b2b_action'))
        ));
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitB2becommerceModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $formFields = [
            'form' => [
                'legend' => [
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ],
            ],
        ];

        foreach ($this->b2bModules as $mod => $label) {
            $modStatus = (Module::isEnabled($mod)) ? true : false;
            $modInstallStatus = (Module::isInstalled($mod)) ? true : false;
            $this->context->smarty->assign([
                'mod' => $mod,
                'modDetail' => $label,
                'mod_status' => (int) $modStatus,
                'mod_version' => $this->getModuleVersion($mod),
                'mod_install_status' => (int) $modInstallStatus,
                'mod_action_path' => $this->getModuleConfigLink($this),
            ]);

            $formFields['form']['input'][] = [
                'type' => 'html',
                'name' => 'B2BECOMMERCE_MODULE_' . $mod,
                'html_content' => $this->context->smarty->fetch(
                    $this->local_path . 'views/templates/admin/module-button.tpl'
                ),
                'desc' => $label['desc'],
            ];
        }

        return $formFields;
    }

    protected function processB2bFeature($b2bAction)
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        $module = Tools::getValue('mod');
        $moduleInstance = Module::getInstanceByName($module);
        $response = ['success' => false, 'msg' => $this->l('Error: request unsuccessful')];
        $featureDisplayName = $moduleInstance->displayName;

        try {
            switch ($b2bAction) {
                case 'enableFeature':
                    if (!$moduleInstance->enable(true)) {
                        $response['msg'] = sprintf(
                            $this->l('Feature %s cannot be enabled now. Please try later or check your errors log.'),
                            $featureDisplayName
                        );
                    } else {
                        $response['success'] = true;
                        $response['msg'] = sprintf(
                            $this->l('Feature %s enabled successfully.'),
                            $featureDisplayName
                        );
                    }
                    break;
                case 'disableFeature':
                    if ($moduleInstance->active) {
                        if (!$moduleInstance->disable()) {
                            $response['msg'] = sprintf(
                                $this->l('Feature %s cannot be disabled now. Please try later or check your errors log.'),
                                $featureDisplayName
                            );
                        } else {
                            $response['success'] = true;
                            $response['msg'] = sprintf(
                                $this->l('Feature %s disabled successfully.'),
                                $featureDisplayName
                            );
                        }
                    }
                    break;
                case 'installFeature':
                    $result = true;
                    if (false === $moduleInstance) {
                        $result &= $this->installB2BModules($module);
                        $moduleInstance = Module::getInstanceByName($module);
                    } else {
                        $result &= $moduleInstance->install();
                    }

                    if (!$result) {
                        $response['msg'] = sprintf(
                            $this->l('Feature %s cannot be installed now. Please try later or check your errors log.'),
                            $moduleInstance->displayName
                        );
                    } else {
                        $response['success'] = true;
                        $response['msg'] = sprintf(
                            $this->l('Feature %s installed successfully.'),
                            $featureDisplayName
                        );
                    }
                    break;
                case 'uninstallFeature':
                    $displayName = $moduleInstance->displayName;
                    if (!$moduleInstance->uninstall()) {
                        $response['msg'] = sprintf(
                            $this->l('Feature %s cannot uninstall now. Please try later or check your errors log.'),
                            $displayName
                        );
                    } else {
                        $moduleDir = _PS_MODULE_DIR_ . str_replace(['.', '/', '\\'], ['', '', ''], $module);
                        $this->recursiveDeleteDir($moduleDir);
                        if (!file_exists($moduleDir)) {
                            $response['success'] = true;
                            $response['msg'] = sprintf(
                                $this->l('Feature %s uninstalled successfully.'),
                                $featureDisplayName
                            );
                        } else {
                            $response['msg'] = sprintf(
                                $this->l('Check folder permissions to completely uninstall feature : %s'),
                                $displayName
                            );
                        }
                    }
                    break;
                case 'upgradeFeature':
                    $success = false;
                    $file = $this->local_path . 'packages/' . $module . '.zip';
                    $tmp_folder = $this->local_path . 'packages/' . md5(time());
                    $msg = sprintf(
                        $this->l('Something went wrong while upgrading %s.'),
                        $featureDisplayName
                    );
                    if (Tools::substr($file, -4) == '.zip') {
                        if (Tools::ZipExtract($file, $tmp_folder)) {
                            if (Tools::ZipExtract($file, _PS_MODULE_DIR_)) {
                                $success = true;
                                $msg = sprintf(
                                    $this->l('Feature %s upgraded successfully.'),
                                    $featureDisplayName
                                );
                            }
                        }
                    }
                    $this->recursiveDeleteDir($tmp_folder);

                    $response['success'] = $success;
                    $response['msg'] = $msg;
                    break;
            }
            $this->flushCache();
        } catch (RuntimeException $e) {
            http_response_code(400);

            $response['success'] = false;
            $response['msg'] = $e->getMessage();
        }

        return $response;
    }

    protected function processDisableAll()
    {
        $result = true;
        foreach (array_keys($this->b2bModules) as $module) {
            if (Validate::isLoadedObject($moduleInstance = Module::getInstanceByName($module))) {
                if ($moduleInstance->active) {
                    $result &= $moduleInstance->disable();
                }
            }
        }

        return $result;
    }

    public function getModuleConfigLink($module, $params = [])
    {
        if (false === ($module instanceof Module)) {
            return false;
        }

        $modParams = [
            'configure' => $module->name,
            'tab_module' => $module->tab,
            'module_name' => $module->name,
        ];

        $params = (isset($params) && $params) ? array_merge($modParams, $params) : $modParams;

        return $this->context->link->getAdminLink('AdminModules') . '&' . http_build_query($params);
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7.7', '<')) {
            $this->initB2bMod();
        }

        $this->context->controller->addJquery();
        $this->context->controller->addJS($this->_path . 'views/js/back.js');
        if ($this->name === Tools::getValue('configure')) {
            $this->context->controller->addCss($this->_path . 'views/css/waitMe.min.css');
            $this->context->controller->addJS($this->_path . 'views/js/waitMe.min.js');
        }
    }

    public function hookDisplayAdminListBefore()
    {
        $controller = Dispatcher::getInstance()->getController();
        if (in_array($controller, $this->menuControllers)) {
            return $this->getB2bMenu();
        }
    }

    public function hookDisplayAdminNavBarBeforeEnd()
    {
        $this->context->smarty->assign('b2b_nav', $this->b2bNav);
        $this->context->smarty->assign(
            'ps_new',
            true === (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? 1 : 0
        );

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/b2b-nav-menu.tpl');
    }

    /**
     * prepare menu content for b2b top-menu.
     *
     * @return array
     */
    protected function getB2bNavigation()
    {
        $navigation = [
            'main' => [
                'label' => $this->l('B2B E-Commerce'),
                'icon' => 'briefcase',
            ],
            'home' => [
                'label' => $this->l('Home'),
                'icon' => 'home',
                'link' => $this->getModuleConfigLink($this),
            ],
            'child' => [],
        ];

        if (Module::isInstalled('b2bregistration') && Module::isEnabled('b2bregistration')) {
            $navigation['child']['b2bregistration'] = [
                'label' => $this->l('B2B Registration'),
                'icon' => 'users',
                'has_submenu' => true,
                'submenu' => [
                    [
                        'id' => 'b2b_reg',
                        'label' => $this->l('Settings'),
                        'sub_menu_link' => $this->getModuleConfigLink(
                            Module::getInstanceByName('b2bregistration')
                        ),
                    ],
                    [
                        'id' => 'b2b_cust',
                        'label' => $this->l('B2B customers'),
                        'sub_menu_link' => $this->context->link->getAdminLink('AdminB2BCustomers'),
                    ],
                    [
                        'id' => 'b2b_profile',
                        'label' => $this->l('B2B Profile'),
                        'sub_menu_link' => $this->context->link->getAdminLink('AdminB2BProfiles'),
                    ],
                    [
                        'id' => 'b2b_fields',
                        'label' => $this->l('B2B Fields'),
                        'sub_menu_link' => $this->context->link->getAdminLink('AdminB2BCustomFields'),
                    ],
                ],
            ];
        }

        if (Module::isInstalled('productquotation') && Module::isEnabled('productquotation')) {
            $navigation['child']['productquotation'] = [
                'label' => $this->l('Product Quotation'),
                'icon' => 'cart-arrow-down',
                'has_submenu' => true,
                'submenu' => [
                    [
                        'id' => 'b2b_quot_conf',
                        'label' => $this->l('Settings'),
                        'sub_menu_link' => $this->getModuleConfigLink(
                            Module::getInstanceByName('productquotation')
                        ),
                    ],
                    [
                        'id' => 'b2b_quot',
                        'label' => $this->l('Quotes'),
                        'sub_menu_link' => $this->context->link->getAdminLink('AdminProductQuotes'),
                    ],
                    [
                        'id' => 'b2b_quot_msg',
                        'label' => $this->l('Quote Messages'),
                        'sub_menu_link' => $this->context->link->getAdminLink('AdminQuotationMessages'),
                    ],
                    [
                        'id' => 'b2b_quot_field',
                        'label' => $this->l('Quote Fields'),
                        'sub_menu_link' => $this->context->link->getAdminLink('AdminQuoteFields'),
                    ],
                ],
            ];
        }

        if (Module::isInstalled('restrictcustomergroup') && Module::isEnabled('restrictcustomergroup')) {
            $navigation['child']['restrictcustomergroup'] = [
                'label' => $this->l('Restrictions'),
                'icon' => 'ban',
                'has_submenu' => true,
                'submenu' => [],
            ];

            if (Module::isInstalled('restrictcustomergroup') && Module::isEnabled('restrictcustomergroup')) {
                $navigation['child']['restrictcustomergroup']['submenu'][] = [
                    'id' => 'b2b_rest_group',
                    'label' => $this->l('Restrict Customer Group'),
                    'sub_menu_link' => $this->context->link->getAdminLink('AdminRestrictCustomerGroup'),
                ];

                if (Module::isInstalled('restrictpaymentmethods') && Module::isEnabled('restrictpaymentmethods')) {
                    $navigation['child']['restrictcustomergroup']['submenu'][] = [
                        'id' => 'b2b_rest_pm',
                        'label' => $this->l('Restrict Payment Methods'),
                        'sub_menu_link' => $this->context->link->getAdminLink('AdminRestrictPaymentMethod'),
                    ];

                    $navigation['child']['restrictcustomergroup']['submenu'][] = [
                        'id' => 'b2b_rest_pm_set',
                        'label' => $this->l('Payment Method Settings'),
                        'sub_menu_link' => $this->getModuleConfigLink(
                            Module::getInstanceByName('restrictpaymentmethods')
                        ),
                    ];
                }
            }
        }

        if (Module::isInstalled('quickproducttable') && Module::isEnabled('quickproducttable')) {
            $navigation['child']['quickproducttable'] = [
                'label' => $this->l('Quick Order'),
                'icon' => 'table',
                'has_submenu' => true,
                'submenu' => [
                    [
                        'id' => 'b2b_quick_ord',
                        'label' => $this->l('Settings'),
                        'sub_menu_link' => $this->getModuleConfigLink(
                            Module::getInstanceByName('quickproducttable')
                        ),
                    ],
                ],
            ];
        }

        return $navigation;
    }

    protected function initB2bMod()
    {
        $this->b2bNav = $this->getB2bNavigation();
        $this->b2bModules = $this->getFeatureDescription();
    }

    /**
     * get b2b top navigation menu.
     *
     * @return string
     */
    public function getB2bMenu()
    {
        $this->context->smarty->assign('b2b_nav', $this->b2bNav);

        return $this->display(__FILE__, 'views/templates/admin/top-menu.tpl');
    }

    /**
     * install b2b sub-module.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function installB2BModules($name)
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        $return = true;
        if (!$this->extractZipArchive($this->local_path . 'packages/' . $name . '.zip')) {
            $return &= false;
            $this->context->controller->errors[] = sprintf(
                $this->l('Error while extracting module %s.'),
                $name
            );
        } else {
            $nameInstance = Module::getInstanceByName($name);
            if (!$nameInstance->install()) {
                $return &= false;
                $this->context->controller->errors[] = sprintf(
                    $this->l('Error while installing module %s.'),
                    $name
                );
            } else {
                $this->context->controller->confirmations[] = sprintf(
                    $this->l('Module %s successfully installed.'),
                    $name
                );
            }
        }

        return $return;
    }

    /**
     * extract a zip package.
     *
     * @param string $file file path
     *
     * @return bool
     */
    protected function extractZipArchive($file)
    {
        $zip_folders = [];
        $tmp_folder = _PS_MODULE_DIR_ . md5(time());

        $success = false;
        if (Tools::substr($file, -4) == '.zip') {
            if (Tools::ZipExtract($file, $tmp_folder)) {
                $zip_folders = scandir($tmp_folder);
                if (Tools::ZipExtract($file, _PS_MODULE_DIR_)) {
                    $success = true;
                }
            }
        }

        if (!$success) {
            $this->context->controller->errors[] = $this->l('
                There was an error while extracting the module (file may be corrupted).
            ');
        } else {
            foreach ($zip_folders as $folder) {
                if (!in_array($folder, ['.', '..', '.svn', '.git', '__MACOSX']) &&
                    !Module::getInstanceByName($folder)) {
                    $this->context->controller->errors[] = sprintf(
                        $this->l('The module %1$s that you uploaded is not a valid module.'),
                        $folder
                    );
                    $this->recursiveDeleteDir(_PS_MODULE_DIR_ . $folder);
                }
            }
        }

        $this->recursiveDeleteDir($tmp_folder);

        return $success;
    }

    /**
     * delete a directory recursively.
     *
     * @param sting $dir
     *
     * @return void
     */
    protected function recursiveDeleteDir($dir)
    {
        if (false === strpos(realpath($dir), realpath(_PS_MODULE_DIR_))) {
            return;
        }

        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir . '/' . $object) == 'dir') {
                        $this->recursiveDeleteDir($dir . '/' . $object);
                    } else {
                        unlink($dir . '/' . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * force clear cache.
     *
     * @return void
     */
    public function flushCache()
    {
        Tools::clearSmartyCache();
        Tools::clearXMLCache();
        Media::clearCache();
        Tools::generateIndex();
        if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=') &&
            is_callable(['Tools', 'clearAllCache'])) {
            Tools::clearAllCache();
        }
    }

    /**
     * get sub-module current package version.
     *
     * @param string $module
     *
     * @return string
     */
    protected function getModuleVersion($module)
    {
        $resource = $this->getFeatureDescription();

        return $resource[$module]['version'];
    }

    /**
     * get b2b sub-module features detail.
     *
     * @return array
     */
    protected function getFeatureDescription()
    {
        $features = [];

        $features['b2bregistration'] = [
            'icon' => 'users',
            'version' => '1.3.0',
            'upgrade' => false,
            'label' => $this->l('B2B Registration'),
            'desc' => $this->l('
                Register your B2B Customer(s) and Offers a custom signup form for B2B customers or wholesellers
            '),
        ];

        $features['productquotation'] = [
            'icon' => 'cart-arrow-down',
            'version' => '2.4.0',
            'upgrade' => false,
            'label' => $this->l('Product Quotation'),
            'desc' => $this->l('B2B Customer(s) can send you quotation using this module.'),
        ];

        $features['restrictcustomergroup'] = [
            'icon' => 'ban',
            'version' => '1.2.0',
            'upgrade' => false,
            'label' => $this->l('Customer Group Restriction'),
            'desc' => $this->l('
                Allow B2B Customer(s) to shop for specific categories/products/cms pages.
            '),
        ];

        $features['restrictpaymentmethods'] = [
            'icon' => 'money',
            'version' => '2.0.3',
            'upgrade' => false,
            'label' => $this->l('Payment Restriction'),
            'desc' => $this->l('
                Disable payment methods for specific cart total ranges,
                products or categories for your B2B Customer(s).
            ') . ((Module::isInstalled('restrictcustomergroup')) ? '' : '<br><b>' . sprintf($this->l('This feature depends on "%s" feature. Please install it to use this feature.'), $features['restrictcustomergroup']['label']) . '</b>'),
        ];

        $features['quickproducttable'] = [
            'icon' => 'table',
            'version' => '1.2.0',
            'upgrade' => false,
            'label' => $this->l('Quick Order'),
            'desc' => $this->l('
                List products in a fully searchable and sortable table view for B2B Customer(s) for quick shopping.
            '),
        ];

        foreach ($features as $module => $feature) {
            if (true === (bool) Module::isInstalled($module)) {
                $instance = Module::getInstanceByName($module);
                if (Tools::version_compare($feature['version'], $instance->version, '>')) {
                    $features[$module]['upgrade'] = true;
                }
            }
        }

        return $features;
    }

    /**
     * upgrade sub-module packages.
     *
     * @return bool
     */
    public function upgradeFeatures($toUpgrade = [])
    {
        $up = true;
        if (isset($toUpgrade) && $toUpgrade) {
            foreach ($toUpgrade as $feature) {
                if (true == Module::isInstalled($feature)) {
                    $module = Module::getInstanceByName($feature);
                    $module->installed = 1;
                    if (false === Tools::version_compare($module->version, $this->getModuleVersion($feature), '<')) {
                        $up &= $this->extractZipArchive($this->local_path . 'packages/' . $feature . '.zip');
                    }

                    if (true === (bool) Tools::version_compare($module->version, $this->getModuleVersion($feature), '<')) {
                        if (Module::initUpgradeModule($module)) {
                            $up &= true;
                            $module->runUpgradeModule();
                        }
                    }
                }
            }
        }

        return $up;
    }

    protected function recursiveCopy($src, $dst)
    {
        $dir = opendir($src);

        if (!is_dir($dst)) {
            @mkdir($dst);
        }

        while ($file = readdir($dir)) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    if (!is_dir($dst . '/' . $file)) {
                        @mkdir($dst . '/' . $file);
                    }
                    $this->recursiveCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
