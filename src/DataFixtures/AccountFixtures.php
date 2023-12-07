<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\Client;
use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AccountFixtures extends Fixture implements DependentFixtureInterface
{
    public const ACCOUNT_REFERENCE_1 = 'test-account-1';
    public const ACCOUNT_REFERENCE_2 = 'test-account-2';
    public const ACCOUNT_REFERENCE_3 = 'test-account-3';
    public const ACCOUNT_REFERENCE_4 = 'test-account-4';

    public function load(ObjectManager $manager)
    {
        $this->createAccount($manager, ClientFixtures::CLIENT_REFERENCE, CurrencyFixtures::CURRENCY_USD_REFERENCE, self::ACCOUNT_REFERENCE_1);
        $this->createAccount($manager, ClientFixtures::CLIENT_REFERENCE, CurrencyFixtures::CURRENCY_EUR_REFERENCE, self::ACCOUNT_REFERENCE_2);

        $this->createAccount($manager, ClientFixtures::CLIENT_REFERENCE_WITH_ACCOUNT, CurrencyFixtures::CURRENCY_GBP_REFERENCE, self::ACCOUNT_REFERENCE_3);
        $this->createAccount($manager, ClientFixtures::CLIENT_REFERENCE_WITH_ACCOUNT, CurrencyFixtures::CURRENCY_USD_REFERENCE, self::ACCOUNT_REFERENCE_4);

        $manager->flush();
    }

    private function createAccount(ObjectManager $manager, string $clientRef, string $currencyRef, string $accountRef): void
    {
        /** @var Client $client */
        $client = $this->getReference($clientRef);
        /** @var Currency $currency */
        $currency = $this->getReference($currencyRef);

        $account = new Account();
        $account->setClient($client);
        $account->setCurrency($currency);
        $account->setBalance(1000.0);

        $manager->persist($account);
        $this->addReference($accountRef, $account);
    }

    public function getDependencies(): array
    {
        return [
            CurrencyFixtures::class,
            ClientFixtures::class,
        ];
    }
}
