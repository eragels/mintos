<?php

namespace App\Entity;

class AccountTransactionsCollection
{
    public function __construct(
        private readonly array $sentTransactions,
        private readonly array $receivedTransactions
    ) {}

    public function getSentTransactions(): array
    {
        return $this->sentTransactions;
    }

    public function getReceivedTransactions(): array
    {
        return $this->receivedTransactions;
    }
}