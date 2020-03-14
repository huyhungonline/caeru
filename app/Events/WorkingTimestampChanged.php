<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use App\EmployeeWorkingDay;

class WorkingTimestampChanged
{
    use Dispatchable;

    // The EmployeeWorkingDay instance of this Event
    public $working_day;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(EmployeeWorkingDay $working_day)
    {
        $this->working_day = $working_day;
    }

}
