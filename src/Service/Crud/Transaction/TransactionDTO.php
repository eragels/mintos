<?php

namespace App\Service\Crud\Transaction;

use App\Service\DTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class TransactionDTO implements DTOInterface
{
    #[Assert\NotBlank(message: 'Transaction sender should not be blank.')]
    private int $senderAccountId;

    #[Assert\NotBlank(message: 'Transaction receiver should not be blank.')]
    private int $receiverAccountId;

    #[Assert\NotBlank(message: 'Transaction currency should not be blank.')]
    private int $currencyId;

    #[Assert\NotBlank(message: 'Transaction amount should not be blank.')]
    #[Assert\GreaterThanOrEqual(
        value: 1,
        message: 'Transaction amount must be 1.00 or greater.'
    )]
    private float $amount;

    public function getSenderAccountId(): int
    {
        return $this->senderAccountId;
    }

    public function setSenderAccountId(int $senderAccountId): void
    {
        $this->senderAccountId = $senderAccountId;
    }

    public function getReceiverAccountId(): int
    {
        return $this->receiverAccountId;
    }

    public function setReceiverAccountId(int $receiverAccountId): void
    {
        $this->receiverAccountId = $receiverAccountId;
    }

    public function getCurrencyId(): string
    {
        return $this->currencyId;
    }

    public function setCurrencyId(int $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public static function fromArray(array $data): self
    {
        $self = new self();

        if (isset($data['senderAccount'])) {
            $self->setSenderAccountId($data['senderAccount']);
        }

        if (isset($data['receiverAccount'])) {
            $self->setReceiverAccountId($data['receiverAccount']);
        }

        if (isset($data['currency'])) {
            $self->setCurrencyId($data['currency']);
        }

        if (isset($data['amount'])) {
            $self->setAmount($data['amount']);
        }

        return $self;
    }
}
