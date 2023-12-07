<?php

namespace App\Controller;

use App\Entity\AccountTransactionsCollection;
use App\Exception\ValidationException;
use App\Repository\TransactionRepository;
use App\Service\Crud\Account\AccountCrudService;
use App\Service\Crud\Account\AccountDTO;
use App\Service\Formatter\FormatterContainer;
use App\Utils\ValidationErrorFormatter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly AccountCrudService    $crudService,
        private readonly FormatterContainer    $formatterFactory,
        private readonly LoggerInterface       $logger
    ) {}

    #[Route('/accounts/{id}/transactions', name: 'get_account_transactions', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getAccountTransactions(int $id, #[MapQueryParameter('offset')] int $offset = 0, #[MapQueryParameter('limit')] int $limit = 10): JsonResponse
    {
        $sentTransactions = $this->transactionRepository->findSentTransactionsByAccount($id, $offset, $limit);
        $receivedTransactions = $this->transactionRepository->findReceivedTransactionsByAccount($id, $offset, $limit);

        $transactionsCollection = new AccountTransactionsCollection($sentTransactions, $receivedTransactions);
        $formatter = $this->formatterFactory->getFormatter($transactionsCollection);

        return $this->json($formatter ? $formatter->format(['collection' => $transactionsCollection]) : []);
    }

    #[Route('/accounts', name: 'post_accounts', methods: ['POST'])]
    public function postAccount(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $dto = AccountDTO::fromArray($data);

            $account = $this->crudService->create($dto);
            $formatter = $this->formatterFactory->getFormatter($account);

            return $this->json($formatter ? $formatter->format(['account' => $account]) : []);
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
