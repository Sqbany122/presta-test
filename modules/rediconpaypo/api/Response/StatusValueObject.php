<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Response;

class StatusValueObject
{
    private $code;

    private $message;

    public function __construct(int $code, string $message)
    {
        $this->code    = $code;
        $this->message = $message;
    }

    public function getCode()
    {
        return $this->code;
    }

    
    public function getMessage()
    {
        return $this->message;
    }
}
