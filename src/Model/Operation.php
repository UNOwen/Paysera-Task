<?php

declare(strict_types=1);

namespace Model;

class Operation
{
    const CASH_IN = 1;
    const CASH_OUT = 2;

    private $type;

    public function __construct(int $type)
    {
        $this->type = $type;
    }

    public function getType(): int
    {
        return $this->type;
    }
}