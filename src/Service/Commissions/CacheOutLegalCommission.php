<?php

namespace Service\Commissions;

use Model\Amount;
use Model\Transaction;
use Service\Currencies;

class CacheOutLegalCommission implements Commission
{
    const COMMISSION_RATE = '0.003';
    private $floor;

    public function __construct(Currencies $converter)
    {
        $this->floor = new Amount('0.5', 'EUR', $converter);
    }

    public function calculate(Transaction $transaction): Amount
    {
        return $transaction
            ->getAmount()
            ->multiply(self::COMMISSION_RATE)
            ->greatest($this->floor)
            ->convert($transaction->getAmount()->getCurrency())
            ->roundUp();
    }
}