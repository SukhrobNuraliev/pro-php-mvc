<?php

namespace Framework\Validation\Rule;

class RequiredRule implements Rule
{

    public function validate(array $data, string $field, array $params): bool
    {
         return !empty($data[$field]);
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "{$field} is required";
    }
}