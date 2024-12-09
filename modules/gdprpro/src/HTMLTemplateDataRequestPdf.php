<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

/**
 * Class HTMLTemplateDataRequestPdf
 */
class HTMLTemplateDataRequestPdf extends HTMLTemplate
{
    public $data;

    public function __construct($data, $smarty)
    {
        $this->data = $data;
        $this->smarty = $smarty;
        $this->title = self::l('Data request');
        $this->shop = new Shop(Context::getContext()->shop->id);
    }

    /**
     * Returns the template's HTML content
     *
     * @return string HTML content
     * @throws SmartyException
     */
    public function getContent()
    {
        $this->smarty->assign(array(
            'customerData' => $this->data,
        ));

        return $this->smarty->fetch(
            _PS_MODULE_DIR_ .
            'gdprpro/views/templates/front/pdf/data-request/template_content.tpl'
        );
    }

    /**
     * @return string
     * @throws SmartyException
     */
    public function getLogo()
    {
        return $this->smarty->fetch(
            _PS_MODULE_DIR_ .
            'gdprpro/views/templates/front/pdf/data-request/template_logo.tpl'
        );
    }

    /**
     * @return string
     * @throws SmartyException
     */
    public function getHeader()
    {
        return "";
    }

    /**
     * Returns the template filename
     *
     * @return string filename
     * @throws SmartyException
     */
    public function getFooter()
    {
        return "";
    }

    /**
     * Returns the template filename
     *
     * @return string filename
     */
    public function getFilename()
    {
        return 'data_request.pdf';
    }

    /**
     * Returns the template filename when using bulk rendering
     *
     * @return string filename
     */
    public function getBulkFilename()
    {
        return 'data_requests.pdf';
    }

    public function getPagination()
    {
    }
}
