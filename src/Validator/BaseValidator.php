<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseValidator
{
    protected ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(object $entity): array
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