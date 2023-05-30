<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class GetData extends Module
{
    // contructor function, where the module data is instatiated such as : name, version ...
    public function __construct()
    {
        $this->name = 'getdata';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Sobrus';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6.0.0',
            'max' => '1.7.99.99'
        ];
        $this->tabs = [
            'name' => 'GetData',
            'visible' => true,
            'class_name' => 'AdminOriginController',
            'parent_class_name' => 'AdminCatalog'
        ];
        $this->bootstrap = true;


        parent::__construct();

        $this->displayName = $this->l('Data Retrieval');
        $this->description = $this->l('PrestaShop module to retreive data effortlessly from your database tables.');
        // $this->registerControllers();

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }

    //  the install method 
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // if (_PSVERSION >= '1.7') {
        //     $this->registerHook('displayProductAdditionalInfo');
        // } else {
        //     $this->registerHook('productTab');
        // }

        return (parent::install()
            && $this->registerHook('displayLeftColumn')
            && $this->registerHook('displayHeader')
            && Configuration::updateValue('MYMODULE_NAME', 'my friend')
        );
    }

    // the uninstall method 
    public function uninstall()
    {
        return (parent::uninstall()
            && Configuration::deleteByName('MYMODULE_NAME')
        );
    }

    // Retrieve data from database table, return an array of products data .
    public function getDataFromTable()
    {
        $_SERVER['HTTPS'] = 'off';
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
        $query = "SELECT DISTINCT pp.id_product AS product_id,
                CONCAT('{$protocol}://{$_SERVER['SERVER_NAME']}','/',pp.id_product,'-',pcl.name, '/',ppl.link_rewrite,'.webp') AS image_url,
                ppl.name AS Nom,
                pp.reference AS Référence, 
                pcl.name AS Catégorie,
                pp.wholesale_price as MontantHT,
                pp.price as MontantTTC,
                psa.quantity AS Quantité,
                pp.state AS État, 
                pcp.position as product_position
        FROM " . _DB_PREFIX_ . "product AS pp
        JOIN " . _DB_PREFIX_ . "product_lang AS ppl ON pp.id_product = ppl.id_product
        JOIN " . _DB_PREFIX_ . "category_lang AS pcl ON ppl.id_lang = pcl.id_lang 
        JOIN " . _DB_PREFIX_ . "stock_available AS psa ON psa.id_product = pp.id_product
        JOIN " . _DB_PREFIX_ . "category_product as pcp ON pcp.id_product = pp.id_product 
        ORDER BY product_id";
        $result = Db::getInstance()->executeS($query);
        return $result;
    }

    public function installTab()
    {
        $languages = Language::getLanguages(false) ;
        if (!(int) Tab::getIdByName('AdminInspiration')) {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminInspiration";
            foreach ($languages as $language) {
                $parentTab->name[$language['id_lang']] = 'Inspiration';
            }
            $parentTab->id_parent = (int)Tab::getIdByName('SELL');
            $parentTab->module = '';
            $parentTab->add();
        }
    }

    // public function displayLeftColumn()
    // {
    //     return $this->display(__FILE__, "views/templates/get_form.tpl");
    // }


    // Hook to display data in the header
    public function hookDisplayHeader()
    {
        $this->generateCsvFile();
    }

    // Generate CSV file and initiate download
    public function generateCsvFile()
    {
        $data = $this->getDataFromTable();

        $filename = 'data.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        $file = fopen('php://output', 'w');

        if (!empty($data)) {
            fputcsv($file, array_keys($data[0]));
        }

        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }
}
