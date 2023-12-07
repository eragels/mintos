<?php

namespace App\Tests\Integration;

use App\DataFixtures\AccountFixtures;
use App\DataFixtures\ClientFixtures;
use App\DataFixtures\CurrencyFixtures;
use App\DataFixtures\TransactionFixtures;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TransactionControllerTest extends WebTestCase
{
    private $entityManager;
    private $client;
    private $databaseTool;
    private $fixtures;
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->databaseTool = $this->getContainer()->get(DatabaseToolCollection::class)->get();
        $this->fixtures = $this->databaseTool->loadFixtures([
            CurrencyFixtures::class,
            ClientFixtures::class,
            AccountFixtures::class,
            TransactionFixtures::class
        ]);
        $this->repository = $this->fixtures->getReferenceRepository();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    public function testPostTransactionSuccess(): void
    {
        $amount = 100;

        $senderAccountFixture = $this->repository->getReference(AccountFixtures::ACCOUNT_REFERENCE_1);
        $senderAccountId = $senderAccountFixture->getId();

        $receiverAccountFixture = $this->repository->getReference(AccountFixtures::ACCOUNT_REFERENCE_4);
        $receiverAccountId = $receiverAccountFixture->getId();

        $usdCurrencyFixtures = $this->repository->getReference(CurrencyFixtures::CURRENCY_USD_REFERENCE);
        $usdCurrencyId = $usdCurrencyFixtures->getId();

        $requestData = [
            'senderAccount' => $senderAccountId,
            'receiverAccount' => $receiverAccountId,
            'currency' => $usdCurrencyId,
            'amount' => $amount
        ];

        $this->client->request('POST', '/transactions', [], [], [], json_encode($requestData));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($amount, $responseData['amount']);
    }

    public function testPostTransactionShouldConvertCurrency(): void
    {
        $amount = 100;

        $senderAccountFixture = $this->repository->getReference(AccountFixtures::ACCOUNT_REFERENCE_2);
        $senderAccountId = $senderAccountFixture->getId();

        $receiverAccountFixture = $this->repository->getReference(AccountFixtures::ACCOUNT_REFERENCE_4);
        $receiverAccountId = $receiverAccountFixture->getId();

        $eurCurrencyFixtures = $this->repository->getReference(CurrencyFixtures::CURRENCY_EUR_REFERENCE);
        $eurCurrencyId = $eurCurrencyFixtures->getId();

        $requestData = [
            'senderAccount' => $senderAccountId,
            'receiverAccount' => $receiverAccountId,
            'currency' => $eurCurrencyId,
            'amount' => $amount
        ];

        $this->client->request('POST', '/transactions', [], [], [], json_encode($requestData));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertGreaterThan($amount, $responseData['amount']); // not stable, better to mock exchange service
    }

    public function testPostTransactionInvalidJson(): void
    {
        $this->client->request('POST', '/transactions', [], [], [], '{invalid json}');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPostTransactionWithInvalidData(): void
    {
        $requestData = [
            'senderAccountId' => 9999,
            'receiverAccountId' => 9999,
            'currencyId' => 9999,
            'amount' => -1000.0
        ];

        $this->client->request('POST', '/transactions', [], [], [], json_encode($requestData));
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

}
