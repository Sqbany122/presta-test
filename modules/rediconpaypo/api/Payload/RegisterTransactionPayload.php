<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Payload;

class RegisterTransactionPayload
{
    private $id = null;

    private $merchantId;

    private $shopId = null;

    private $order;

    private $customer;

    private $configuration;

    public function getMerchantId()
    {
        return $this->merchantId;
    }

    public function setMerchantId(string $merchantId)
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    public function getShopId()
    {
        return $this->shopId;
    }

    public function setShopId(string $shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(OrderPayload $order)
    {
        $this->order = $order;

        return $this;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer(CustomerPayload $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function setConfiguration(ConfigurationPayload $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }
}
