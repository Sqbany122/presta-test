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

use \Context;
use PrestaChamps\GdprPro\Traits;

/**
 * Class DataRequestCommand
 */
abstract class DataRequestCommand
{
    use Traits\CollectCustomerDataTrait;
    /**
     * Additional options for the command
     *
     * @var $options []
     */
    public $options = array();

    /**
     * @var $context Context
     */
    public $context;

    /**
     * DataRequestCommand constructor.
     *
     * @param \Customer $customer
     * @param array     $options
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function __construct(\Customer $customer, Context $context, $options = array())
    {
        $this->customer = $customer;
        $this->context = $context;
        $this->options = array_merge($this->options, $options);
        $this->collectData();
        $this->init();
    }

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
    }

    abstract public function execute();
}
