<?php
/**
* NOTICE OF LICENSE
*
*  @author    Kjeld Borch Egevang
*  @copyright 2017 Kjeld Borch Egevang
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*  $Date: 2018/02/07 07:02:41 $
*  E-mail: kjeld@mail4us.dk
*/

class ContactFormNoSpam extends Module
{
    public function __construct()
    {
        $this->name = 'contactformnospam';
        // $this->tab = 'payments_gateways';
        $this->version = '0.0.1';
        $this->author = 'Kjeld Borch Egevang';

        parent::__construct();

        $this->displayName = $this->l('Contact form no spam');
        $this->description = $this->l('Ignore spam in contact form');
    }
}
