<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsAboutController extends CommonController
{
    private $links = array();

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->links = $this->getInformationLink();
        $this->fields_options = $this->createFieldsOption();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/upsmodule.css');
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('AdminUpsAbout');
    }

    private function createFieldsOption()
    {
        $fieldsOptions = array(
            'indexation' => array(
                'title'  => '<p style="margin-top: -60px;"></p>',
                'icon'   => '',
                'info'   => $this->drawHtml()
            )
        );

        return $fieldsOptions;
    }

    private function drawHtml()
    {
        $sdk = $this->sdk;

        $header = $sdk->t('about', 'headerFeatures');
        $headerVersion = $sdk->t('about', 'headerModuleVersion');
        $txtFrontOffice = $sdk->t('about', 'txtFrontOffice');
        $txtBackOffice = $sdk->t('about', 'txtBackOffice');
        $eFeature1 = $sdk->t('about', 'eFeature1');
        $eFeature2 = $sdk->t('about', 'eFeature2');
        $eFeature3 = $sdk->t('about', 'eFeature3');
        $eFeature4 = $sdk->t('about', 'eFeature4');
        $eFeature5 = $sdk->t('about', 'eFeature5');
        $eFeature6 = $sdk->t('about', 'eFeature6');
        $eFeature7 = $sdk->t('about', 'eFeature7');
        $txtModuleVersion = $sdk->t('about', 'txtModuleVersion') . ' ' . $sdk->getVersion();
        $txtReleaseDate = $sdk->t('about', 'txtReleaseDate') . ' ' . $sdk->getReleaseDate();

        $html = $sdk->t(
            'about',
            'titleAbout',
            array(
                'ttlInfo' => '<b><a href="' . $this->links['about_title_link'] . '" target="_blank">',
                'eottlInfo' => '</a></b>'
            )
        )
        
        . $this->drawHeader($header)
        . "<u><b>$txtFrontOffice:</b></u>
        <ul>
            <li>$eFeature1</li>
            <li>$eFeature2</li>
            <li>$eFeature3</li>
        </ul>
        <u><b>$txtBackOffice:</b></u>
        <ul>
            <li>$eFeature4</li>
            <li>$eFeature5</li>
            <li>$eFeature6</li>
            <li>$eFeature7</li>
        </ul>"
        . $this->drawHeader($headerVersion)
        . "$txtModuleVersion<br/>$txtReleaseDate"
        
        . $this->drawHeader($sdk->t('about', 'headerChangeLog'))
        
        . $sdk->t('about', 'txtChangelog')
        
        . $this->drawHeader($sdk->t('about', 'headerCompatible'))
        
        . $sdk->t(
            'about',
            'txtCompatible',
            array('vCompatible' => $sdk->getCompatibleVersions())
        )

        . $this->drawHeader($sdk->t('about', 'headerLinkWebsite'))
        
        . $sdk->t(
            'about',
            'txtLinkWebsite',
            array('infomation_link' => $this->getIntegrationLinks())
        )
        
        . $this->drawHeader($sdk->t('about', 'headerSupport'))
        
        . $sdk->t(
            'about',
            'txtInfo',
            array(
                'support_phone' => $this->getSupportPhones(),
                'merchant_key' => Configuration::get('MERCHANT_KEY')
            )
        );

        return $html;
    }

    private function drawHeader($title)
    {
        return "<h5 class='content-bold'>$title</h5>";
    }

    private function getIntegrationLinks()
    {
        $links = include PATH_ASSETS_FOLDER . 'Links/support_links.php';
        $str = "<ul>";
        $lang = '';

        foreach ($this->module->pluginCountryList as $iso => $name) {
            $locale = $this->module->lang . '-' . $iso;

            if (array_search($locale, $this->module::$supportedLocale)) {
                $lang = $this->module->lang;
            } else {
                $lang = 'en';
            }

            $link = $links[$iso][$lang]['infomation_link'];

            $str .= "<li><a target='_blank' href='$link'>$name</a></li>";
        }
        return $str . "</ul>";
    }

    private function getSupportPhones()
    {
        $strSupportPhone = '';

        $listPhone = include(PATH_ASSETS_FOLDER . 'Links/support_phones.php');

        foreach ($this->module->pluginCountryList as $iso => $name) {
            $phoneNumber = $listPhone[$iso];
            $strSupportPhone .= "<li>$name $phoneNumber</li>";
        }

        return "<ul>$strSupportPhone</ul>";
    }
}
