<?php

use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    /**
     * @var \Service\Currencies
     */
    private $converter;

    public function setUp()
    {
        $this->converter = new \Service\Currencies([
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
    }

    public function testNoConversion()
    {
        $amount = new \Model\Amount('3', 'EUR', $this->converter);
        $this->assertEquals('3.00', $amount->convert('EUR')->roundUp()->getAmount());
    }

    public function testSingleConversion()
    {
        $amount = new \Model\Amount('3', 'EUR', $this->converter);
        $this->assertEquals('3.45', $amount->convert('USD')->roundUp()->getAmount());
    }

    public function testTransitionalConversion()
    {
        $amount = new \Model\Amount('3', 'USD', $this->converter);
        $this->assertEquals('338', $amount->convert('JPY')->roundUp()->getAmount());
    }

    public function testUnknownCurrency()
    {
        $amount = new \Model\Amount('3', 'USD', $this->converter);
        $this->expectException(\RuntimeException::class);
        $amount->convert('LTL')->roundUp()->getAmount();
    }
}