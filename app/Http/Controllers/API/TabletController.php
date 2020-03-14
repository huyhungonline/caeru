<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\API\APIJupiterLoginRequest;
use App\Http\Requests\API\APIJupiterRegisterWorkLocationRequest;
use App\Http\Requests\API\APIJupiterChooseWorkAddressRequest;
use App\Http\Requests\API\APIJupiterCheckRequest;
use App\Http\Requests\API\APIJupiterCheckCardRequest;
use App\Http\Requests\API\APIJupiterGetEmployeeNameRequest;
use App\Http\Requests\API\APIJupiterRegisterCardRequest;
use App\Http\Requests\API\APIJupiterTimeStampingRequest;
use App\Http\Requests\API\APIJupiterTimeTableRequest;
use App\Http\Requests\API\APIJupiterOfflineDataRequest;
use Illuminate\Support\Facades\Hash;
use DB;
use App\WorkLocation;
use App\Setting;
use App\Employee;
use Carbon\Carbon;
use App\WorkingTimestamp;
use App\Company;
use App\EmployeeWorkingDay;
use Constants;
use Config;
use App\Events\WorkingTimestampChanged;

class TabletController extends Controller
{
    /**
     * Controller of API connection
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function connection(Request $request)
    {
        return response()->json([
            'sucesses'      => 'sucesses'
        ], 200);
    }

    /**
     * Controller of API login
     *
     * @param  App\Http\Requests\API\APIJupiterLoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(APIJupiterLoginRequest $request)
    {
        $random = str_random(20);
        if (DB::table('api_auth_tokens')->where('device_id', $request->input('tablet_id'))->where('company_code', $request->input('company_code'))->exists()) 
            DB::table('api_auth_tokens')->where('device_id', $request->input('tablet_id'))->where('company_code', $request->input('company_code'))->update(['remember_token' => Hash::make($random)]);
        else
            DB::table('api_auth_tokens')->insert(
                ['device_id'        =>  $request->input('tablet_id'),
                 'company_code'     =>  $request->input('company_code'),
                 'remember_token'   =>  Hash::make($random)
             ]);
        return response()->json([
            'company_name' => '会社',
            'authenticated_token' => $random
        ], 200);
    }

    /**
     * Controller of API register work location
     *
     * @param  App\Http\Requests\API\APIJupiterRegisterWorkLocationRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function registerWorkLocation(APIJupiterRegisterWorkLocationRequest $request)
    {
        $work_location = WorkLocation::where('registration_number', $request->work_location_number)->first();
        $work_addresses =  $work_location->workAddresses->map(function ($work_address) {
            return collect($work_address)->only(['id', 'name']);
        });
        return response()->json([
            'use_work_address'      => ($work_location->company->use_address_system) ? true : false,
            'work_addresses'        => $work_addresses,
            'work_location_name'    => $work_location->name,
            'work_location_id'      => $work_location->id,
            'time_zone'             => \DB::table('timezones')->where('id', $work_location->currentSetting()->timezone)->first()->name_id,
            'go_out_button'         => ($work_location->currentSetting()->go_out_button_usage == Setting::NOT_USE_GO_OUT_BUTTON) ? false : true,
            'over_time_button'      => ($work_location->currentSetting()->use_overtime_button) ? false : false,
            'bentou_button'         => false,
        ], 200);
    }

    /**
     * Controller of API choose work address
     *
     * @param  App\Http\Requests\API\APIJupiterChooseWorkAddressRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function chooseWorkAddress(APIJupiterChooseWorkAddressRequest $request)
    {
        return response()->json([
            'sucesses'      => 'sucesses'
        ], 200);
    }

    /**
     * Controller of API check
     *
     * @param  App\Http\Requests\API\APIJupiterCheckRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function check(APIJupiterCheckRequest $request)
    {
        $work_location = WorkLocation::find($request->work_location_id);
        return response()->json([
            'work_location_name'    => $work_location->name,
            'work_location_id'      => $work_location->id,
            'time_zone'             => \DB::table('timezones')->where('id', $work_location->currentSetting()->timezone)->first()->name_id,
            'go_out_button'         => ($work_location->currentSetting()->go_out_button_usage == Setting::NOT_USE_GO_OUT_BUTTON) ? false : true,
            'over_time_button'      => ($work_location->currentSetting()->use_overtime_button) ? false : false,
            'bentou_button'         => false,
            'expected_time'         => Carbon::now()->timestamp,
            'version'               => "7.0",
            'use_work_address'      => ($work_location->company->use_address_system) ? true : false,
            'work_address_id'       => ($work_location->company->use_address_system && isset($request->work_address_id) && !empty($request->work_address_id)) ?  $work_location->workAddresses->where('id', $request->work_address_id)->first()->id : null,
            'work_address_name'       => ($work_location->company->use_address_system && isset($request->work_address_id) && !empty($request->work_address_id)) ?  $work_location->workAddresses->where('id', $request->work_address_id)->first()->name : null,
        ], 200);
    }

    /**
     *  Controller of API Time stamping
     *
     * @param  App\Http\Requests\API\APIJupiterTimeStampingRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function timeStamping(APIJupiterTimeStampingRequest $request)
    {
        $employee = Employee::where('card_number', $request->card_code)->first();
        $employee_working_day = $this->getEmployeeWorkingDayAvailable($request->type, $employee, $request->work_location_id);

        $work_timestamps = new WorkingTimestamp;
        $work_timestamps->enable = true;
        $work_timestamps->timestamped_value = Carbon::now()->timestamp;
        $work_timestamps->timestamped_type = $request->type;
        $work_timestamps->registerer_type = WorkingTimestamp::TABLET;
        $work_timestamps->work_location_id = $request->work_location_id;
        if (isset($request->work_address_id) && !empty($request->work_address_id))
            $work_timestamps->work_address_id = $request->work_address_id;
        $work_timestamps->employee_working_day_id = $employee_working_day->id;
        $work_timestamps->save();

        event(new WorkingTimestampChanged($employee_working_day));

        return response()->json([
            'employee_name'    => $employee->last_name . " " .$employee->first_name,
        ], 200);
    }

    /**
     * Controller of API Time table
     *
     * @param  App\Http\Requests\API\APIJupiterTimeTableRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function timeTable(APIJupiterTimeTableRequest $request)
    {
        $work_location = WorkLocation::find($request->work_location_id);
        $start_day = $this->getStartDay(Carbon::createFromFormat('Y-m-d', $request->date), ($work_location->currentSetting()->start_day_of_week != Config::get('constants.sunday')) ? $work_location->currentSetting()->start_day_of_week : 0);
        return response()->json([
            'data'    =>$this->getTimestampWithWeek($start_day, $request->card_code),
        ],  200);
    }

    /**
     * Controller of API office data
     *
     * @param  App\Http\Requests\API\APIJupiterOfflineDataRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function offlineData(APIJupiterOfflineDataRequest $request)
    {
        $work_location = WorkLocation::find($request->work_location_id);
        foreach ($request->data as $value) {
            $datetime = Carbon::createFromTimestamp($value["timestamp"]);
            $time_setting_company = Carbon::createFromTimestamp($value["timestamp"])
                                    ->hour(Carbon::createFromFormat('H:i', $work_location->company->date_separate_time)->hour)
                                    ->minute(Carbon::createFromFormat('H:i', $work_location->company->date_separate_time)->minute);
            $employee = Employee::where('card_number', $value["card_code"])->first();
            $employee_working_day = $this->getEmployeeWorkingDayAvailable($value["type"], $employee, $request->work_location_id, $datetime, $time_setting_company, $value["timestamp"]);

            $work_timestamps = new WorkingTimestamp;
            $work_timestamps->enable = true;
            $work_timestamps->timestamped_value =$value["timestamp"];
            $work_timestamps->timestamped_type = $value["type"];
            $work_timestamps->registerer_type = WorkingTimestamp::TABLET;
            $work_timestamps->work_location_id = $request->work_location_id;
            if (isset($request->work_address_id) && !empty($request->work_address_id))
                $work_timestamps->work_address_id = $request->work_address_id;
            $work_timestamps->employee_working_day_id = $employee_working_day->id;
            $work_timestamps->save();

            event(new WorkingTimestampChanged($employee_working_day));
        }
        return response()->json([
            'sucesses'      => 'sucesses'
        ], 200);
    }

    /**
     * Controller of API check card
     *
     * @param  App\Http\Requests\API\APIJupiterCheckCardRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function checkCard(APIJupiterCheckCardRequest $request)
    {
        return response()->json([
            'usable'    => (DB::table('employees')->where('card_number', $request->card_code)->exists()) ? false : true ,
        ], 200);
    }

    /**
     * Controller of API get employee name
     *
     * @param  App\Http\Requests\API\APIJupiterGetEmployeeNameRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function getEmployeeName(APIJupiterGetEmployeeNameRequest $request)
    {
        $employee_presentation_id = DB::table('employees')->where('card_registration_number', $request->employee_presentation_id)->first();
        return response()->json([
            'employee_id'       => $employee_presentation_id->id ,
            'employee_name'     => $employee_presentation_id->last_name . " " .$employee_presentation_id->first_name ,
        ], 200);
    }

    /**
     * Controller of API register card for employee
     *
     * @param  App\Http\Requests\API\APIJupiterRegisterCardRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function registerCard(APIJupiterRegisterCardRequest $request)
    {
        $employee = Employee::find($request->employee_id);
        $employee->card_number = $request->card_code;
        $employee->save();
        return response()->json([
            'sucesses'      => 'sucesses'
        ], 200);
    }

    /**
     * Controller of API download file apk
     *
     * @param  App\Http\Requests  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function downloadInstaller(Request $request)
    {
        $pathToFile = public_path("file/apk/jupiter/caeru_tablet_apk.apk");
        return response()->file($pathToFile ,[
            'Content-Type'=>'application/vnd.android.package-archive',
            'Content-Disposition'=> 'attachment; filename="android.apk"',
        ]) ;
    }

    /**
     * The function search and get in employee working day
     *
     * @param  timestamped_type(integer)  $type
     * @param  app/Employee  $employee
     * @param  work_location(integer)  $work_location_id
     * @return app/EmployyeWorkingDay $employee_working_day
     */
    private function getEmployeeWorkingDayAvailable($type, $employee, $work_location_id, $datetime = null, $time_setting_company = null, $timestamp_working = null)
    {
        $work_location = WorkLocation::find($work_location_id);
        if (is_null($datetime)) {
            $datetime = Carbon::now();
            $datetime->second = 0;
        }
        if (is_null($time_setting_company))
            $time_setting_company = Carbon::createFromFormat('H:i',$work_location->company->date_separate_time);

        $employee_working_day = $this->getEmployeeWorkingDayToday($work_location, $datetime, $time_setting_company, $employee);

        if ($type == WorkingTimestamp::END_WORK || $type == WorkingTimestamp::GO_OUT)
            $employee_working_day = $this->sreachDataWorkingTimestampForEndWorkAndGoOut($employee_working_day, $timestamp_working);
        elseif ($type == WorkingTimestamp::RETURN)
            $employee_working_day = $this->sreachDataWorkingTimestampForComeBack($employee_working_day, $timestamp_working);

        return $employee_working_day;
    }

    /**
     * The function get the today's employee working day
     *
     * @param  app/WorkLocation  $work_location
     * @param  Carbon  $datetime
     * @param  app/Setting(Carbon)  $time_setting_company
     * @param  app/Employee  $employee
     * @return app/EmployyeWorkingDay $employee_working_day
     */
    private function getEmployeeWorkingDayToday($work_location, $datetime, $time_setting_company, $employee)
    {
        if ($work_location->company->date_separate_type == Company::APPLY_TO_THE_DAY_BEFORE) {
            if ($datetime->gte($time_setting_company))
                $employee_working_day = EmployeeWorkingDay::where('employee_id', $employee->id)->where('date', $datetime->toDateString())->first();
            else
                $employee_working_day = EmployeeWorkingDay::where('employee_id', $employee->id)->where('date', $datetime->subDay()->toDateString())->first();

        }elseif ($work_location->company->date_separate_type == Company::APPLY_TO_THE_DAY_AFTER) {
            if ($datetime->gte($time_setting_company))
                $employee_working_day = EmployeeWorkingDay::where('employee_id', $employee->id)->where('date', $datetime->addDay()->toDateString())->first();
            else
                $employee_working_day = EmployeeWorkingDay::where('employee_id', $employee->id)->where('date', $datetime->toDateString())->first();

        }
        if (!$employee_working_day)
            $employee_working_day = $this->createEmployeeWorkingDayOnTheFly($employee->id, $datetime->toDateString());

        return $employee_working_day;
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

    /**
     * Search all available in working timestamp for type start work and end work
     * After return the trust employee working day
     *
     * @param App/EmployeeWorkingDay       $employee_working_day
     * @return App/EmployeeWorkingDay
     */
    protected function sreachDataWorkingTimestampForEndWorkAndGoOut($employee_working_day, $timestamp_working = null)
    {
        for ($i=0; $i <= 2; $i++) {
            $set_employee_working_day = EmployeeWorkingDay::where('employee_id', $employee_working_day->employee_id)->where('date', Carbon::createFromFormat('Y-m-d', $employee_working_day->date)->subDays($i)->toDateString())->first();

            if (!empty($set_employee_working_day) && WorkingTimestamp::enable()->startwork()->where('employee_working_day_id', $set_employee_working_day->id)->exists()) {

                if ($i == 0) {
                    if (!is_null($timestamp_working)) {
                        if (WorkingTimestamp::enable()->startwork()->where('employee_working_day_id', $set_employee_working_day->id)->where('timestamped_value', '<=', $timestamp_working)->exists())
                            return $set_employee_working_day;
                    } else
                        return $set_employee_working_day;
                }else
                    if (WorkingTimestamp::enable()->where('employee_working_day_id', $set_employee_working_day->id)
                    ->whereIn('timestamped_type', [WorkingTimestamp::START_WORK, WorkingTimestamp::END_WORK])
                    ->orderBy('timestamped_value', 'desc')
                    ->first()->timestamped_type == WorkingTimestamp::START_WORK)
                        return $set_employee_working_day;
            }

        }
        return $employee_working_day;
    }

    /**
     * Search all available in working timestamp for type go out and go back
     * After return the trust employee working day
     *
     * @param App/EmployeeWorkingDay       $employee_working_day
     * @return App/EmployeeWorkingDay
     */
    protected function sreachDataWorkingTimestampForComeBack($employee_working_day, $timestamp_working = null)
    {
        for ($i=0; $i <= 2; $i++) {
            $set_employee_working_day = EmployeeWorkingDay::where('employee_id', $employee_working_day->employee_id)->where('date', Carbon::createFromFormat('Y-m-d', $employee_working_day->date)->subDays($i)->toDateString())->first();

            if (!empty($set_employee_working_day) && WorkingTimestamp::enable()->startwork()->where('employee_working_day_id', $set_employee_working_day->id)->exists()) {

                if ($i == 0) {
                    if (!is_null($timestamp_working)) {
                        if (WorkingTimestamp::enable()->startwork()->where('employee_working_day_id', $set_employee_working_day->id)->where('timestamped_value', '<=', $timestamp_working)->exists())
                            return $set_employee_working_day;
                    } else
                        return $set_employee_working_day;
                } else
                    if (WorkingTimestamp::enable()->where('employee_working_day_id', $set_employee_working_day->id)
                        ->whereIn('timestamped_type', [WorkingTimestamp::GO_OUT, WorkingTimestamp::RETURN])
                        ->orderBy('timestamped_value', 'desc')
                        ->first()->timestamped_type == WorkingTimestamp::GO_OUT)
                        return $set_employee_working_day;
            }
        }
        return $employee_working_day;
    }

    /**
     * Get all time stamp for the week
     * Format: date, start_time, end_time, arrived_early, arrived_late, go_out_time
     *
     * @param Carbon      $start_date
     * @param integer      $card_code
     * @return array($time_stamp)
     */
    protected function getTimestampWithWeek($start_date, $card_code)
    {
        $week_array = array();
        $employee = Employee::where('card_number', $card_code)->first();
        $end_date = (new Carbon($start_date))->addDay(count(Constants::dayOfTheWeek())-1);

        for($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $day_array = array();
            $day_array['date'] = $date->toDateString();
            $day_array['start_time'] = $day_array['end_time'] = null;
            $day_array['arrived_early'] = $day_array['arrived_late'] = false;
            $day_array['go_out_time'] = 0;

            $employeeWorkingDays = $employee->employeeWorkingDays->where('date', $date->toDateString())->first();
            if (!empty($employeeWorkingDays)) {

                $start_time = WorkingTimestamp::enable()->startwork()->where('employee_working_day_id', $employeeWorkingDays->id)->orderBy('timestamped_value')->first();
                $end_time = WorkingTimestamp::enable()->endwork()->where('employee_working_day_id', $employeeWorkingDays->id)->orderBy('timestamped_value')->first();

                if (!empty($start_time)) {
                    $day_array['start_time'] = $start_time->processed_time_value;
                    $get_all_go_out = WorkingTimestamp::enable()->where('employee_working_day_id', $employeeWorkingDays->id)
                        ->whereIn('timestamped_type', [WorkingTimestamp::GO_OUT, WorkingTimestamp::RETURN])
                        ->where('timestamped_value', '>=', $start_time->full_processed_date_time_value->timestamp)->orderBy('timestamped_value')->get();

                    $end_time = WorkingTimestamp::enable()->endwork()->where('employee_working_day_id', $employeeWorkingDays->id)->where('timestamped_value', '>=', $start_time->full_processed_date_time_value->timestamp)->orderBy('timestamped_value')->first();

                    if (!empty($end_time)) $get_all_go_out = $get_all_go_out->where('timestamped_value', '<=', $end_time->full_processed_date_time_value->timestamp);
                    $day_array['go_out_time'] = $this->getGoOutTime($get_all_go_out, $day_array['go_out_time'], $employee->workLocation->currentSetting()->break_time_round_up);
                }

                if (!empty($end_time))
                    $day_array['end_time'] = $end_time->processed_time_value;
            }

            array_push($week_array,$day_array);
        }

        return $week_array;
    }

    /**
     * Find start week from $date
     *
     * @param Carbon      $date
     * @param integer      $target
     * @return Carbon $date
     */
    protected function getStartDay($date, $target)
    {
        while ($date->dayOfWeek != $target) {
            $date->subDay();
        }
        return $date;
    }

    /**
     * Count all go out time on that day
     *
     * @param App/WorkingTimestamp      $get_all_go_out
     * @param integer      $sum
     * @return integer $sum
     */
    protected function getGoOutTime($get_all_go_out, $sum, $round_up)
    {
        $check = WorkingTimestamp::RETURN;
        $array = array();

        foreach ($get_all_go_out as $get_go_out) {
            ($get_go_out->timestamped_type == $check) ? array_push($array, $get_go_out->id) : $check = $get_go_out->timestamped_type;
        }

        $filtered = $get_all_go_out->whereNotIn('id', $array)->pluck('id');

        for ($i=1; $i < $filtered->count(); $i+=2) {
            $return = WorkingTimestamp::find($filtered[$i]);
            $go_out = WorkingTimestamp::find($filtered[$i-1]);
            $sum += $return->full_processed_date_time_value->diffInMinutes($go_out->full_processed_date_time_value);
        }

        return (ceil($sum/$round_up) * $round_up);
    }
}
