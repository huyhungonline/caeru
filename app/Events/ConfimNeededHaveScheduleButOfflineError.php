<?php

namespace App\Events;

use App\EmployeeWorkingDay;
use Illuminate\Foundation\Events\Dispatchable;

class ConfimNeededHaveScheduleButOfflineError
{
    use Dispatchable;

    // The EmployeeWorkingDay instance of this Event
    public $working_day;

    public $working_info_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(EmployeeWorkingDay $working_day, $working_info_id = null)
    {
        $this->working_day = $working_day;

        $this->working_info_id = $working_info_id;
    }
}
