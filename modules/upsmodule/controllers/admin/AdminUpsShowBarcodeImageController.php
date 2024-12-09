<?php
/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

require_once dirname(__FILE__) . '/../../common/CommonController.php';

class AdminUpsShowBarcodeImageController extends CommonController
{
    public function initContent()
    {
        parent::initContent();
    }
    public function postProcess()
    {
        $fileDownload = Constants::NAME_LABEL_SHIPMENT . '.zip';
        $zipFilePath = $this->context->cookie->ZipPath;

        if (file_exists($zipFilePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($fileDownload) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($zipFilePath));
            ob_clean();
            ob_end_flush();
            readfile($zipFilePath);

            unlink($zipFilePath);
            exit;
        }
    }
}
