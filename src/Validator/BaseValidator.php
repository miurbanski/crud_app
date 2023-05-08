<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Response;

class BaseValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(object $entity)
    {
        $errors = $this->validator->validate($entity);

        $messages = [];

        foreach ($errors as $message) {
            $messages['validation_failed'][] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        return $messages;
    }
}