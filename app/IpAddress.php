<?php

namespace App;

class IpAddress extends Model
{
    /**
     * Get all the managers using this ip address
     */
    public function managers()
    {
        return $this->belongsToMany(Manager::class);
    }
}
