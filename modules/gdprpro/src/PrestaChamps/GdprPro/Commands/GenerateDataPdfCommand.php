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

namespace PrestaChamps\GdprPro\Commands;

/**
 * Class GenerateDataPdfCommand
 *
 * @package PrestaChamps\GdprPro\Commands
 */
class GenerateDataPdfCommand extends DataRequestCommand
{
    /**
     * @var $pdf \PDF
     */
    protected $pdf;

    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function init()
    {
        parent::init();
        $this->pdf = new \PDF(array('data' => $this->formatData()), 'DataRequestPdf', \Context::getContext()->smarty);
    }

    /**
     * @throws \PrestaShopException
     */
    public function execute()
    {
        header("Content-type: application/pdf", true, 200);
        echo $this->pdf->render(false);
    }
}
