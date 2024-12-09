<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo\Helper;
// 
class SettingsPaypo
{
    const _NEW = 'NEW';

    const PENDING = 'PENDING';

    const CANCELED = 'CANCELED';

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

    /* waluty obsługiwane przez paypo */
    const CURRENCY_RON = 'RON';
    const CURRENCY_PLN = 'PLN';
    const ACCEPTED_CURRNCIES = [self::CURRENCY_PLN, self::CURRENCY_RON];

    const PAYPO_URL = [
        self::CURRENCY_PLN => [
            'sandbox' => 'https://api.sandbox.paypo.pl/v3/',
            'production' => 'https://api.paypo.pl/v3/',
        ],
        self::CURRENCY_RON => [
            'sandbox' => 'https://api.sandbox.paypo.ro/v3/',
            'production' => 'https://api.paypo.ro/v3/',
        ],
    ];

}
