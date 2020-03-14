<?php

namespace App;

class ColorStatus extends Model
{

    /**
     * Get the employee working information instance of this color status instance
     */
    public function employeeWorkingInformation()
    {
        return $this->belongsTo(EmployeeWorkingInformation::class);
    }

    /**
     * Get the work address working information instance of this color status instance
     */
    public function workAddressWorkingInformation()
    {
        return $this->belongsTo(WorkAddressWorkingInformation::class);
    }
}
