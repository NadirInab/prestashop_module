<?php

class GetDataInstallDataController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $this->processInstallation();
    }

    public function processInstallation()
    {
        // Add your installation logic here
        // This method will be triggered when the user clicks the install button
        // You can insert the data into the database, perform any required actions, etc.
    }
}
