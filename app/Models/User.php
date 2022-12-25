<?php

namespace App\Models;

use Framework\Database\Model;
use Framework\Database\Relationship;

#[Table('users')]
class User extends Model
{
    protected string $table = 'users';

    public function profile(): Relationship
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    /**
     * @throws \Exception
     */
    public function orders(): mixed
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}