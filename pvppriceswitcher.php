<?php
/**
 * PVP price switcher
 *
 * @author    Reborn Media Studio <info@rebornmediastudio.com>
 * @copyright Reborn Media Studio 2024
 * @version   1.0.0
 *
 */


 if (!defined('_PS_VERSION_')) {
    exit;
}

class PvpPriceSwitcher extends Module
{
    public function __construct()
    {
        $this->name = 'pvppriceswitcher';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Reborn Media Studio';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PVP Price Switcher');
        $this->description = $this->l('Allows B2B customers to toggle between tax-excluded and tax-included prices.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->hooks = array(
            array(
                'id' => 'displayNav',
                'name' => $this->l('Header'),
            )
        );

        $this->configVars = array(
            'NXTAL_PRICE_TAX_SWITCHER_HOOK',
            'NXTAL_PRICE_TAX_SWITCHER_STYLE',
            'NXTAL_PRICE_TAX_PRODUCT_PAGE',
        );

        $this->configValues = $this->getConfigValue();
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayNav') &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            Configuration::updateValue('NXTAL_PRICE_TAX_SWITCHER_HOOK', 'displayNav');
    }

    public function uninstall()
    {
        foreach ($this->configVars as $key) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall();
    }

    public function getConfigValue()
    {
        $values = array();
        foreach ($this->configVars as $key) {
            $values[$key] = Tools::getValue($key, Configuration::get($key));
        }
        return $values;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submit'.$this->name)) == true) {
            $this->postProcess();
            $this->context->controller->confirmations[] = $this->l('The settings have been successfully updated.');
        }

        return $this->renderForm() . $this->displayPromo();
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
        $helper->submit_action = 'submit'.$this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->configValues, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Style'),
                        'name' => 'NXTAL_PRICE_TAX_SWITCHER_STYLE',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 0,
                                    'name' => $this->l('Switch')
                                ),
                                array(
                                    'id' => 1,
                                    'name' => $this->l('Switch Interactive')
                                ),
                                array(
                                    'id' => 2,
                                    'name' => $this->l('Drop Down')
                                ),
                                array(
                                    'id' => 3,
                                    'name' => $this->l('Radio')
                                )
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Set tax switcher widget style to display in front office.')
                    )
                    array(
                        'type' => 'switch',
                        'name' => 'NXTAL_PRICE_TAX_PRODUCT_PAGE',
                        'label' => $this->l('Display switch at product page'),
                        'desc' => $this->l('Enable to display tax switch at product detail page.'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                            ),
                        )
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        foreach ($this->configVars as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookDisplayNav($params)
    {
        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
        // $this->context->smarty->assign(array(
        //     'is_customer_b2b' => $this->isCustomerB2B(),
        // ));
        return $this->display(__FILE__, 'views/templates/hook/toggle.tpl');
    }

    // private function isCustomerB2B()
    // {
    //     // Implement logic to this function
    //     return $this->context->customer->isLogged() && $this->context->customer->id_default_group == YOUR_B2B_GROUP_ID;
    // }
}
