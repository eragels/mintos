<?php

namespace App\Service\Formatter\Transaction;

use App\Entity\TransactionCollection;
use App\Service\Formatter\FormatterInterface;

final class TransactionCollectionFormatter implements FormatterInterface
{
    public function __construct(private readonly TransactionResponseFormatter $transactionResponseFormatter) {}

    public function format(array $data): array
    {
        /** @var TransactionCollection $collection */
        $collection = $data['collection'] ?? null;
        if (!$collection) {
            return [];
        }

        return $this->formatTransactions($collection->getTransactions());
    }

    private function formatTransactions(array $transactions): array
    {
        return array_map(function ($transaction) {
            return $this->transactionResponseFormatter->format(['transaction' => $transaction]);
        }, $transactions);
    }
}