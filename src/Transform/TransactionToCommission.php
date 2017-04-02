<?php

namespace Transform;

use Service\CommissionCalculator;
use Model\Transaction;

class TransactionToCommission
{
    private $calculator;

    public function __construct(CommissionCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function __invoke(Transaction $transaction)
    {
        return $this->calculator->calculateCommission($transaction);
    }
}