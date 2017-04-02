<?php

namespace Service;

class Currencies
{
    private $data;
    const COMPUTATIONS_SCALE = 10;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getConversionRate(string $from, string $to, int $precision)
    {
        if (!isset($this->data[$from])) {
            throw new \RuntimeException("Unknown currency $from");
        }
        if (!isset($this->data[$to])) {
            throw new \RuntimeException("Unknown currency $to");
        }
        return bcdiv(
            '1',
            bcdiv($this->data[$from]['rate'], $this->data[$to]['rate'], self::COMPUTATIONS_SCALE),
            self::COMPUTATIONS_SCALE
        );
    }

    public function getPrecision(string $currency): int
    {
        if (isset($this->data[$currency])) {
            return $this->data[$currency]['precision'];
        } else {
            throw new \RuntimeException("Unknown currency $currency");
        }
    }
}