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
 * Class GenerateInvoicesPdfCommand
 */
class GenerateInvoicesPdfCommand extends \PrestaChamps\GdprPro\Commands\DataRequestCommand
{
    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function execute()
    {
        try {
            $pdf = new \PDF($this->getInvoices(), \PDF::TEMPLATE_INVOICE, \Context::getContext()->smarty);
            $pdf = $pdf->render(false);

            header("Content-type: application/pdf", true, 200);
            echo $pdf;
        } catch (\Exception $exception) {
            die('error');
        }
        die();
    }

    /**
     * Get invoices which belongs the selected client
     *
     * @return \OrderInvoice[]
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getInvoices()
    {
        $id_customer = pSQL($this->customer->id);

        $query = new \DbQuery();
        $query->select('order_invoice.*');
        $query->from('order_invoice', 'order_invoice');
        $query->leftJoin('orders', 'orders', 'orders.id_order = order_invoice.id_order');
        $query->where("orders.id_customer = {$id_customer} AND order_invoice.number > 0");
        $invoiceList = \Db::getInstance()->executeS($query);
        if (count($invoiceList) < 1) {
            throw new \PrestaShopModuleException("This user does not have any invoices");
        }
        return \ObjectModel::hydrateCollection('OrderInvoice', $invoiceList);
    }
}
