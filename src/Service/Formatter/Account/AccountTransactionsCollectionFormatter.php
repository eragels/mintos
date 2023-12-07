<?php

namespace App\Service\Formatter\Account;

use App\Entity\AccountTransactionsCollection;
use App\Service\Formatter\FormatterInterface;
use App\Service\Formatter\Transaction\TransactionResponseFormatter;

final class AccountTransactionsCollectionFormatter implements FormatterInterface
{
    public function __construct(private readonly TransactionResponseFormatter $transactionFormatter) {}

    public function format(array $data): array
    {
        /** @var AccountTransactionsCollection $collection */
        $collection = $data['collection'] ?? null;
        if (!$collection) {
            return [];
        }

        return [
            'transactions' => [
                'sent' => $this->formatTransactions($collection->getSentTransactions()),
                'received' => $this->formatTransactions($collection->getReceivedTransactions()),
            ],
        ];
    }

    private function formatTransactions(array $transactions): array
    {
        return array_map(function ($transaction) {
            return $this->transactionFormatter->format(['transaction' => $transaction]);
        }, $transactions);
    }
}