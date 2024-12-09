<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Response;

class NotificationResponseObject
{
    const _NEW = 'NEW';

    const PENDING = 'PENDING';

    const CANCELLED = 'CANCELLED';

    const REJECTED = 'REJECTED';

    const ACCEPTED = 'ACCEPTED';

    const COMPLETED = 'COMPLETED';

    const PAID = 'PAID';

    const CONFIRMED = 'CONFIRMED';

    const TRANSACTION_STATUSES = [
        self::_NEW,
        self::PENDING,
        self::CANCELLED,
        self::REJECTED,
        self::ACCEPTED,
        self::COMPLETED,
    ];

    const SETTLEMENT_STATUSES = [
        self::_NEW,
        self::CONFIRMED,
        self::PAID,
    ];

   
    private $merchantId;
    private $referenceId;
    private $transactionId;
    private $transactionStatus;
    private $amount;
    private $message;
    private $lastUpdate;

    public function __construct(
        string $merchantId,
        string $referenceId,
        string $transactionId,
        string $transactionStatus,
        int $amount,
        string $message,
        $lastUpdate
    ) {
        $this->merchantId        = $merchantId;
        $this->referenceId       = $referenceId;
        $this->transactionId     = $transactionId;
        $this->transactionStatus = $transactionStatus;
        $this->amount            = $amount;
        $this->message           = $message;
        $this->lastUpdate        = $lastUpdate;
    }


    public function getMerchantId()
    {
        return $this->merchantId;
    }

    public function getReferenceId()
    {
        return $this->referenceId;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }
}
