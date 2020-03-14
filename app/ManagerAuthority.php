<?php

namespace App;

class ManagerAuthority extends Model
{

    /**
     * Constants of the authority
     */
    const CHANGE        = 1;
    const BROWSE        = 2;
    const NOTHING       = 0;

    /**
     * Get the manager
     */
    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

}
