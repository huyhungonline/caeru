<?php

namespace App;

class EmployeeWorkingInformationSnapshot extends Model
{

    /**
     * Get the modify request instance of this snapshot
     */
    public function modifyRequest()
    {
        return $this->belongsTo(EmployeeWorkingInformationModifyRequest::class);
    }
}
