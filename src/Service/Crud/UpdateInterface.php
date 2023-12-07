<?php

namespace App\Service\Crud;

use App\Service\DTOInterface;

interface UpdateInterface
{
    public function update(object $entity, DTOInterface $dto);
}