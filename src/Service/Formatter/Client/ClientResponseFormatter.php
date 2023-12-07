<?php

namespace App\Service\Formatter\Client;

use App\Entity\Client;
use App\Service\Formatter\FormatterInterface;

final class ClientResponseFormatter implements FormatterInterface
{
    public function format(array $data): array
    {
        /** @var Client $client */
        $client = $data['client'] ?? null;
        if (!$client) {
            return [];
        }

        return [
            'id' => $client->getId(),
            'name' => $client->getName(),
        ];
    }

}
