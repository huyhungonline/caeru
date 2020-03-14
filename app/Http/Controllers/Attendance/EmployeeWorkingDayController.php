<?php

namespace App\Http\Controllers\Attendance;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Employee;
use App\EmployeeWorkingDay;
use App\EmployeeWorkingInformation;
use App\WorkingTimestamp;
use App\WorkStatus;
use App\Http\Requests\WorkingInformationTransferRequest;
use App\Events\WorkingTimestampChanged;
use App\Http\Controllers\Reusables\UseTheEmployeeWorkingInfoVueComponentTrait;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use Carbon\Carbon;
use NationalHolidays;

class EmployeeWorkingDayController extends Controller
{
    use UseTheEmployeeWorkingInfoVueComponentTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view_attendance_employee_working_day');
        $this->middleware('can:change_attendance_data')->only(['scheduleTransfer']);
    }

    /**
     * Show an EmployeeWorkingDay page
     *
     * @param \Illuminate\Http\Request   $request        the request instance
     * @param int                       $employee_id    the id of the employee
     * @param string                    $date           the working day
     * @param string                    $list           name of the search history that you want to navigate along.
     * @param int                       $page           the current page in that search history order
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request, $employee_id, $date, $list = 'default', $page = 1)
    {
        // Validate the date by format 'Ymd'
        if ($this->validateDateByFormat($date)) {

            $employee_working_day = EmployeeWorkingDay::where('employee_id', $employee_id)->where('date', '=', $date)->first();

            // Make sure there is always a working_day, if that employee_id is valid.
            if (!$employee_working_day) {
                $employee_working_day = $this->createEmployeeWorkingDayOnTheFly($employee_id, $date);
            }

            Javascript::put([
                'working_day_id' => $employee_working_day->id,
                'current_date' => $employee_working_day->date,
                'current_employee' => [
                    'id' => $employee_working_day->employee->id,
                    'name' => $employee_working_day->employee->fullName(),
                    'presentation_id' => $employee_working_day->employee->presentation_id,
                    'schedule_type' => $employee_working_day->employee->scheduleType(),
                ],
                'working_infos' => $this->extractNecessaryData($employee_working_day->employeeWorkingInformations),
                'working_timestamps' => $employee_working_day->workingTimestamps()->orderBy('raw_date_time_value')->get(),
                'timezone' => (Carbon::now()->tz->getOffset(Carbon::now('utc'))/60),
                'can_change_data' => $request->user()->can('change_attendance_data'),
            ]);

            // Send presentation data for employee working information components
            $this->sendPresentationalDataOfEmployeeWorkingInformationComponent($request);

            // Send data for the date picker components. This part is a normal date picker(located at the date navigation at the top, not the schedule-transfer-purposed calendars)
            $this->sendDatePickerData($employee_working_day->employee_id);

            // Send data for the schedule-transfer-purposed calendars. These date pickers are inside of each employee_working_information component
            $schedule_transfer_data = $this->getScheduleTransferData($employee_working_day->employeeWorkingInformations, $employee_working_day->employee_id);
            Javascript::put([
                'schedule_transfer_data' => $schedule_transfer_data,
            ]);

            // Send data for the alert-when-work-time-does-not-match function(the numbers turn red).
            $work_locations_settings = $this->extractDataFromWorkLocationSetting($employee_working_day->employeeWorkingInformations);
            Javascript::put([
                'alert_setting_data' => $work_locations_settings,
            ]);


            return view('attendance.employee.working_day');
        
        } else {
            abort(404, 'Can not find attendance information for this employee on that day!');
        }

    }

    /**
     * Retrieve the EmployeeWorkingInformation list (and their coresponding schedule_transfer_data) of an EmployeeWorkingDay
     *
     * @param EmployeeWorkingDay        $employee_working_day
     * @return array
     */
    public function retrieve(EmployeeWorkingDay $employee_working_day)
    {
        return [
            'working_infos' => $this->extractNecessaryData($employee_working_day->employeeWorkingInformations),
            'schedule_transfer_data' => $this->getScheduleTransferData($employee_working_day->employeeWorkingInformations, $employee_working_day->employee_id),
        ];
    }

    /**
     * Transfer all the EmployeeWorkingInformations for an EmployeeWorkingDay to another.
     *
     * @param WorkingInformationTransferRequest     $request,
     * @return \Illuminate\Http\Response
     */
    public function scheduleTransfer(WorkingInformationTransferRequest $request)
    {

        $data = $request->only([
            'employee_id',
            'from_date',
            'to_date',
        ]);

        $from_working_day = EmployeeWorkingDay::where('employee_id', $data['employee_id'])->where('date', $data['from_date'])->notConcluded()->first();

        $to_working_day = EmployeeWorkingDay::where('employee_id', $data['employee_id'])->where('date', $data['to_date'])->notConcluded()->first();

        // If that day is not existing, create it
        if (!$to_working_day) {
            $to_working_day = $this->createEmployeeWorkingDayOnTheFly($data['employee_id'], $data['to_date']);
        }


        // Transfer all the  working_informations of this 'from_date' to the 'to_date'
        foreach ($from_working_day->employeeWorkingInformations as $working_info) {

            $working_info->planned_work_status_id = WorkStatus::FURIDE;
            $working_info->manually_modified = true;
            $working_info->employeeWorkingDay()->associate($to_working_day);
            $working_info = $this->makeTheWorkingInfoIndependent($working_info, $data['from_date'], $data['to_date']);
            $working_info = $this->removeRealDataFromWorkingInfo($working_info);
            $working_info->save();


            // And create the replaced working information for the 'from_date'
            $replaced_working_info = new EmployeeWorkingInformation();
            $replaced_working_info->planned_work_status_id = WorkStatus::FURIKYUU;
            $replaced_working_info->planned_work_location_id = $working_info->planned_work_location_id;
            $replaced_working_info->manually_modified = true;
            $replaced_working_info->last_modify_person_type = EmployeeWorkingInformation::MODIFY_PERSON_TYPE_MANAGER;
            $replaced_working_info->last_modify_person_id = $request->user()->id;
            $replaced_working_info->employeeWorkingDay()->associate($from_working_day);
            $replaced_working_info->save();

        }

        // Do this just in case. Maybe we don't nee to do this after all. Well, I'm not sure.
        event(new WorkingTimestampChanged($from_working_day));
        event(new WorkingTimestampChanged($to_working_day));

        $request->session()->flash('success', '保存しました');
        return [
            'success' => '保存しました'
        ];
    }


    /**
     * Make the working information independent from the planned schedule, also recalibrate all the editable date time fields.
     *
     * @param  EmployeeWorkingInformation    $working_info
     * @return EmployeeWorkingInformation    $working_info
     */
    protected function makeTheWorkingInfoIndependent($working_info, $from_date, $to_date)
    {
        // Make the schedule part become independent( no longer be effect by the planned schedule). Also adjust them with the new working day
        $working_info->schedule_start_work_time = ($working_info->schedule_start_work_time !== null) ? $working_info->schedule_start_work_time : config('caeru.empty_date');
        $working_info->schedule_end_work_time = ($working_info->schedule_end_work_time !== null) ? $working_info->schedule_end_work_time : config('caeru.empty_date');
        $working_info->schedule_break_time = ($working_info->schedule_break_time !== null) ? $working_info->schedule_break_time : config('caeru.empty');
        $working_info->schedule_night_break_time = ($working_info->schedule_night_break_time !== null) ? $working_info->schedule_night_break_time : config('caeru.empty');
        $working_info->schedule_working_hour = ($working_info->schedule_working_hour !== null) ? $working_info->schedule_working_hour : config('caeru.empty_time');

        // Make these salaries fields independent from the planned schedule
        $working_info->basic_salary    = ($working_info->basic_salary !== null) ? $working_info->basic_salary : config('caeru.empty');
        $working_info->night_salary    = ($working_info->night_salary !== null) ? $working_info->night_salary : config('caeru.empty');
        $working_info->overtime_salary    = ($working_info->overtime_salary !== null) ? $working_info->overtime_salary : config('caeru.empty');
        $working_info->deduction_salary    = ($working_info->deduction_salary !== null) ? $working_info->deduction_salary : config('caeru.empty');
        $working_info->night_deduction_salary    = ($working_info->night_deduction_salary !== null) ? $working_info->night_deduction_salary : config('caeru.empty');
        $working_info->monthly_traffic_expense    = ($working_info->monthly_traffic_expense !== null) ? $working_info->monthly_traffic_expense : config('caeru.empty');
        $working_info->daily_traffic_expense    = ($working_info->daily_traffic_expense !== null) ? $working_info->daily_traffic_expense : config('caeru.empty');


        // Also we have to recalibrate all these editable 'planned_' date time fields, because they are still stick to the old working day
        $carbon_from_date = Carbon::createFromFormat('Y-m-d', $from_date);
        $carbon_to_date = Carbon::createFromFormat('Y-m-d', $to_date);

        $working_info->paid_rest_time_start         = $this->adjustTheDateOfThisAttribute($working_info->paid_rest_time_start, $carbon_from_date, $carbon_to_date);
        $working_info->paid_rest_time_end           = $this->adjustTheDateOfThisAttribute($working_info->paid_rest_time_end, $carbon_from_date, $carbon_to_date);
        $working_info->planned_early_arrive_start   = $this->adjustTheDateOfThisAttribute($working_info->planned_early_arrive_start, $carbon_from_date, $carbon_to_date);
        $working_info->planned_early_arrive_end     = $this->adjustTheDateOfThisAttribute($working_info->planned_early_arrive_end, $carbon_from_date, $carbon_to_date);
        $working_info->planned_overtime_start       = $this->adjustTheDateOfThisAttribute($working_info->planned_overtime_start, $carbon_from_date, $carbon_to_date);
        $working_info->planned_overtime_end         = $this->adjustTheDateOfThisAttribute($working_info->planned_overtime_end, $carbon_from_date, $carbon_to_date);

        return $working_info;
    }

    /**
     * Adjust the date of a given attribute's value (by add or subtract the number of days between the from_date and the end_date)
     *
     * @param string        $attribute_value
     * @param Carbon        $carbon_from_date
     * @param Carbon        $carbon_to_date
     * @return string
     */
    protected function adjustTheDateOfThisAttribute($attribute_value, $carbon_from_date, $carbon_to_date)
    {
        if ($attribute_value !== null) {

            $carbon_instance = Carbon::createFromFormat('Y-m-d H:i:s', $attribute_value);
            $diff = $carbon_to_date->diffInDays($carbon_from_date);

            if ($carbon_from_date->lt($carbon_to_date)) {
                $carbon_instance->addDays($diff);

            // The to_date must be smaller than the from_date in this case, there can't be an equal case.
            } else {
                $carbon_instance->subDays($diff);
            }
            return $carbon_instance->format('Y-m-d H:i:s');

        } else {
            return null;
        }
    }

    /**
     * Remove all the real data that has been assigned for this EmployeeWorkingInformation instance
     *
     * @param EmployeeWorkingInformation    $working_info
     * @return EmployeeWorkingInformation
     */
    protected function removeRealDataFromWorkingInfo($working_info)
    {
        $working_info->timestamped_start_work_time = null;
        $working_info->timestamped_end_work_time = null;
        $working_info->real_work_location_id = null;
        $working_info->real_work_address_id = null;
        $working_info->real_go_out_time = null;

        return $working_info;
    }

    /**
     * Extract the necessary data from a collection of working informations
     *
     * @param Collection      $working_infos
     * @param Collection
     */
    protected function extractNecessaryData($working_infos)
    {
        return $working_infos->map(function($info) {
            return $info->necessaryDataForTheVueComponent();
        });
    }

    /**
     * Prepare the data that support the presentational logic (list of work_location, list of work_status,... etc.) for the javascript side
     *
     * @param Illuminate\Http\Request   $request        the request instance
     * @return void
     */
    protected function sendPresentationalDataOfEmployeeWorkingInformationComponent(Request $request)
    {

        $all_work_locations = $request->user()->company->workLocations()->with('workAddresses')->get();

        // The list of work location with (if any) work address use for the timestamps section
        $places = [];
        foreach ($all_work_locations as $work_location) {
            $places[] = [
                'work_location_id'  => $work_location->id,
                'work_address_id'   => null,
                'name'              => $work_location->name,
            ];
            foreach ($work_location->workAddresses as $work_address) {
                $places[] = [
                    'work_location_id'  => $work_location->id,
                    'work_address_id'   => $work_address->id,
                    'name'              => $work_location->name . ' ' . $work_address->name,
                ];
            }
        }

        // The list of work location with work statuses and rest statuses
        $work_locations = $all_work_locations->map(function($work_location) {
            return [
                'id'            => $work_location->id,
                'name'          => $work_location->name,
                'work_statuses' => $work_location->activatingWorkStatuses()->map(function($status) {
                    return [
                        'id'    => $status->id,
                        'name'  => $status->name,
                    ];
                }),
                'rest_statuses' => $work_location->activatingRestStatuses()->map(function($status) {
                    return [
                        'id'        => $status->id,
                        'name'      => $status->name,
                        'day_based' => $status->unit_type == true,
                    ];
                }),
            ];
        });

        $timestamp_types = [
            WorkingTimestamp::START_WORK => "出勤",
            WorkingTimestamp::END_WORK => "退勤",
            WorkingTimestamp::GO_OUT => "外出",
            WorkingTimestamp::RETURN => "戻り",
        ];

        Javascript::put([
            'work_locations'    => $work_locations,
            'timestamp_types'   => $timestamp_types,
            'timestamp_places'  => $places,
        ]);
    }

    /**
     * Send data to the normal date picker
     *
     * @param integer       $employee_id,
     * @return void
     */
    protected function sendDatePickerData($employee_id)
    {
        $work_location = Employee::find($employee_id)->workLocation;

        if ($work_location) {

            $rest_days = $work_location->getRestDays();

            $national_holidays = NationalHolidays::get();

            Javascript::put([
                'rest_days'             => $rest_days,
                'national_holidays'     => $national_holidays,
                'flip_color_day'        => $work_location->currentSetting()->salary_accounting_day,
            ]);
        }
    }


    /**
     * If on that day, this employee still does not have any working day instance, create one on the fly for him/her.
     * This working_day instance have an empty working_information instance, of course.
     *
     * @param int       $employee_id
     * @param string    $date
     * @return void
     */
    protected function createEmployeeWorkingDayOnTheFly($employee_id, $date)
    {
        // Validate again. Just in case
        $employee = Employee::find($employee_id);

        if ($employee && $this->validateDateByFormat($date)) {

            $new_working_day_instance = new EmployeeWorkingDay();

            $new_working_day_instance->date = $date;
            $new_working_day_instance->employee()->associate($employee_id);
            $new_working_day_instance->save();

            return $new_working_day_instance;

        } else {
            abort(404, 'Can not find attendance information for this employee on that day!');
        }
    }

    /**
     * Validate the date string.
     *
     * @param string    $date
     * @param string    $format
     * @return boolean
     */
    protected function validateDateByFormat($date, $format = 'Y-m-d')
    {
        $result = Carbon::createFromFormat($format, $date);

        return $result && ($result->format($format) == $date);
    }
}
