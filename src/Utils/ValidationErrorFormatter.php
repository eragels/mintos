<?php

namespace App\Utils;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationErrorFormatter
{
    public static function format(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $errors[$propertyPath] = $violation->getMessage();
        }
        return $errors;
    }
}
