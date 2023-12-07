<?php

namespace App\Service\Crud\Account;

use App\Entity\Account;
use App\Entity\Client;
use App\Entity\Currency;
use App\Exception\ValidationException;
use App\Service\Crud\CreateInterface;
use App\Service\DTOInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AccountCrudService implements CreateInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator
    ) {}

    public function create(DTOInterface $dto): Account
    {
        if (!$dto instanceof AccountDTO) {
            throw new \InvalidArgumentException('Expected a AccountDTO');
        }

        $errors = $this->validator->validate($dto);
        if ($errors->count() > 0) {
            throw new ValidationException($errors);
        }

        /** @var Client $client */
        $client = $this->entityManager
            ->getRepository(Client::class)
            ->find($dto->getClientId());

        if (!$client) {
            throw new \InvalidArgumentException('Client not found');
        }

        /** @var Currency $currency */
        $currency = $this->entityManager
            ->getRepository(Currency::class)
            ->find($dto->getCurrencyId());

        if (!$currency) {
            throw new \InvalidArgumentException('Currency not found');
        }

        /** @var Account $account */
        foreach ($client->getAccounts() as $account) {
            if ($account->getCurrency()?->getId() === $currency->getId()) {
                throw new \InvalidArgumentException("You already have an account with {$currency->getName()}");
            }
        }

        $account = new Account();
        $account
            ->setClient($client)
            ->setCurrency($currency)
            ->setBalance($dto->getBalance());

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        return $account;
    }
}
