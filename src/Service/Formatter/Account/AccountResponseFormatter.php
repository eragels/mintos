<?php

namespace App\Service\Formatter\Account;

use App\Entity\Account;
use App\Service\Formatter\FormatterInterface;
use App\Service\Formatter\Transaction\TransactionCollectionFormatter;

final class AccountResponseFormatter implements FormatterInterface
{
    public function __construct(private readonly TransactionCollectionFormatter $transactionFormatter) {}

    public function format(array $data): array
    {
        /** @var Account $account */
        $account = $data['account'] ?? null;
        if (!$account) {
            return [];
        }

        return [
            'id' => $account->getId(),
            'client' => $account->getClient()->getId(),
            'currency' => $account->getCurrency()?->getName(),
            'balance' => $account->getBalance(),
            'receivedTransactions' => $this->formatTransactions($account->getReceivedTransactions()),
            'sentTransactions' => $this->formatTransactions($account->getSentTransactions()),
        ];
    }

    private function formatTransactions($transactions): array
    {
        return array_map(function ($collection) {
            return $this->transactionFormatter->format(['collection' => $collection]);
        }, $transactions->toArray());
    }

}
