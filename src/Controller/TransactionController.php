<?php

namespace App\Controller;

use App\Exception\ValidationException;
use App\Service\Crud\Transaction\TransactionCrudService;
use App\Service\Crud\Transaction\TransactionDTO;
use App\Service\Formatter\FormatterContainer;
use App\Utils\ValidationErrorFormatter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    public function __construct(
        private readonly TransactionCrudService $crudService,
        private readonly FormatterContainer     $formatterFactory,
        private readonly LoggerInterface        $logger
    ) {}

    #[Route('/transactions', name: 'post_transaction', methods: ['POST'])]
    public function postTransaction(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $dto = TransactionDTO::fromArray($data);

            $transaction = $this->crudService->create($dto);
            $formatter = $this->formatterFactory->getFormatter($transaction);

            return $this->json($formatter ? $formatter->format(['transaction' => $transaction]) : []);
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

            return $this->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
