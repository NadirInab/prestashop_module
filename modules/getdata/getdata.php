<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class GetData extends Module
{
    public function __construct()
    {
        $this->name = 'getdata';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Sobrus';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Get data');
        $this->description = $this->l('PrestaShop module for data retrieving.');

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

    public function getDataFromTable()
    {
        $sql = 'SELECT DISTINCT pp.id_product AS product_id, pp.reference AS reference, pp.state AS etat, 
                ppl.name AS nom, ppl.link_rewrite AS imageLink, 
                pcl.name AS categorie, psa.quantity AS quantite,
                pp.price as MontantTTC,
                pp.wholesale_price as MontantHT,
                pcp.position as product_position
        FROM '. _DB_PREFIX_ .'product AS pp
        JOIN '. _DB_PREFIX_ .'product_lang AS ppl ON pp.id_product = ppl.id_product
        JOIN '. _DB_PREFIX_ .'category_lang AS pcl ON ppl.id_lang = pcl.id_lang 
        JOIN '. _DB_PREFIX_ .'stock_available AS psa ON psa.id_product = pp.id_product
        JOIN '. _DB_PREFIX_ .'category_product as pcp ON pcp.id_product = pp.id_product 
        ORDER BY product_id';
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public function uninstall()
    {
        return (parent::uninstall()
            && Configuration::deleteByName('MYMODULE_NAME')
        );
    }

    public function hookDisplayHeader()
    {
        $this->generateCsvFile();
    }

    public function generateCsvFile()
    {
        $data = $this->getDataFromTable();

        // Define the CSV file name
        $filename = 'data.csv';

        // Set the appropriate headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        // Create a file pointer
        $file = fopen('php://output', 'w');

        // Write the CSV headers
        if (!empty($data)) {
            fputcsv($file, array_keys($data[0]));
        }

        // Write the data rows
        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        // Close the file pointer
        fclose($file);

        // Stop the script execution
        exit;
    }
}