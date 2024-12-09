<?php

class ContactController extends ContactControllerCore
{
    public function postProcess()
    {
        if (Tools::isSubmit('submitMessage')) {
            if (!Tools::getValue('contactformnospan')) {
                return;
            }
        }
        parent::postProcess();
    }


    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_PS_MODULE_DIR_.'contactformnospam/nospam.js');
    }
}

?>
