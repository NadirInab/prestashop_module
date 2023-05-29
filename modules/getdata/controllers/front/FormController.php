<?php


class FormController extends FrontController
{
    public function initContent()
    {
        parent::initContent();

        // Generate the CSRF token
        // $token = Tools::getToken(false);

        // Assign the token to the Smarty template variable
        // $this->context->smarty->assign('token', $token);

        // Render the template file
        $this->setTemplate('module:getdata/views/templates/getdata_form.tpl');
    }
}
