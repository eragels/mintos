<?php

namespace App\Service\Formatter\Transaction;

use App\Entity\Transaction;
use App\Service\Formatter\FormatterInterface;

final class TransactionResponseFormatter implements FormatterInterface
{
    public function format(array $data): array
    {
        /** @var Transaction $transaction */
        $transaction = $data['transaction'] ?? null;
        if (!$transaction) {
            return [];
        }

        return [
            'id' => $transaction->getId(),
            'senderAccount' => $transaction->getSenderAccount()->getId(),
            'receiverAccount' => $transaction->getReceiverAccount()->getId(),
            'currency' => $transaction->getCurrency()->getName(),
            'amount' => $transaction->getAmount(),
            'timestamp' => $transaction->getTimestamp()->format('Y-m-d H:i:s'),
        ];
    }
}
