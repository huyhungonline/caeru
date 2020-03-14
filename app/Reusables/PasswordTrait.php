<?php

namespace App\Reusables;

use Illuminate\Support\Facades\Hash;

trait PasswordTrait
{
    /**
     * Mutator for the password
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}