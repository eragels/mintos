<?php

namespace App\Service\Formatter;

interface FormatterInterface
{
    public function format(array $data): array;
}
