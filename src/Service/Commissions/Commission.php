<?php

namespace Service\Commissions;

use Model\Amount;
use Model\Transaction;

interface Commission
{
    public function calculate(Transaction $transaction): Amount;
}
