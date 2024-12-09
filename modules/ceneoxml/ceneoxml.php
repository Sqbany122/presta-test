<?php
if (!defined('_PS_VERSION_'))
  exit;

define('__CENEO_XML_MODULE_PATH__', _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . 'ceneoxml' . DIRECTORY_SEPARATOR); 

require_once(__CENEO_XML_MODULE_PATH__ . 'helpers'. DIRECTORY_SEPARATOR . 'PrestaShopHelper.php'); 
 
class CeneoXml extends Module
{
    CONST PS_VERSION_MIN = '1.5';
    
    public function __construct()
    {
        //hack for version 1.5
        $ps_version_max = PrestaShopHelper::GetPSVersionBasePart(_PS_VERSION_) == '1.5' ? '1.6' : _PS_VERSION_;
        
        $this->name = 'ceneoxml';
        $this->tab = 'market_place';
        $this->version = '1.1.0';
        $this->author = 'Ceneo Sp. z o.o.';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => $ps_version_max); 
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Ceneo XML');
        $this->description = $this->l('Moduł Ceneo XML pozwala na integrację sklepu PrestaShop z porównywarką cen Ceneo.pl. 
                                        W szybki sposób generuje poprawny dokument XML z ofertami.
                                        Copyright © 2016 Ceneo.pl. All rights reserved.');

        $this->confirmUninstall = $this->l('Czy na pewno chcesz odinstalować moduł Ceneo XML?');
    }

    public function getContent()
    {
        $xml_url_friendly = _PS_BASE_URL_.__PS_BASE_URI__.'module/ceneoxml/xml';
        $xml_url_not_friendly = _PS_BASE_URL_.__PS_BASE_URI__.'index.php?fc=module&module=ceneoxml&controller=xml';
        return '<div class="panel"><h3>Do czego służy ten moduł?</h3>
            Moduł Ceneo XML pozwala na integrację sklepu PrestaShop z porównywarką cen Ceneo.pl. 
            W szybki sposób generuje poprawny dokument XML z ofertami.<br/><br/>
            Xml z produktami jest generowany domyślnie pod adresem: 
            <ul>
                <li><a href="'.$xml_url_friendly.'" target="_blank">'.$xml_url_friendly.'</a><br/> W przypadku włączonej opcji Przyjazne adresy URL</li>
                <li><a href="'.$xml_url_not_friendly.'" target="_blank">'.$xml_url_not_friendly.'</a><br/> W przypadku wyłączonej opcji Przyjazne adresy URL</li>
            </ul>
            Dla poprawnego działania modułu wpis <i>safe_mode</i> w php.ini musi być ustawiony na 0.
            </div>';
    }
}