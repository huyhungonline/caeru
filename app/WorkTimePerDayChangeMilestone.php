<?php

namespace App;

class WorkTimePerDayChangeMilestone extends Model
{

    /**
     * Get the paid holiday information instance of this change milestone instance
     */
    public function paidHolidayInformation()
    {
        return $this->belongsTo(PaidHolidayInformation::class);
    }
}
