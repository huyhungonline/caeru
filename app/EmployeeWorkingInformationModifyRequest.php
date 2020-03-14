<?php

namespace App;

class EmployeeWorkingInformationModifyRequest extends Model
{

    /**
     * Constant for the request statuses
     */
    const APPROVED      = 1;
    const CONSIDERING   = 2;
    const DENIED        = 0;

    /**
     * Get the working information instance of this modify request
     */
    public function employeeWorkingInformation()
    {
        return $this->belongsTo(EmployeeWorkingInformation::class);
    }

    /**
     * Get the working information snapshot before this modify request is applied to the real working information instance
     */
    public function beforeSnapshot()
    {
        return $this->hasOne(EmployeeWorkingInformationSnapshot::class, 'modify_request_id', 'before_working_information_snapshot_id');
    }

    /**
     * Get the working information snapshot after this modify request is applied to the real working information instance
     */
    public function afterSnapshot()
    {
        return $this->hasOne(EmployeeWorkingInformationSnapshot::class, 'modify_request_id', 'after_working_information_snapshot_id');
    }
}
