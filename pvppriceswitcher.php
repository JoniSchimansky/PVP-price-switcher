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
        $this->tab = 'pricing_promotion';
        $this->version = '1.0.0';
        $this->author = 'Reborn Media Studio';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PVP Price Switcher');
        $this->description = $this->l('Allows B2B customers to toggle between tax-excluded and tax-included prices.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        
        return parent::install() &&
               $this->registerHook('displayNav') &&
               $this->registerHook('header') &&
               $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitPvpPriceSwitcherModule')) == true) {
            $this->postProcess();
        }
        return $this->renderForm();
    }

    protected function postProcess()
    {
        // Procesar y guardar la configuración enviada por el usuario
    }

    protected function renderForm()
    {
        // Construir y devolver el formulario de configuración
    }

    public function hookDisplayNav($params)
    {
        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
        $this->context->smarty->assign(array(
            'is_customer_b2b' => $this->isCustomerB2B(),
        ));
        return $this->display(__FILE__, 'views/templates/hook/toggle.tpl');
    }

    private function isCustomerB2B()
    {
        // Implement logic to this function
        return $this->context->customer->isLogged() && $this->context->customer->id_default_group == YOUR_B2B_GROUP_ID;
    }
}
