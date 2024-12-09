<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Response;

class TransactionResponseObject
{
    private $transactionId;

    private $redirectUrl;

    public function __construct(string $transactionId, string $redirectUrl)
    {
        $this->transactionId = $transactionId;
        $this->redirectUrl   = $redirectUrl;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }
}
