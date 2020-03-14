<?php

namespace App\Http\Controllers\Attendance;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\WorkingTimestampRequest;
use App\EmployeeWorkingDay;
use App\WorkingTimestamp;
use App\Events\WorkingTimestampChanged;

class WorkingTimestampController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:change_attendance_data');
    }    


    /**
     * Store a WorkingTimestamp
     *
     * @param \App\Http\Requests\WorkingTimestampRequest        $request
     * @param \App\EmployeeWorkingDay                           $working_day
     * @return \Illuminate\Http\Response
     */
    public function store(WorkingTimestampRequest $request, EmployeeWorkingDay $working_day)
    {
        $new_timestamp = new WorkingTimestamp($request->only([
            'enable',
            'processed_date_value',
            'processed_time_value',
            'timestamped_type',
            'work_location_id',
            'work_address_id',
        ]));

        $new_timestamp->registerer_type = WorkingTimestamp::MANAGER;
        $new_timestamp->registerer_id = $request->user()->id;

        $new_timestamp->employeeWorkingDay()->associate($working_day);

        $new_timestamp->save();

        $working_day->load('workingTimestamps');

        event(new WorkingTimestampChanged($working_day));

        return [
            'success' => '保存しました',
            'timestamps' => $working_day->workingTimestamps()->orderBy('raw_date_time_value')->get(),
        ];
    }

    /**
     * Toggle the enable of a WorkingTimestamp
     *
     * @param \Illuminate\Http\Request      $request
     * @param \App\WorkingTimestamp         $working_timestamp
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus(Request $request, WorkingTimestamp $working_timestamp)
    {
        $working_timestamp->enable = $request->input('enable');

        $working_timestamp->save();

        event(new WorkingTimestampChanged($working_timestamp->employeeWorkingDay));

        return [
            'success' => '保存しました',
        ];
    }
}
