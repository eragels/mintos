<?php

namespace App\Service\Formatter;

use App\Entity\Account;
use App\Entity\AccountCollection;
use App\Entity\AccountTransactionsCollection;
use App\Entity\Client;
use App\Entity\Transaction;
use App\Entity\TransactionCollection;

class FormatterContainer
{
    private array $formatters;

    public function __construct(array $formatters)
    {
        $this->formatters = $formatters;
    }

    public function getFormatter($entity): ?FormatterInterface
    {
        return match ($entity::class) {
            Account::class => $this->formatters[Account::class] ?? null,
            AccountCollection::class => $this->formatters[AccountCollection::class] ?? null,
            AccountTransactionsCollection::class => $this->formatters[AccountTransactionsCollection::class] ?? null,
            Client::class => $this->formatters[Client::class] ?? null,
            Transaction::class => $this->formatters[Transaction::class] ?? null,
            TransactionCollection::class => $this->formatters[TransactionCollection::class] ?? null,
            default => null,
        };
    }

}
