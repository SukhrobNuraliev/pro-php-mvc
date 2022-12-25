<?php

namespace App\Models;

use Framework\Database\Model;

function toInt($value): int
{
    return (int)$value;
}

class Product extends Model
{
    protected string $table = 'products';
    protected array $casts = [
        'id' => 'App\Models\toInt',
    ];

    protected function setDescriptionAttribute(string $value): string
    {
        $limit = 50;
        $ending = '...';
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }
        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $ending;
    }
}