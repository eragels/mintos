<?php

namespace App\Service\Crud;

interface DeleteInterface
{
    public function delete(object $entity): void;
}