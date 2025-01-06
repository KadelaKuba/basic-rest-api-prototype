<?php

namespace App\Application\Api\Exception;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationViolationsBadRequestException extends HttpBadRequestException
{
    public static function create(
        ServerRequestInterface $request,
        ConstraintViolationListInterface $errors
    ): ValidationViolationsBadRequestException {
        return new self(
            $request,
            $errors
        );
    }

    private function __construct(ServerRequestInterface $request, ConstraintViolationListInterface $errors)
    {
        $formattedErrors = [];
        foreach ($errors as $violation) {
            $formattedErrors[] = '[' . $violation->getPropertyPath() . '] - ' . $violation->getMessage();
        }

        $formattedMessage = 'Request body data validation violations: ' . implode(', ', $formattedErrors);

        parent::__construct($request, $formattedMessage);
    }
}
