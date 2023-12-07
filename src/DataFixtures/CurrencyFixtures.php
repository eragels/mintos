<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use App\Enum\CurrencyEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture
{
    public const CURRENCY_EUR_REFERENCE = 'eur-currency';
    public const CURRENCY_USD_REFERENCE = 'usd-currency';
    public const CURRENCY_GBP_REFERENCE = 'gbp-currency';

    public function load(ObjectManager $manager)
    {
        $eurCurrency = new Currency();
        $eurCurrency->setName(CurrencyEnum::EUR->value);
        $manager->persist($eurCurrency);
        $this->addReference(self::CURRENCY_EUR_REFERENCE, $eurCurrency);

        $usdCurrency = new Currency();
        $usdCurrency->setName(CurrencyEnum::USD->value);
        $manager->persist($usdCurrency);
        $this->addReference(self::CURRENCY_USD_REFERENCE, $usdCurrency);

        $gbpCurrency = new Currency();
        $gbpCurrency->setName(CurrencyEnum::GBP->value);
        $manager->persist($gbpCurrency);
        $this->addReference(self::CURRENCY_GBP_REFERENCE, $gbpCurrency);

        $manager->flush();
    }
}