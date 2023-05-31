<?php

class AdminGetDataController extends ModuleAdminController 
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(
            'module_dir' => $this->module->getLocalPath(),
        ));

        $this->setTemplate('module:mymodule/views/templates/.tpl');
    }
}