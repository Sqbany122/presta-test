<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Payload;

class UpdateTransactionPayload
{
    const STATUSES = [self::COMPLETED, self::CANCELED];

    const COMPLETED = 'COMPLETED';

    const CANCELED = 'CANCELED';


    private $status;

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }
}
