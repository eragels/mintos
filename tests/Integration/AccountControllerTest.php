<?php

namespace App\Tests\Integration;

use App\DataFixtures\AccountFixtures;
use App\DataFixtures\ClientFixtures;
use App\DataFixtures\CurrencyFixtures;
use App\DataFixtures\TransactionFixtures;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\ORMDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccountControllerTest extends WebTestCase
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
        $this->assertInstanceOf(ORMDatabaseTool::class, $this->databaseTool);
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

    public function testPostAccountSuccess(): void
    {
        $requestData = [
            'client' => 1,
            'currency' => 3,
            'balance' => 1000.0
        ];

        $this->client->request('POST', '/accounts', [], [], [], json_encode($requestData));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(1000.0, $responseData['balance']);
    }

    public function testPostAccountInvalidJson(): void
    {
        $this->client->request('POST', '/accounts', [], [], [], '{invalid json}');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPostAccountWithInvalidData(): void
    {
        $requestData = [
            'clientId' => 9999,
            'currencyId' => 9999,
            'balance' => -1000.0
        ];

        $this->client->request('POST', '/accounts', [], [], [], json_encode($requestData));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testGetAccountTransactionsSuccess(): void
    {
        $accountFixture = $this->repository->getReference(AccountFixtures::ACCOUNT_REFERENCE_1);
        $accountWithTransactionsId = $accountFixture->getId();

        $this->client->request('GET', '/accounts/' . $accountWithTransactionsId . '/transactions');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertArrayHasKey('transactions', $responseData);
        $this->assertArrayHasKey('sent', $responseData['transactions']);
        $this->assertArrayHasKey('received', $responseData['transactions']);

        $sentTransactions = $responseData['transactions']['sent'];
        $this->assertNotEmpty($sentTransactions);

        foreach ($sentTransactions as $wrappedTransaction) {
            $transaction = $wrappedTransaction;

            $this->assertArrayHasKey('id', $transaction);
            $this->assertArrayHasKey('senderAccount', $transaction);
            $this->assertArrayHasKey('receiverAccount', $transaction);
            $this->assertArrayHasKey('currency', $transaction);
            $this->assertArrayHasKey('amount', $transaction);
            $this->assertArrayHasKey('timestamp', $transaction);

            $this->assertEquals(1, $transaction['senderAccount']);
            $this->assertEquals(4, $transaction['receiverAccount']);
            $this->assertEquals('usd', $transaction['currency']);
        }

        $receivedTransactions = $responseData['transactions']['received'];
        $this->assertEmpty($receivedTransactions);
    }

    public function testGetAccountTransactionsForNonExistentAccount(): void
    {
        $this->client->request('GET', '/accounts/9999/transactions');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEmpty($responseData['transactions']['sent']);
        $this->assertEmpty($responseData['transactions']['received']);
    }

}
