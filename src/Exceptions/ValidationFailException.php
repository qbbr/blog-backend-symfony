<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidationFailException extends HttpException
{
    private array $errors;

    public function __construct($errors, \Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation failed.', $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
