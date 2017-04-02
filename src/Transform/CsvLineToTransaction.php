<?php

namespace Transform;

use Model\Actor;
use Model\Amount;
use Model\Operation;
use Model\Transaction;
use DateTimeImmutable;
use Service\Currencies;

class CsvLineToTransaction
{
    private $currencies;

    public function __construct(Currencies $currencies)
    {
        $this->currencies = $currencies;
    }

    public function __invoke($args)
    {
        $date = $this->buildDate($args[0]);
        $actor = $this->buildActor($args[1], $args[2]);
        $operation = $this->buildOperation($args[3]);
        $amount = $this->buildAmount($args[4], $args[5]);

        return new Transaction($date, $actor, $operation, $amount);
    }

    private function buildDate(string $value): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat($date_format = 'Y-m-d', $value);
        if ($date instanceof DateTimeImmutable && $date->format($date_format) == $value) {
            $date = $date->setTime(0, 0, 0);
        } else {
            throw new \DomainException("Value $value is not a Y-m-d format date");
        }

        return $date;
    }

    private function buildActor(string $raw_id, string $raw_type): Actor
    {
        $actor_id = filter_var($raw_id, FILTER_VALIDATE_INT);
        if (false !== $actor_id) {
            $actor_id = intval($actor_id);
        } else {
            throw new \DomainException("Value $raw_id is not a numeric id");
        }
        if ($raw_type == 'natural') {
            $actor_type = Actor::TYPE_NATURAL;
        } elseif ($raw_type == 'legal') {
            $actor_type = Actor::TYPE_LEGAL;
        } else {
            throw new \DomainException("Value $raw_type is not valid actor type");
        }

        return new Actor($actor_id, $actor_type);
    }

    private function buildOperation(string $raw_type): Operation
    {
        if ($raw_type == 'cash_in') {
            $operation_type = Operation::CASH_IN;
        } elseif ($raw_type == 'cash_out') {
            $operation_type = Operation::CASH_OUT;
        } else {
            throw new \DomainException("Value $raw_type is not valid opration type");
        }

        return new Operation($operation_type);
    }

    private function buildAmount(string $raw_amount, string $raw_currency): Amount
    {
        $amount = filter_var($raw_amount, FILTER_VALIDATE_FLOAT);
        if (false === $amount) {
            throw new \DomainException("Value $raw_amount is not a valid amount of money");
        }
        // maybe check currency someday

        return new Amount($amount, $raw_currency, $this->currencies);
    }
}