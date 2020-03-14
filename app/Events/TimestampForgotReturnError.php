<?php

namespace App\Events;

use App\EmployeeWorkingDay;
use Illuminate\Foundation\Events\Dispatchable;

class TimestampForgotReturnError
{
    use Dispatchable;

    // The EmployeeWorkingDay instance of this Event
    public $working_day;

    // The EmployeeWorkingInformation in which, the error ocurs.
    public $working_info_id;

    // The timestamp of the go_out
    public $go_out_timestamp;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(EmployeeWorkingDay $working_day, $working_info_id = null, $go_out_timestamp)
    {
        $this->working_day = $working_day;

        $this->working_info_id = $working_info_id;

        $this->go_out_timestamp = $go_out_timestamp;
    }
}
