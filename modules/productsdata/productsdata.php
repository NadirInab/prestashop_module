<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductsData extends Module
{
    public function __construct()
    {
        $this->name = 'Products Retriever';
        $this->tab = 'Back_office_features';
        $this->version = '1.0.0';
        $this->author = 'Sobrus';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Get Data');
        $this->description = $this->l('prodcuts data retriever mysql => csv');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (parent::install()
            && $this->registerHook('displayLeftColumn')
            && $this->registerHook('displayHeader')
            && Configuration::updateValue('MYMODULE_NAME', 'my friend')
        );
    }
    public function uninstall()
    {
        return (parent::uninstall()
            && Configuration::deleteByName('MYMODULE_NAME')
        );
    }
}
