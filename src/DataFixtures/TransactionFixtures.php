<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use App\Entity\Account;
use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var Account $account1 */
        $account1 = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE_1);
        /** @var Account $account4 */
        $account4 = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE_4);
        /** @var Currency $currency */
        $currency = $this->getReference(CurrencyFixtures::CURRENCY_USD_REFERENCE);

        for ($i = 0; $i < 5; $i++) {
            $transaction = new Transaction();
            $transaction->setSenderAccount($account1);
            $transaction->setReceiverAccount($account4);
            $transaction->setAmount(rand(100, 1000));
            $transaction->setCurrency($currency);
            $transaction->setTimestamp(new \DateTime());

            $manager->persist($transaction);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            CurrencyFixtures::class,
        ];
    }
}
