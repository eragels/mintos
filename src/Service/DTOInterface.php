<?php

namespace App\Service;

interface DTOInterface
{
    public static function fromArray(array $data): self;
}
