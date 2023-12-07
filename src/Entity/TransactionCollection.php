<?php

namespace App\Entity;

class TransactionCollection
{
    public function __construct(private readonly array $transactions) {}

    public function getTransactions(): array
    {
        return $this->transactions;
    }
}