<?php

namespace App\Service\Crud\Account;

use App\Entity\Client;
use App\Entity\Currency;
use App\Service\DTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class AccountDTO implements DTOInterface
{
    #[Assert\NotBlank(message: 'Account client should not be blank.')]
    private int $clientId;

    #[Assert\NotBlank(message: 'Account currency should not be blank.')]
    private int $currencyId;

    #[Assert\NotBlank(message: 'Account balance should not be blank.')]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'Account balance must be zero or greater.'
    )]
    private float $balance;

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function setCurrencyId(int $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    public static function fromArray(array $data): self
    {
        $self = new self();

        if (isset($data['client'])) {
            $self->setClientId($data['client']);
        }

        if (isset($data['currency'])) {
            $self->setCurrencyId($data['currency']);
        }

        if (isset($data['balance'])) {
            $self->setBalance($data['balance']);
        }

        return $self;
    }
}
