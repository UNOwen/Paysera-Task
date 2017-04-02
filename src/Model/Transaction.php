<?php

declare(strict_types=1);

namespace Model;

use DateTimeImmutable;

class Transaction
{
    private $date;
    private $actor;
    private $operation;
    private $amount;

    public function __construct(
        DateTimeImmutable $date,
        Actor $actor,
        Operation $operation,
        Amount $amount
    ) {
        $this->date = $date;
        $this->actor = $actor;
        $this->operation = $operation;
        $this->amount = $amount;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getActor(): Actor
    {
        return $this->actor;
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }
}