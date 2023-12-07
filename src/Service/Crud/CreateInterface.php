<?php

namespace App\Service\Crud;

use App\Service\DTOInterface;

interface CreateInterface
{
    public function create(DTOInterface $dto);
}