<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class CaeruStandardizeStringInput extends TransformsRequest
{
    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {

        if (is_string($value) && ($value !== '')) {

            // Convert zen-kaku numbers and alphabet and space to han-kaku (the normal ones)
            $value = mb_convert_kana($value, 'a');
            $value = mb_convert_kana($value, 's');

            // Then convert the han-kaku katakana to zen-kaku (the normal katakana )
            $value = mb_convert_kana($value, 'K');
            
        }

        return $value;
    }
}
