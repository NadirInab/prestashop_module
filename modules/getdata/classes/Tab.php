<?php

// use PrestaShopBundle\Repository\Admin\TabRepository;



class Tab extends TabCore
{
    public function __construct()
    {
        $idParent = (int) Db::getInstance()->getValue('
        SELECT `id_tab`
        FROM `' . _DB_PREFIX_ . 'tab`
        WHERE `class_name` = "AdminParentModules"
    ');
        $this->id_parent = $idParent;
        $this->module = 'getdata';
        $this->name = array();

        foreach (Language::getLanguages() as $lang) {
            $this->name[$lang['id_lang']] = 'getdata';
        }

        $this->class_name = 'AdminGetDataController';
        $this->position = Tab::getNewLastPosition($this->id_parent);
        $this->active = true;
    }
}
