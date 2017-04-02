<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class DemoTest extends TestCase
{
    public function testEndToEnd()
    {
        $transactionsQuery = new \Query\CalculateCommissionsFromTransactionsCSV(__DIR__ . '/input.csv', [
            'EUR' => [
                'rate' => 1.,
                'precision' => 2,
            ],
            'USD' => [
                'rate' => 1.1497,
                'precision' => 2,
            ],
            'JPY' => [
                'rate' => 129.53,
                'precision' => 0,
            ]
        ]);

        $transactions = $transactionsQuery->execute();
        $this->assertEquals(['0.06', '0.90', '0', '0.70', '0.30', '0.30', '5.00', '0.00', '0.00'], $transactions);
    }
}