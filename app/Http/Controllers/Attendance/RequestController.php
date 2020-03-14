<?php

namespace App\Http\Controllers\Attendance;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Employee;
use App\WorkLocation;
use App\EmployeeWorkingDay;
use Carbon\Carbon;
use App\EmployeeWorkingInformation;
use Mockery\Exception;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
class RequestController extends Controller
{


    public function person_detail(Request $request)
    {
        $employee_id = $request->employee_id;

        $today = Carbon::today();

        $item = explode(' ', $today);
        $item = explode('-', $item[0]);
        $year = $item[0];
        $month = $item[1];
        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $list_working_employees_days = EmployeeWorkingDay::where('employee_id', $employee_id)->whereDate('date', '>=', date($year . '-' . $month . '-01'))->whereDate('date', '<=', date($year . '-' . $month . '-' . $number))->get();
        $default = EmployeeWorkingInformation::where('employee_working_day_id', 1)->get();

        $list_day = array();
        $default->id = 0;
        $default->schedule_start_work_time = "";
        $default->schedule_end_work_time = "";
        $default->real_start_work_time = "";
        $default->real_end_work_time = "";
        $default->planned_break_time = "";
        $default->real_break_time = "";

        for ($i = 0; $i < $number; $i++) {

            array_push($list_day, $default);
//          print_r("<pre>");
//          var_dump( $list_day[$i]->schedule_start_work_time);
//          print_r("</pre>");

        }

        for ($i = 0; $i <= $number; $i++) {
            foreach ($list_working_employees_days as $working_employee_day) {

                if ($i > 10) {
                    if ($working_employee_day->date === $year . '-' . $month . '-' . $i) {

                        $employeeWorkingInformations = EmployeeWorkingInformation::where('employee_working_day_id', $working_employee_day->id)->first();
                        $list_day[$i] = $employeeWorkingInformations;
                        if ($employeeWorkingInformations == null) {
                            $employeeWorkingInformations = $default;

                        }


                    }
                } else {
                    if ($working_employee_day->date === $year . '-' . $month . '-' . '0' . $i) {


                        $employeeWorkingInformations = EmployeeWorkingInformation::where('employee_working_day_id', $working_employee_day->id)->first();

                        if ($employeeWorkingInformations == null) {

                            $employeeWorkingInformations = $default;

                        }
                        $list_day[$i] = $employeeWorkingInformations;

                    }
                }


            }


        }
//        foreach ($list_day as $l) {
//
//                       print_r("<pre>");
//                       var_dump( $l->schedule_start_work_time);
//                       print_r("</pre>");
//        }
        Javascript::put([
            'list_day' => $list_day,

        ]);

         return view('request.personal_detail');

    }
    public function requestPage(){

            $employee_working_information = EmployeeWorkingInformation::where('id', 1)->first();
            $employee_working_day = EmployeeWorkingDay::where('id', $employee_working_information->	employee_working_day_id)->first();
            $work_location = WorkLocation::where('id',1)->first();


        Javascript::put([
            'holiday_mode' => $work_location->activatingWorkStatuses(),
             'work_form'  => $work_location->activatingRestStatuses(),
              'work_location_id' => $work_location->id

        ]);
             return view('request.request_page')->with('employee_working_information',$employee_working_information)->with('employee_working_day',$employee_working_day);
    }
    public function save_request_page(Request $request){
        $conditions = $request->input('conditions');
        $in = $conditions[2];
        try{

        }catch (Exception $exception){

        }
        return [
            'success'   => '保存しました',
            'd'        => $in,
        ];
    }
}