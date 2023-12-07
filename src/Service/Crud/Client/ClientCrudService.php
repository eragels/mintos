<?php

namespace App\Service\Crud\Client;

use App\Entity\Client;
use App\Exception\ValidationException;
use App\Service\Crud\CreateInterface;
use App\Service\DTOInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ClientCrudService implements CreateInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {}

    public function create(DTOInterface $dto): Client
    {
        if (!$dto instanceof ClientDTO) {
            throw new \InvalidArgumentException('Expected a ClientDTO');
        }

        $errors = $this->validator->validate($dto);
        if ($errors->count() > 0) {
            throw new ValidationException($errors);
        }

        $client = new Client();
        $client->setName($dto->getName());

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $client;
    }
}
