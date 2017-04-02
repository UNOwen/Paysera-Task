<?php

declare(strict_types=1);

namespace Query;

use Model\Amount;
use Model\Transaction;
use Formigone\Chain;
use Service\CommissionCalculator;
use Service\Currencies;
use Transform\CsvLineToTransaction;
use Transform\TransactionToCommission;

class CalculateCommissionsFromTransactionsCSV implements Query
{
    private $infile;
    private $currencies;

    public function __construct(string $infile, $currencies)
    {
        $this->infile = $infile;
        $this->currencies = new Currencies($currencies);
    }

    /**
     * @return Transaction[]
     */
    public function execute(): array
    {
        $transactions = Chain::from($this->readLines())
            ->map(new CsvLineToTransaction($this->currencies))
            // todo can make sure transaction log is ordered here
            ->map(new TransactionToCommission(
                new CommissionCalculator($this->currencies)
            ))
            ->map(function (Amount $commission) {
                return $commission->getAmount();
            })
            ->get();

        return $transactions;
    }

    private function readLines(): array
    {
        if (!($file = fopen($this->infile, 'r'))) {
            throw new \RuntimeException('Failed to open file');
        }

        $lines = [];
        while (is_array($line = fgetcsv($file, null, ','))) {
            $lines[] = $line;
        }

        return $lines;
    }
}