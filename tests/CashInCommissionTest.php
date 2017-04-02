<?php

class CashInCommissionTest extends \PHPUnit\Framework\TestCase
{
    public function testUnderLimit()
    {
        $converter = new \Service\Currencies([
            'EUR' => [
                'rate' => 1.,
                'precision' => 2
            ]
        ]);
        $calculator = new \Service\Commissions\CacheInCommission($converter);
        $commission = $calculator->calculate(
            new \Model\Transaction(
                new DateTimeImmutable(),
                new \Model\Actor(1, \Model\Actor::TYPE_NATURAL),
                new \Model\Operation(\Model\Operation::CASH_IN),
                new \Model\Amount('100.00', 'EUR', $converter)
            )
        );

        $this->assertEquals('0.03', $commission->getAmount());
    }

    public function testOverLimitConversion()
    {
        $converter = new \Service\Currencies([
            'EUR' => [
                'rate' => 1.,
                'precision' => 2
            ],
            'USD' => [
                'rate' => 2.,
                'precision' => 2
            ]
        ]);
        $calculator = new \Service\Commissions\CacheInCommission($converter);
        $commission = $calculator->calculate(
            new \Model\Transaction(
                new DateTimeImmutable(),
                new \Model\Actor(1, \Model\Actor::TYPE_NATURAL),
                new \Model\Operation(\Model\Operation::CASH_IN),
                new \Model\Amount('100000.00', 'USD', $converter)
            )
        );

        $this->assertEquals('10.00', $commission->getAmount());
    }
}