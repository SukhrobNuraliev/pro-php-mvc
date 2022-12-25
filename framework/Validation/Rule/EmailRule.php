<?php

namespace Framework\Validation\Rule;

class EmailRule implements Rule
{

    public function validate(array $data, string $field, array $params): bool
    {
        if (empty($data[$field])) {
            return true;
        }
        return str_contains($data[$field], '@');
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "{$field} should be an email";
    }
}