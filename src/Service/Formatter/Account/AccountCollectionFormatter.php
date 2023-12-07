<?php

namespace App\Service\Formatter\Account;

use App\Entity\AccountCollection;
use App\Service\Formatter\FormatterInterface;

final class AccountCollectionFormatter implements FormatterInterface
{
    public function __construct(private readonly AccountResponseFormatter $accountResponseFormatter) {}

    public function format(array $data): array
    {
        /** @var AccountCollection $collection */
        $collection = $data['collection'] ?? null;
        if (!$collection) {
            return [];
        }

        return [
            'accounts' => $this->formatTransactions($collection->getAccounts())
        ];
    }

    private function formatTransactions(array $accounts): array
    {
        return array_map(function ($account) {
            return $this->accountResponseFormatter->format(['account' => $account]);
        }, $accounts);
    }
}
