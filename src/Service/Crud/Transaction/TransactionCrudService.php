<?php

namespace App\Service\Crud\Transaction;

use App\Entity\Account;
use App\Entity\Currency;
use App\Entity\Transaction;
use App\Exception\ValidationException;
use App\Service\Crud\CreateInterface;
use App\Service\CurrencyExchange\CurrencyExchangeService;
use App\Service\DTOInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TransactionCrudService implements CreateInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CurrencyExchangeService $exchangeService,
        private readonly ValidatorInterface $validator
    ) {}

    public function create(DTOInterface $dto): Transaction
    {
        if (!$dto instanceof TransactionDTO) {
            throw new \InvalidArgumentException('Expected a TransactionDTO');
        }

        $errors = $this->validator->validate($dto);
        if ($errors->count() > 0) {
            throw new ValidationException($errors);
        }

        /** @var Account $senderAccount */
        $senderAccount = $this->entityManager
            ->getRepository(Account::class)
            ->find($dto->getSenderAccountId());

        if (!$senderAccount) {
            throw new \InvalidArgumentException('Sender account not found');
        }

        /** @var Account $receiverAccount */
        $receiverAccount = $this->entityManager
            ->getRepository(Account::class)
            ->find($dto->getReceiverAccountId());

        if (!$receiverAccount) {
            throw new \InvalidArgumentException('Receiver account not found');
        }

        /** @var Currency $transactionCurrency */
        $transactionCurrency = $this->entityManager
            ->getRepository(Currency::class)
            ->find($dto->getCurrencyId());

        if (!$transactionCurrency) {
            throw new \InvalidArgumentException('Currency not found');
        }

        if ($senderAccount->getId() === $receiverAccount->getId()) {
            throw new \InvalidArgumentException('You cant send to yourself');
        }

        if ($transactionCurrency->getId() !== $senderAccount->getCurrency()?->getId()) {
            throw new \InvalidArgumentException('Transaction currency must match the sender account currency');
        }

        $baseAmount = $dto->getAmount();
        $receiveAmount = $dto->getAmount();

        if ($senderAccount->getCurrency()?->getId() !== $receiverAccount->getCurrency()?->getId()) {
            $receiveAmount = $this->exchangeService->convertCurrency($receiveAmount, $senderAccount->getCurrency()?->getName(), $receiverAccount->getCurrency()?->getName());
        }

        $transaction = new Transaction();
        $transaction
            ->setSenderAccount($senderAccount)
            ->setReceiverAccount($receiverAccount)
            ->setCurrency($transactionCurrency)
            ->setAmount($receiveAmount)
            ->setTimestamp(new \DateTime());

        $this->entityManager->persist($transaction);

        $senderAccount->setBalance($senderAccount->getBalance() - $baseAmount);
        $receiverAccount->setBalance($receiverAccount->getBalance() + $receiveAmount);

        $this->entityManager->persist($senderAccount);
        $this->entityManager->persist($receiverAccount);

        $this->entityManager->flush();

        return $transaction;
    }
}
