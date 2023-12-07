<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationException extends \Exception
{
    private ConstraintViolationListInterface $violations;

    public function __construct(ConstraintViolationListInterface $violations, $message = "Validation failed", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->violations = $violations;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
