<?php

namespace App\Service\CurrencyExchange;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CurrencyExchangeService
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiUrl,
        private readonly string $apiKey,
        private readonly LoggerInterface $logger
    ) {}

    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): ?float
    {
        $rate = $this->getExchangeRate($fromCurrency, $toCurrency);

        if ($rate === null) {
            return null;
        }

        return $amount * $rate;
    }

    private function getExchangeRate(string $baseCurrency, string $targetCurrency): ?float
    {
        $retryDelay = CurrencyExchangeEnum::InitialRetryDelay->value;

        // Exponential backoff
        for ($attempt = 0; $attempt < CurrencyExchangeEnum::MaxRetries->value; $attempt++) {
            try {
                $response = $this->client->request('GET', $this->apiUrl, [
                    'query' => [
                        'apikey' => $this->apiKey,
                        'base_currency' => strtoupper($baseCurrency),
                        'currencies' => strtoupper($targetCurrency),
                    ],
                ]);

                $data = $response->toArray();

                return $data['data'][strtoupper($targetCurrency)] ?? null;
            } catch (\Exception $e) {
                usleep($retryDelay);
                $retryDelay *= CurrencyExchangeEnum::RetryMultiplier->value;

                $this->logger->error("Error fetching exchange rate: " . $e->getMessage());
            }
        }

        throw new \Exception('Error fetching exchange rate');
    }
}
