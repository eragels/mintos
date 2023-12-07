<?php

namespace App\Controller;

use App\Entity\AccountCollection;
use App\Entity\Client;
use App\Exception\ValidationException;
use App\Repository\AccountRepository;
use App\Service\Crud\Client\ClientCrudService;
use App\Service\Crud\Client\ClientDTO;
use App\Service\Formatter\FormatterContainer;
use App\Utils\ValidationErrorFormatter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    public function __construct(
        private readonly ClientCrudService  $crudService,
        private readonly LoggerInterface    $logger,
        private readonly FormatterContainer $formatterFactory,
        private readonly AccountRepository  $accountRepository
    ) {}

    #[Route('/clients/{id}/accounts', name: 'get_clients_accounts', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getClientAccountsById(int $id): JsonResponse
    {
        $accounts = $this->accountRepository->findBy(['client' => $id]);

        $accountsCollection = new AccountCollection($accounts);
        $formatter = $this->formatterFactory->getFormatter($accountsCollection);

        return $this->json($formatter ? $formatter->format(['collection' => $accountsCollection]) : []);
    }

    #[Route('/clients', name: 'post_client', methods: ['POST'])]
    public function postClient(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $dto = ClientDTO::fromArray($data);

            $client = $this->crudService->create($dto);
            $formatter = $this->formatterFactory->getFormatter($client);

            return $this->json($formatter ? $formatter->format(['client' => $client]) : []);
        } catch (\JsonException $e) {
            $this->logger->error('JSON parse error: ' . $e->getMessage());

            return $this->json(['message' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        } catch (ValidationException $e) {
            $this->logger->error('Validation error: ' . $e->getMessage());
            $violations = ValidationErrorFormatter::format($e->getViolations());

            return $this->json(['message' => $e->getMessage(), 'validation' => $violations], Response::HTTP_BAD_REQUEST);
        } catch (\InvalidArgumentException $e) {
            $this->logger->error('Invalid argument error: ' . $e->getMessage());

            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error: ' . $e->getMessage());

            return $this->json(['message' => 'An unexpected error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
