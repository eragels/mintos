<?php

namespace App\Entity;

class AccountCollection
{
    public function __construct(private readonly array $accounts) {}

    public function getAccounts(): array
    {
        return $this->accounts;
    }
}