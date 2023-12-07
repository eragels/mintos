<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture
{
    public const CLIENT_REFERENCE = 'test-client';
    public const CLIENT_REFERENCE_WITH_ACCOUNT = 'test-client-with-account';
    public const CLIENT_REFERENCE_WITHOUT_ACCOUNT = 'test-client-without-account';

    public function load(ObjectManager $manager)
    {
        $client = new Client();
        $client->setName('Test client 1');
        $manager->persist($client);
        $this->addReference(self::CLIENT_REFERENCE, $client);

        $clientWithAccount = new Client();
        $clientWithAccount->setName('Test client 2');
        $manager->persist($clientWithAccount);
        $this->addReference(self::CLIENT_REFERENCE_WITH_ACCOUNT, $clientWithAccount);

        $clientWithoutAccount = new Client();
        $clientWithoutAccount->setName('Test client 3');
        $manager->persist($clientWithoutAccount);
        $this->addReference(self::CLIENT_REFERENCE_WITHOUT_ACCOUNT, $clientWithoutAccount);

        $manager->flush();
    }
}
