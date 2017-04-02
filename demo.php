<?php

require_once 'vendor/autoload.php';

if (!isset($argv[1]) || !is_file($infile = $argv[1])) {
    printf("php %s <infile>\n", __FILE__);
    exit(1);
}

$transactionsQuery = new \Query\CalculateCommissionsFromTransactionsCSV(
    $infile,
    [
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
    ]
);

try {
    $transactions = $transactionsQuery->execute();
    echo implode("\n", $transactions);
} catch (Exception $ex) {
    echo $ex->getMessage();
    exit(1);
}
