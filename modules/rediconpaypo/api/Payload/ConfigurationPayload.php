<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Payload;

class ConfigurationPayload
{
    private $returnUrl;

    private $notifyUrl;

    private $cancelUrl = null;
    private $product = [
        'productType' => 'PNX',
        'installmentCount' => 1
    ];

    public function getInstallmentCount()
    {
        return $this->product['installmentCount'];
    }

    public function setInstallmentCount(string $installmentCount)
    {
        $this->product['installmentCount'] = (int)$installmentCount;
        return $this;
    }

    public function getProductType()
    {
        return $this->product['productType'];
    }

    public function setProductType(string $productType)
    {
        if ($productType=='core') {
            $this->product['installmentCount'] = 4;
        }
        $this->product['productType'] = $productType;
        return $this;
    }


    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    public function setReturnUrl(string $returnUrl)
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    public function setNotifyUrl(string $notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;

        return $this;
    }

    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    public function setCancelUrl(string $cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;

        return $this;
    }
}
