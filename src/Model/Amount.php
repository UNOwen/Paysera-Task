<?php

declare(strict_types=1);

namespace Model;

use Service\Currencies;

class Amount
{
    const COMPUTATIONS_SCALE = 5;

    private $amount;
    private $currency;
    private $converter;

    public function __construct(string $amount, string $currency, Currencies $converter)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->converter = $converter;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function multiply(string $factor): Amount
    {
        return new Amount(
            bcmul($this->amount, $factor, self::COMPUTATIONS_SCALE),
            $this->currency,
            $this->converter
        );
    }

    public function compare(Amount $another): int
    {
        return bccomp($this->getAmount(), $another->convert($this->currency)->getAmount(), self::COMPUTATIONS_SCALE);
    }

    public function add(Amount $another): Amount
    {
        return new Amount(
            bcadd($this->amount, $another->convert($this->currency)->getAmount()),
            $this->getCurrency(),
            $this->converter
        );
    }

    public function sub(Amount $another): Amount
    {
        return new Amount(
            bcsub($this->amount, $another->convert($this->currency)->getAmount()),
            $this->getCurrency(),
            $this->converter
        );
    }

    public function least(Amount $another): Amount
    {
        return $this->compare($another) <= 0
            ? $this
            : $another;
    }

    public function greatest(Amount $another): Amount
    {
        return $this->compare($another)  >= 0
            ? $this
            : $another;
    }

    public function convert(string $currency): Amount
    {
        return new Amount(
            bcmul(
                $this->amount,
                $this->converter->getConversionRate($this->currency, $currency, self::COMPUTATIONS_SCALE),
                self::COMPUTATIONS_SCALE
            ),
            $currency,
            $this->converter
        );
    }

    public function roundUp()
    {
        $precision = $this->converter->getPrecision($this->currency);
        $amount = bcmul($this->amount, (string) pow(10, $precision), self::COMPUTATIONS_SCALE);
        $parts = explode('.', $amount);
        if (count($parts) == 2 && intval($parts[1]) > 0) {
            $parts[0] = bcadd($parts[0], '1', self::COMPUTATIONS_SCALE);
        }

        return new Amount(
            bcdiv($parts[0], (string) pow(10, $precision), $precision),
            $this->currency,
            $this->converter
        );
    }
}