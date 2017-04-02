<?php

namespace Service\Commissions;

use Model\Actor;
use Model\Amount;
use Model\Transaction;
use DateTimeImmutable;
use Service\Currencies;

class CacheOutNaturalCommission implements Commission
{
    const MAX_DISCOUNT_TIMES = 3;
    const DISCOUNT_RATE = '0.003';

    private $consumptions = [];
    private $weekly_discount;
    private $zero_funds;

    public function __construct(Currencies $converter)
    {
        $this->weekly_discount = new Amount('1000.00', 'EUR', $converter);
        $this->zero_funds = new Amount('0.0', 'EUR', $converter);
    }

    public function calculate(Transaction $transaction): Amount
    {
        $amount = $transaction->getAmount();

        $discount_info = $this->getDiscount($transaction->getActor(), $transaction->getDate());
        /** @var Amount $discount_left */
        $discount_left = $discount_info['amount'];
        if ($discount_info['times'] > 0 && $discount_left->compare($this->zero_funds) > 0) {
            $discounted = $amount->least($discount_left);
            $commissioned = $amount->sub($discounted);

            $discount_info['times'] -= 1;
            $discount_info['amount'] = $discount_left->sub($discounted);
        } else {
            $commissioned = $amount;
        }

        return $commissioned
            ->multiply(self::DISCOUNT_RATE)
            ->convert($amount->getCurrency())
            ->roundUp();
    }

    private function getDiscount(Actor $actor, DateTimeImmutable $date)
    {
        $key = $actor->getId() . '-' . $date->format('Y-W');
        if (!isset($this->consumptions[$key])) {
            $this->consumptions[$key] = new \ArrayObject([
                'amount' => $this->weekly_discount,
                'times'  => self::MAX_DISCOUNT_TIMES
            ]);
        }

        return $this->consumptions[$key];
    }
}