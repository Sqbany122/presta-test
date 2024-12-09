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

use PrestaChamps\GdprPro\Commands\SendDataCommand;
use PrestaChamps\GdprPro\Commands\RequestDeleteDataCommand;

/**
 * Class GdprProEraseMyDataModuleFrontController
 *
 * Handle and process the customer data requests (like data export and deletion)
 */
class GdprProDataRequestModuleFrontController extends ModuleFrontControllerCore
{
    public $display_column_left = false;
    public function initContent()
    {
        parent::initContent();
        $this->display_column_left = false;
        if (Context::getContext()->customer->isLogged() && Tools::getValue('type')) {
            $type = (Tools::getValue('type') == 'delete') ?
                DataRequest::REQUEST_TYPE_DELETION
                : DataRequest::REQUEST_TYPE_EXPORT;
            try {
                $dataRequest = new DataRequest();
                $dataRequest->id_customer = $this->context->customer->id;
                $dataRequest->id_guest = $this->context->customer->id_guest;
                $dataRequest->type = $type;
                $dataRequest->status = DataRequest::REQUEST_STATUS_NEW;
                $dataRequest->created_at = (new \DateTime('NOW'))->format('Y-m-d H:i:s');
                $dataRequest->save();

                if ($type == DataRequest::REQUEST_TYPE_EXPORT) {
                    $dataRequest->fulfill();
                    $command = new SendDataCommand(new Customer($this->context->customer->id), $this->context);
                    $command->execute();
                } else {
                    $command = new RequestDeleteDataCommand(new Customer($this->context->customer->id), $this->context);
                    $command->execute();
                }
            } catch (\Exception $exception) {
                $this->ajaxDie(
                    array(
                        'status' => 500,
                        'error'  => (_PS_MODE_DEV_)
                            ? $exception->getMessage()
                            :
                            $this->module->l(
                                "An error occurred while processing your request, please contact the shop owner"
                            ),
                    )
                );
            }
        }

        $this->ajaxDie(array('status' => 200));
    }

    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        if (!is_scalar($value)) {
            $value = json_encode($value);
        }
        parent::ajaxDie($value, $controller, $method);
    }
}
