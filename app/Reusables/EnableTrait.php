<?php

namespace App\Reusables;

use Illuminate\Support\Facades\Hash;

trait EnableTrait
{
    /**
     * Scope a query to get the enable model .
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnable($query)
    {
        return $query->where($this->getTable() . '.enable', true);
    }
}