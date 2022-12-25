<?php

namespace App\Models;

class Profile extends \Framework\Database\Model
{
    protected string $table = 'profiles';

    public function user(): mixed
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}