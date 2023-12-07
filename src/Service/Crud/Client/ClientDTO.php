<?php

namespace App\Service\Crud\Client;

use App\Service\DTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ClientDTO implements DTOInterface
{
    #[Assert\NotBlank(message: 'Client name should not be blank.')]
    #[Assert\Length(
        min: 4,
        minMessage: 'Client name must be at least {{ limit }} characters long.'
    )]
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public static function fromArray(array $data): self
    {
        $self = new self();

        if (isset($data['name'])) {
            $self->setName($data['name']);
        }

        return $self;
    }
}
