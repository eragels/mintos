<?php

namespace App\Tests\Integration;

use App\DataFixtures\AccountFixtures;
use App\DataFixtures\ClientFixtures;
use App\DataFixtures\CurrencyFixtures;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\ORMDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ClientControllerTest extends WebTestCase
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
            AccountFixtures::class
        ]);
        $this->repository = $this->fixtures->getReferenceRepository();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    public function testPostClient(): void
    {
        $requestData = [
            'name' => 'Test Client',
        ];

        $this->client->request('POST', '/clients', [], [], [], json_encode($requestData));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Test Client', $responseData['name']);
    }

    public function testPostClientInvalidJson(): void
    {
        $this->client->request('POST', '/clients', [], [], [], '{invalid json}');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPostClientWithInvalidData(): void
    {
        $requestData = ['name' => ''];
        $this->client->request('POST', '/clients', [], [], [], json_encode($requestData));
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testGetClientAccounts(): void
    {
        $clientFixture = $this->repository->getReference(ClientFixtures::CLIENT_REFERENCE_WITH_ACCOUNT);
        $clientWithoutAccountsId = $clientFixture->getId();

        $this->client->request('GET', '/clients/' . $clientWithoutAccountsId . '/accounts');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertNotEmpty($responseData['accounts']);
    }

    public function testGetClientAccountsByIdWithNoAccounts(): void
    {
        $clientFixture = $this->repository->getReference(ClientFixtures::CLIENT_REFERENCE_WITHOUT_ACCOUNT);
        $clientWithoutAccountsId = $clientFixture->getId();

        $this->client->request('GET', '/clients/' . $clientWithoutAccountsId . '/accounts');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEmpty($responseData['accounts']);
    }
}
