<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Payload;

class RefundsPayload
{
  
    private $amount;

    public function getAmount()
    {
        return (int) $this->amount;
    }

    public function setAmount(int $amount)
    {
        $this->amount = (int) $amount;

        return $this;
    }
}
