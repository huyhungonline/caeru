<?php


namespace App\Http\Controllers\Attendance;

use App\WorkAddress;
use App\WorkAddressWorkingDay;
use App\WorkAddressWorkingInformation;
use App\Employee;
use App\WorkAddressWorkingEmployee;
use App\EmployeeWorkingInformation;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use NationalHolidays;
use DB;

class WorkAddressWorkingController extends Controller
         {
    public $employee_default_conditions = null;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('choose');


    }

    /*
    * begin load page attendance's information
    */
    public function attendanceinfor(WorkAddressWorkingInformation $working_info)
    {
        return view('attendance.attendance_work_info', [
            'working_info' => $working_info,
        ]);
    }

    /*
    * get employees's information of a working address
    * WorkAddressWorkingDay $workAddressWorkingDay member attendance this day
    */
    public function attendanceWorkingMember(WorkAddressWorkingDay $workAddressWorkingDay)
    {
        session(['current_work_address' => $workAddressWorkingDay->work_address_id]);
        $work_address_working_infor = $workAddressWorkingDay->workAddressWorkingInformations()->get();

        $employee_attribute = array();
        $datas = array();
        $data = array();
        $work_infor_id = array();
        foreach ($work_address_working_infor as $work_infor) {

            array_push($work_infor_id, $work_infor->id);
            $work_address_working_employees = WorkAddressWorkingEmployee::where('work_address_working_information_id', $work_infor->id)->get();
            foreach ($work_address_working_employees as $work_employee) {

                $emp = Employee::find($work_employee->employee_id);

                array_push($employee_attribute, $emp->first_name);
                $infor_id = $work_employee->work_address_working_information_id;
                $item = WorkAddressWorkingInformation::find($infor_id);
                $time_start = $item->planned_start_work_time;
                $time_end = $item->planned_end_work_time;
                array_push($employee_attribute, $time_start);
                array_push($employee_attribute, $time_end);
                array_push($employee_attribute, $emp->presentation_id);
                array_push($employee_attribute, $infor_id);
                array_push($employee_attribute, $workAddressWorkingDay->id);
                if ($work_employee->employee_working_information_id != null) {
                    array_push($employee_attribute, $work_employee->employeeWorkingInformation->real_break_time);
                    array_push($employee_attribute, $work_employee->employeeWorkingInformation->real_night_break_time);
                } else {
                    array_push($employee_attribute, '');
                    array_push($employee_attribute, '');
                }
                array_push($employee_attribute, $work_employee->working_confirm);
                $key = array('name', 'time_start', 'time_end', 'id', 'address_information_id', 'working_day_id', 'break_time', 'night_break_time', 'working_confirm');
                $employee = array_combine($key, $employee_attribute);
                array_push($data, $employee);
                $employee_attribute = array();

            }

            array_push($datas, $data);
            $data = array();


        }
        $today = new Carbon($workAddressWorkingDay->date);

        $item_1 = explode('-', $today);
        $item_2 = explode(' ', $item_1[2]);

        $next_day = $today->copy()->addDay()->format('Y-m-d');
        $next_day_id = WorkAddressWorkingDay::where('date', $next_day)->first()->id;

        $pre_day = $today->copy()->subDay()->format('Y-m-d');
        $pre_day_id = WorkAddressWorkingDay::where('date', $pre_day)->first()->id;
        $weekMap = [
            0 => '日',
            1 => '月',
            2 => '化',
            3 => '水',
            4 => '木',
            5 => '金',
            6 => '土',
        ];
        $dayOfTheWeek = Carbon::parse($workAddressWorkingDay->date)->dayOfWeek;
        $weekday = $weekMap[$dayOfTheWeek];
        Javascript::put([
            'work_address_id' => $workAddressWorkingDay->work_address_id,
            'working_day_id' => $workAddressWorkingDay->id,
            'work_infor_id' => $work_infor_id

        ]);
        Javascript::put([
            'data_employees' => $datas
        ]);

        return view('attendance.work_address.attendance_shift')->with('weekDay', $weekday)->with('next_day_id', $next_day_id)->with('pre_day_id', $pre_day_id)->with('year', $item_1[0])->with('month', $item_1[1])->with('day', $item_2[0]);
    }

    /*
     * begin load page attendancs place
     * Request $request get address_working_id
     */
    public function attendanceplace(Request $request)
    {
        $working_address_id = $request->address_working_id;
        session(['attendance_working_address_id' => $working_address_id]);


        $today = Carbon::today();
        $item = explode(' ', $today);
        $item = explode('-', $item[0]);
        $year = $item[0];
        $month = $item[1];
        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        session(['attendance_working_place_year' => $year]);
        session(['attendance_working_place_month' => $month]);
        $working_days = WorkAddressWorkingDay::whereDate('date', '>=', date($year . '-' . $month . '-01'))->whereDate('date', '<=', date($year . '-' . $month . '-' . $number))->where('work_address_id', '=', $working_address_id)->get();
        $day_infor = array();
        $data_working_days_infors = array();
        foreach ($working_days as $workAddressWorkingDay) {

            $datas = $this->getDetailDayOfAttendancePlace($workAddressWorkingDay);
            array_push($data_working_days_infors, $datas);
            $size = sizeof($datas);
            array_push($day_infor, $size);


        }

        Javascript::put([
            'data_working_days_infors' => $data_working_days_infors,
            'day_infor' => $day_infor
        ]);


        $year = $year;
        $month = $month;
        return view('attendance.work_address.attendance_work_place')->with('month', $month)->with('year', $year)->with('working_address_id', $working_address_id);
    }
     /*
      *
      */
    public function getDetailDayOfAttendancePlace($workAddressWorkingDay)
    {
        $work_address_working_infor = $workAddressWorkingDay->workAddressWorkingInformations()->get();

        $datas = array();
        $data = array();
        $size = 0;

        foreach ($work_address_working_infor as $work_infor) {
            $infor_id = $work_infor->id;
            $item = WorkAddressWorkingInformation::find($infor_id);
            $time_start = $item->planned_start_work_time;
            $time_end = $item->planned_end_work_time;

            $work_address_working_employees = WorkAddressWorkingEmployee::where('work_address_working_information_id', $work_infor->id)->get();
            foreach ($work_address_working_employees as $work_employee) {
                $employee = $this->getEmployeeInforInDayAttendancePalce($work_employee, $time_start, $time_end, $infor_id, $workAddressWorkingDay);
                array_push($data, $employee);


            }

            $date = Carbon::parse($workAddressWorkingDay->date);

            $suborder = $date->format('d/m/Y');
            $weekMap = [
                0 => '日',
                1 => '月',
                2 => '化',
                3 => '水',
                4 => '木',
                5 => '金',
                6 => '土',
            ];
            $dayOfTheWeek = Carbon::parse($workAddressWorkingDay->date)->dayOfWeek;
            $weekday = $weekMap[$dayOfTheWeek];

            $x = array('day', 'time_start', 'time_end', 'data', 'weekday', 'number', 'color', 'infor_table_id');
            $a = array();
            array_push($a, $suborder);
            array_push($a, $time_start);
            array_push($a, $time_end);
            array_push($a, $data);
            array_push($a, $weekday);
            array_push($a, $work_infor->candidate_number);
            array_push($a, sizeof($data));
            array_push($a, $work_infor->id);
            $m = array_combine($x, $a);


            array_push($datas, $m);
            $size = sizeof($datas);

            $data = array();


        }
        return $datas;

    }

    public function getEmployeeInforInDayAttendancePalce($work_employee, $time_start, $time_end, $infor_id, $workAddressWorkingDay)
    {
        $employee_attribute = array();
        $emp = Employee::find($work_employee->employee_id);

        array_push($employee_attribute, $emp->first_name);
        array_push($employee_attribute, $time_start);
        array_push($employee_attribute, $time_end);
        array_push($employee_attribute, $emp->presentation_id);
        array_push($employee_attribute, $infor_id);
        array_push($employee_attribute, $workAddressWorkingDay->id);
        if ($work_employee->employee_working_information_id != null) {
            array_push($employee_attribute, $work_employee->employeeWorkingInformation->real_break_time);
            array_push($employee_attribute, $work_employee->employeeWorkingInformation->real_night_break_time);
        } else {
            array_push($employee_attribute, '');
            array_push($employee_attribute, '');
        }
        array_push($employee_attribute, $work_employee->working_confirm);
        array_push($employee_attribute, $emp->gender);
        array_push($employee_attribute, $work_employee->employee_working_information_id);
        array_push($employee_attribute, $emp->id);
        $key = array('name', 'time_start', 'time_end', 'id', 'address_information_id', 'working_day_id', 'break_time', 'night_break_time', 'working_confirm', 'gender', 'employee_working_information_id', 'id_table');
        $employee = array_combine($key, $employee_attribute);
        return $employee;

    }

    /*
    * get data when change time event
    * @input is date time condition for search
    * @result data of attendance page
    */
    public function getAttendancePlaceByTime()
    {
        $working_address_id = session('attendance_working_address_id');
        $month = Input::get('month');
        $year = Input::get('year');
        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        // get list day
        $working_days = WorkAddressWorkingDay::whereDate('date', '>=', date($year . '-' . $month . '-01'))->whereDate('date', '<=', date($year . '-' . $month . '-' . $number))->where('work_address_id', '=', $working_address_id)->get();
        $day_infor = array();
        $data_working_days_infors = array();

        foreach ($working_days as $workAddressWorkingDay) {
            $datas = $this->getDetailDayOfAttendancePlace($workAddressWorkingDay);
            array_push($data_working_days_infors, $datas);
            $size = sizeof($datas);
            array_push($day_infor, $size);


        }
        Javascript::put([
            'day_infor' => $day_infor
        ]);
        return response()->json($data_working_days_infors);
    }

    /*
     * save change of attendance place
     * @input string is
     */
    public function saveAttendancePlaceInfor()
    {
        $datas = Input::get('data');

        foreach ($datas as $employee) {
            $result = str_replace('[', '', $employee);
            $result = str_replace(']', '', $result);
            $result = str_replace('"', '', $result);
            $item = explode(',', $result);
            $confirm = $item[0];
            $address_information_id = $item[1];
            $employee_working_information_id = $item[2];
            $employee_id = $item[3];
            $working_employee_infor = WorkAddressWorkingEmployee::where('work_address_working_information_id', $address_information_id)->where('employee_working_information_id', $employee_working_information_id)->where('employee_id', $employee_id)->first();
            $working_employee_infor->working_confirm = $confirm;
            $working_employee_infor->update();
        }


        return [
            'success' => '保存しました',

        ];

    }

    /*
    * cancel change working place
    * result is data befor condition apllyed
    */
    public function cancelWorkingPlaceEvent()
    {
        $working_address_id = session('attendance_working_address_id');

        $year = session('attendance_working_place_year');

        $month = session('attendance_working_place_month');


        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $working_days = WorkAddressWorkingDay::whereDate('date', '>=', date($year . '-' . $month . '-01'))->whereDate('date', '<=', date($year . '-' . $month . '-' . $number))->where('work_address_id', '=', $working_address_id)->get();
        $day_infor = array();
        $data_working_days_infors = array();
        foreach ($working_days as $workAddressWorkingDay) {

        $datas = $this->getDetailDayOfAttendancePlace($workAddressWorkingDay);
        array_push($data_working_days_infors, $datas);
        $size = sizeof($datas);
        array_push($day_infor, $size);


        }
        Javascript::put([
            'day_infor' => $day_infor
        ]);
        return response()->json($data_working_days_infors);
    }

    /*
    * insert imployee to address working
    * @return sucess message
    */
    public function insertEmployeeAddressWorking()
    {
        $address_id = session('current_work_address');
        $working_infor_id = Input::get('work_infor_id');
        $employee_id = Input::get('employee_id');
        $employee_name = Input::get('employee_name');
        $working_confirm = Input::get('working_confirm');
        $break_time = Input::get('break_time');
        $nigth_break_time = Input::get('nigth_break_time');
        $working_day_id = Input::get('working_day_id');
        if ($working_confirm === 'true') {
            $working_confirm = 1;
        }
        if ($working_confirm === 'false') {
            $working_confirm = 0;
        }
        $work_address_working_employee = new WorkAddressWorkingEmployee();
        $work_address_working_employee->work_address_working_information_id = $working_infor_id;
        $work_address_working_employee->employee_id = $employee_id;
        $work_address_working_employee->working_confirm = $working_confirm;
        $work_address_working_employee->save();
        if ($break_time !== null && $nigth_break_time !== null) {
            $employee_working_information = new EmployeeWorkingInformation();
            $employee_working_information->real_break_time = $break_time;
            $employee_working_information->real_night_break_time = $nigth_break_time;
            $employee_working_information->employee_working_day_id = $working_day_id;
            $employee_working_information->save();
        }
        return [
            'success' => '保存しました',
        ];


    }

    /*
    * save break time infor of address working
    * @result is save break time infor of address working
    */
    public function saveBreakTimeInfor(Request $request)
    {
        $address_id = session('current_work_address');
        $address_information_id = Input::get('address_information_id') . '';
        $work_location_id = session('current_work_location');
        $break_time = Input::get('break_time') . '';
        $night_break_time = Input::get('night_break_time') . '';
        $employee_id = Input::get('employee_id') . '';
        $working_day_id = Input::get('working_day_id') . '';
        $working_confirm = Input::get('working_confirm');
        if ($working_confirm === 'true') {
            $working_confirm = 1;
        }
        if ($working_confirm === 'false') {
            $working_confirm = 0;
        }
        $work_address_employee = WorkAddressWorkingEmployee::where('work_address_working_information_id', $address_information_id)->where('employee_id', $employee_id)->first();

        $work_address_employee->working_confirm = $working_confirm;

        $work_address_employee->update();
        $employee_working_information = $work_address_employee->employeeWorkingInformation;
        $employee_working_information->planned_break_time = $break_time;
        $employee_working_information->planned_night_break_time = $night_break_time;
        $employee_working_information->update();

        return [
            'success' => '保存しました',
            'id' => $working_confirm,
        ];

    }

    /*
    * get working address infor
    */
    public function getWorkingAddressInfor()
    {
        $working_infor_id = Input::get('work_infor_id');
        $working_infor = WorkAddressWorkingInformation::where('id', $working_infor_id)->first();
        return response()->json($working_infor);
    }

    /*
    * save address working infor
    */
    public function saveAddressWorkingInfor(Request $request)
    {
        $working_infor_id = Input::get('work_infor_id');
        $candidate_number = Input::get('candidate_number');
        $planned_start_work_time = Input::get('planned_start_work_time');
        $planned_end_work_time = Input::get('planned_end_work_time');
        $note = Input::get('note');
        $working_infor = WorkAddressWorkingInformation::where('id', $working_infor_id)->first();
        $working_infor->candidate_number = $candidate_number;
        $working_infor->planned_end_work_time = $planned_end_work_time;
        $working_infor->planned_start_work_time = $planned_start_work_time;
        $working_infor->note = $note;
        $working_infor->update();
        return [
            'success' => '保存しました',
            'id' => $planned_end_work_time,
        ];
    }

    /*
    * begin load page work infor
    * @return data for attendance_work_infor
    */
    public function attendance_work_infor()
    {
        $working_address_id = 1;
        $today = Carbon::today();
        $item = explode(' ', $today);
        $item = explode('-', $item[0]);
        $year = $item[0];
        $month = $item[1];
        $day = $item[2];
        // init day begin today to today + 5
        $day0 = Carbon::today();
        $day1 = $day0->copy()->addDay();
        $day2 = $day1->copy()->addDay();
        $day3 = $day2->copy()->addDay();
        $day4 = $day3->copy()->addDay();
        $day5 = $day4->copy()->addDay();
        $day0 = $day0->format('Y-m-d');
        $day1 = $day1->format('Y-m-d');
        $day2 = $day2->format('Y-m-d');
        $day3 = $day3->format('Y-m-d');
        $day4 = $day4->format('Y-m-d');
        $day5 = $day5->format('Y-m-d');

        $weekMap = [
            0 => '日',
            1 => '月',
            2 => '化',
            3 => '水',
            4 => '木',
            5 => '金',
            6 => '土',
        ];

        $dayOfTheWeek = Carbon::parse($day0)->dayOfWeek;
        $weekday = $weekMap[$dayOfTheWeek];
        $item = Carbon::today();
        $day_infor = array();
        // day_infor is list day begin today to today + 5, this data will pass to view by day_infor name.
        for ($i = 0; $i <= 5; $i++) {
            $x = array();
            $item_day = explode(' ', $item);
            $dayOfTheWeek = Carbon::parse($item_day[0])->dayOfWeek;
            $weekday = $weekMap[$dayOfTheWeek];
            $item_day = explode('-', $item_day[0]);
            $key = array('month', 'day', 'weekDay');
            array_push($x, $item_day[1]);
            array_push($x, $item_day[2]);
            array_push($x, $weekday);
            $d = array_combine($key, $x);
            array_push($day_infor, $d);
            $item = $item->copy()->addDay();
        }

        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $end_day_search = $day + 5;
        if ($day + 5 > $number) {
            $end_day_search = $number - $day;
            $month = $month + 1;
            if ($month > 12) {
                $month = 1;
                $year = $year + 1;
            }
        }

        $list_working_address = WorkAddress::all();
        $list_address_infor = array();
        // lặp theo từng địa chỉ
        foreach ($list_working_address as $working_address) {


            $number_infor_each_time = $this->getDateInforAttendaceInfor($year, $month, $day, $end_day_search, $working_address, $weekday, $day0, $day1, $day2, $day3, $day4, $day5);
            array_push($list_address_infor, $number_infor_each_time);

        }

        // create calender by Nguyen Huy Hung
        $calender = array();
        $calender_array = array();
        $item = Carbon::today();
        $x = array();
        $item_day = explode(' ', $item);
        $weekday = $weekMap[$dayOfTheWeek];
        $item_day = explode('-', $item_day[0]);
        $dayOfTheWeek = Carbon::parse($item_day[0] . '-' . $item_day[1] . '-' . '01')->dayOfWeek;
        $month = $item_day[1];
        $year = $item_day[0];
        $number_day_of_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        if ($month - 1 == 0) {
            $m = 12;
        }

        $number_day_of_pre_month = cal_days_in_month(CAL_GREGORIAN, $m, $year);
        $numberWeek = $number_day_of_month / 7;

        for ($i = $dayOfTheWeek - 1; $i >= 0; $i--) {
            array_push($calender_array, $number_day_of_pre_month - $i);
        }
        $max = 7 - $dayOfTheWeek;
        $begin = 0;
        for ($i = 1; $i <= $max; $i++) {
            array_push($calender_array, $i);
            $begin = $i;
        }
        array_push($calender, $calender_array);
        $begin = $begin + 1;

        $b = 0;

        for ($i = 0; $i <= $numberWeek; $i++) {
            $calender_array = array();
            $count = 0;
            for ($j = $begin; $j <= $number_day_of_month; $j++) {
                //var_dump($j);
                array_push($calender_array, $j);
                if ($count == 6) {

                    array_push($calender, $calender_array);
                    break;
                }
                $count = $count + 1;

            }
            $b = 7 - sizeof($calender_array);
            if ($begin + 6 < $number_day_of_month || $begin + 6 < $number_day_of_month || $begin + 5 < $number_day_of_month || $begin + 4 < $number_day_of_month || $begin + 3 < $number_day_of_month) {
                $begin = $begin + 7;
            }

        }

        for ($k = 1; $k <= $b; $k++) {
            array_push($calender_array, $k);
        }

        // create calender by
        Javascript::put([
            'data_working_days_infors' => $list_address_infor,
            'day_infor' => $day_infor,
            'list_address' => $list_working_address,
            'calender' => $calender
        ]);
        session(['attendance_working_address_id' => $working_address_id]);
        $employee_id = auth()->user()->id;
        $this->sendDatePickerData($employee_id);
        return view('attendance.work_address.attendance_work_info')->with('year', $year)->with('month', $month);
    }
    /*
    *
    */
    public function getDateInforAttendaceInfor($year, $month, $day, $end_day_search, $working_address, $weekday, $day0, $day1, $day2, $day3, $day4, $day5)
    {

        // trong địa chỉ lấy ra các ngày có trong data
        $working_days = WorkAddressWorkingDay::whereDate('date', '>=', date($year . '-' . $month . '-' . $day))->whereDate('date', '<=', date($year . '-' . $month . '-' . $end_day_search))->where('work_address_id', '=', $working_address->id)->limit(6)->get();


        // kiểm tra xem có tối đa bao nhiêu ca làm trong các ngày đó, sau đó các ngày còn lại (hàng trong bảng) sẽ bi phụ thuộc vào ngày có nhiều ca nhất
        $size_of_infor = 0;
        foreach ($working_days as $workAddressWorkingDay) {
            $work_address_working_infor = $workAddressWorkingDay->workAddressWorkingInformations()->get();
        if ($size_of_infor < sizeof($work_address_working_infor)) {

            $size_of_infor = sizeof($work_address_working_infor);

        }
        }

        $number_infor_each_time = array();
        // trong trường hợp 1 ngày ko có ca làm việc nào thì khởi tạo cho in ra 1 hàng rỗng
        if ($size_of_infor == 0) {
            $size_of_infor = $size_of_infor + 1;
        }
        // vòng lặp số lượng hàng ngang đối với 1 working address
        for ($i = 0; $i < $size_of_infor; $i++) {
        // mảng data_working_days_infors chứa thông tin 6 ngày trên 1 hàng ngang
            $data_working_days_infors = array();
        // mới đầu khởi tạo thì thiết lập các ngày trong mảng là null
        for ($f = 0; $f <= 5; $f++) {
            $data_working_days_infors[$f] = null;
        }
        // lấy đủ 6 ngày trong 1 tuần, nếu ngày nào ko có trong db thì chèn dữ liệu giả vào
        for ($j = 0; $j <= 5; $j++) {

        // lấy số ngày có trong db ứng với 1 working address và ứng với 1 hàng ngang, tức là mỗi hàng ngang sẽ có đủ 6 ngày
        foreach ($working_days as $workAddressWorkingDay) {

            $work_address_working_infor = $workAddressWorkingDay->workAddressWorkingInformations()->get();

            $datas = array();
            $data = array();
            $size = 0;
            $work_infor_id = array();
            $time_start = '';
            $time_end = '';
            // vì số lượng hàng ngang lấy theo 1 address phụ thuộc vào ngày có nhiều ca làm việc nhất, nên sẽ có những ngày có những ca thiws $i ko tồn tại
            if (isset($work_address_working_infor[$i])) {
                $infor_id = $work_address_working_infor[$i]->id;
                $item = WorkAddressWorkingInformation::find($infor_id);
                $time_start = $item->planned_start_work_time;
                $time_end = $item->planned_end_work_time;
                array_push($work_infor_id, $work_address_working_infor[$i]->id);
                $work_address_working_employees = WorkAddressWorkingEmployee::where('work_address_working_information_id', $work_address_working_infor[$i]->id)->get();
                // trong mỗi ca của 1 ngày làm việc có nhiều nhân viên
                foreach ($work_address_working_employees as $work_employee) {
                    $employee = $this->getEmployeeAtribute($work_employee,$time_start,$time_end,$infor_id,$workAddressWorkingDay);
                    array_push($data, $employee);
                }

                $data_working_by_each_time = $this->emtyDataByEachTime($time_start,$time_end,$data,$weekday, $work_address_working_infor[$i]);



            } else {

                $data_working_by_each_time = $this->emtyDataByEachTime($time_start,$time_end,$data,$weekday, $work_address_working_infor[$i]);


            }
            //  mảng data_working_days_infors chưa thông tin của 6 ngày, nếu date trùng với ngày nào thì chèn vào mảng data_working_days_infors đúng với thứ tự đó
            if ($workAddressWorkingDay->date === $day0) {

                $data_working_days_infors[0] = $data_working_by_each_time;

            }
            if ($workAddressWorkingDay->date === $day1) {

                $data_working_days_infors[1] = $data_working_by_each_time;

            }
            if ($workAddressWorkingDay->date === $day2) {

                $data_working_days_infors[2] = $data_working_by_each_time;

            }
            if ($workAddressWorkingDay->date === $day3) {

                $data_working_days_infors[3] = $data_working_by_each_time;

            }
            if ($workAddressWorkingDay->date === $day4) {

                $data_working_days_infors[4] = $data_working_by_each_time;

            }
            if ($workAddressWorkingDay->date === $day5) {

                $data_working_days_infors[5] = $data_working_by_each_time;

            }


        }

        }

        // trong trường hợp chưa có ngày nào trong DB ứng với 1 hàng ngang thì khởi tạo 6 ngày rỗng bỏ vào trong mảng data_working_days_infors
        if (sizeof($working_days) == 0) {
            $data_working_days_infors = array();
            for ($k = 0; $k <= 5; $k++) {

                $x = array('day', 'time_start', 'time_end', 'data', 'weekday', 'number', 'color');
                $a = array();
                array_push($a, "");
                array_push($a, '');
                array_push($a, '');
                array_push($a, "");
                array_push($a, "");
                array_push($a, '');
                array_push($a, "");
                $data_working_by_each_time = array_combine($x, $a);
                $data = array();

                array_push($data_working_days_infors, $data_working_by_each_time);
            }
        }
        // thêm vào mảng 6 ngày nếu phần tử là null thí khởi tạo infor của ngày đó
        for ($l = 0; $l <= 5; $l++) {
            if ($data_working_days_infors[$l] === null) {

                $x = array('day', 'time_start', 'time_end', 'data', 'weekday', 'number', 'color');
                $a = array();
                array_push($a, "");
                array_push($a, '');
                array_push($a, '');
                array_push($a, "");
                array_push($a, "");
                array_push($a, '');
                array_push($a, "");
                $data_working_by_each_time = array_combine($x, $a);
                $data = array();

                $data_working_days_infors[$l] = $data_working_by_each_time;
            }
        }
        // lấy các thông tin của 1 hàng ngang tương ứng với 1 address
        $x = array('data_working_days_infors', 'size_of_infor', 'address_name', 'pr_id', 'id');
        $a = array();
        array_push($a, $data_working_days_infors);
        array_push($a, $size_of_infor);
        array_push($a, $working_address->name);
        array_push($a, $working_address->presentation_id);
        array_push($a, $working_address->id);
        $item = array_combine($x, $a);
        array_push($number_infor_each_time, $item);

        }
        return $number_infor_each_time;
    }
    /*
    * get all atribute of employee
    * @param work_employee is employee object
    * @param time_start time start
    * @param time end is end time
    * @param infor id : information id
    * @param workAddressWorkingDay
    * result employee and infor
    */
    public function getEmployeeAtribute($work_employee,$time_start,$time_end,$infor_id,$workAddressWorkingDay){
        $employee_attribute = array();
        $emp = Employee::find($work_employee->employee_id);

        array_push($employee_attribute, $emp->first_name);

        array_push($employee_attribute, $time_start);
        array_push($employee_attribute, $time_end);
        array_push($employee_attribute, $emp->presentation_id);
        array_push($employee_attribute, $infor_id);
        array_push($employee_attribute, $workAddressWorkingDay->id);
        if ($work_employee->employee_working_information_id != null) {
            array_push($employee_attribute, $work_employee->employeeWorkingInformation->real_break_time);
            array_push($employee_attribute, $work_employee->employeeWorkingInformation->real_night_break_time);
        } else {
            array_push($employee_attribute, '');
            array_push($employee_attribute, '');
        }
        array_push($employee_attribute, $work_employee->working_confirm);
        array_push($employee_attribute, $emp->gender);
        array_push($employee_attribute, $work_employee->employee_working_information_id);
        array_push($employee_attribute, $emp->id);
        $key = array('name', 'time_start', 'time_end', 'id', 'address_information_id', 'working_day_id', 'break_time', 'night_break_time', 'working_confirm', 'gender', 'employee_working_information_id', 'id_table');
        $employee = array_combine($key, $employee_attribute);


        return $employee;
    }

    /*
    * send data date for date picker of page address infor
    * @param employee_id id of employee
    * @result
    */
    protected function sendDatePickerData($employee_id)
    {
        $work_location = Employee::find($employee_id)->workLocation;

        if ($work_location) {

            $rest_days = $work_location->getRestDays();

            $national_holidays = NationalHolidays::get();

            Javascript::put([
                'rest_days' => $rest_days,
                'national_holidays' => $national_holidays,
                'flip_color_day' => $work_location->currentSetting()->salary_accounting_day,
            ]);
        }
    }

    /*
    *get work infor by api
    *@result list work
    *
    */
    public function attendance_work_infor_API(Request $request)
    {
         $conditions = $request->input('conditions');
         $list_address_infor = $this->getAddressInfor($conditions);

        return [
            'success' => '保存しました',
            'data' => $list_address_infor,
        ];
    }

    /*
    *  get address infor when search by condition
    * @param condition : all condition to search
    */
    public function getAddressInfor($conditions)
    {

        $day0 = $conditions[2] . '-' . $conditions[1] . '-' . $conditions[0] . ' ' . '00:00:00.000000';
        if ($conditions[2] != null && $conditions[1] != null && $conditions[0] != null) {
        $day = $conditions[0];
        $month = $conditions[1];
        $year = $conditions[2];

        }
        // init list day begin $day0 to $day0 + 5
        $day0 = Carbon::parse($day0);
        $item = $day0;
        $day1 = $day0->copy()->addDay();
        $day2 = $day1->copy()->addDay();
        $day3 = $day2->copy()->addDay();
        $day4 = $day3->copy()->addDay();
        $day5 = $day4->copy()->addDay();
        $day0 = $day0->format('Y-m-d');

        $day1 = $day1->format('Y-m-d');

        $day2 = $day2->format('Y-m-d');
        $day3 = $day3->format('Y-m-d');
        $day4 = $day4->format('Y-m-d');
        $day5 = $day5->format('Y-m-d');

        $weekMap = [
        0 => '日',
        1 => '月',
        2 => '化',
        3 => '水',
        4 => '木',
        5 => '金',
        6 => '土',
        ];

        $dayOfTheWeek = Carbon::parse($day0)->dayOfWeek;
        $weekday = $weekMap[$dayOfTheWeek];
        // create list day $day0  to &day0 + 5
        $day_infor = array();
        for ($i = 0; $i <= 5; $i++) {
        $x = array();
        $item_day = explode(' ', $item);
        $dayOfTheWeek = Carbon::parse($item_day[0])->dayOfWeek;

        $weekday = $weekMap[$dayOfTheWeek];
        $item_day = explode('-', $item_day[0]);
        $key = array('month', 'day', 'weekDay');
        array_push($x, $item_day[1]);
        array_push($x, $item_day[2]);
        array_push($x, $weekday);
        $d = array_combine($key, $x);
        array_push($day_infor, $d);
        $item = $item->copy()->addDay();
        }

        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        // search by work_addreee
        if ($conditions[3] != 0) {
        $list_working_address = WorkAddress::where('id', '=', $conditions[3])->get();
        } else {
        $list_working_address = WorkAddress::all();
        }

        $list_address_infor = array();
        $day_end = $day + 7;
        $month_end = $month;
        $year_end = $year;
        if ($day_end > $number) {
        $day_end = $day_end - $number;
        $month_end = $month + 1;
        if ($month_end > 12) {
            $month_end = 1;
            $year_end = $year + 1;
        }
        }
        // lặp theo từng địa chỉ

        foreach ($list_working_address as $working_address) {

        // trong địa chỉ lấy ra các ngày có trong data
        $working_days = WorkAddressWorkingDay::whereDate('date', '>=', date($year . '-' . $month . '-' . $day))->whereDate('date', '<=', date($year_end . '-' . $month_end . '-' . $day_end))->where('work_address_id', '=', $working_address->id)->limit(6)->get();

        // kiểm tra xem có tối đa bao nhiêu ca làm trong các ngày đó, sau đó các ngày còn lại (hàng trong bảng) sẽ bi phụ thuộc vào ngày có nhiều ca nhất
        $size_of_infor = 0;
        foreach ($working_days as $workAddressWorkingDay) {
            $work_address_working_infor = $workAddressWorkingDay->workAddressWorkingInformations()->get();
            if ($size_of_infor < sizeof($work_address_working_infor)) {

                $size_of_infor = sizeof($work_address_working_infor);

            }
        }

        $number_infor_each_time = array();
        // trong trường hợp 1 ngày ko có ca làm việc nào thì khởi tạo cho in ra 1 hàng rỗng
        if ($size_of_infor == 0) {
            $size_of_infor = $size_of_infor + 1;
        }
        // vòng lặp số lượng hàng ngang đối với 1 working address
        for ($i = 0; $i < $size_of_infor; $i++) {
            // mảng data_working_days_infors chứa thông tin 6 ngày trên 1 hàng ngang
            $data_working_days_infors = array();
            // mới đầu khởi tạo thì thiết lập các ngày trong mảng là null
            for ($f = 0; $f <= 5; $f++) {
                $data_working_days_infors[$f] = null;
            }
            // lấy đủ 6 ngày trong 1 tuần, nếu ngày nào ko có trong db thì chèn dữ liệu giả vào
            for ($j = 0; $j <= 5; $j++) {

                // lấy số ngày có trong db ứng với 1 working address và ứng với 1 hàng ngang, tức là mỗi hàng ngang sẽ có đủ 6 ngày
                foreach ($working_days as $workAddressWorkingDay) {

                    $work_address_working_infor = $workAddressWorkingDay->workAddressWorkingInformations()->get();
                    $employee_attribute = array();
                    $datas = array();
                    $data = array();
                    $size = 0;
                    $work_infor_id = array();

                    $time_start = '';
                    $time_end = '';
                    // vì số lượng hàng ngang lấy theo 1 address phụ thuộc vào ngày có nhiều ca làm việc nhất, nên sẽ có những ngày có những ca thiws $i ko tồn tại
                    if (isset($work_address_working_infor[$i])) {
                        $infor_id = $work_address_working_infor[$i]->id;
                        $item = WorkAddressWorkingInformation::find($infor_id);
                        $time_start = $item->planned_start_work_time;
                        $time_end = $item->planned_end_work_time;
                        array_push($work_infor_id, $work_address_working_infor[$i]->id);
                        if ($conditions[4] != 0) {
                            $work_address_working_employees = WorkAddressWorkingEmployee::where('work_address_working_information_id', $work_address_working_infor[$i]->id)->where('employee_id', $conditions[4])->get();
                        } else {
                            $work_address_working_employees = WorkAddressWorkingEmployee::where('work_address_working_information_id', $work_address_working_infor[$i]->id)->get();

                        }
                        // trong mỗi ca của 1 ngày làm việc có nhiều nhân viên
                        foreach ($work_address_working_employees as $work_employee) {

                            $employee = $this->getEmployeeAtribute($work_employee,$time_start,$time_end,$infor_id,$workAddressWorkingDay);
                            array_push($data, $employee);


                        }



                        $data_working_by_each_time = $this->emtyDataByEachTime($time_start,$time_end,$data,$weekday, $work_address_working_infor[$i]);



                    } else {

                        $data_working_by_each_time = $this->emtyDataByEachTime($time_start,$time_end,$data,$weekday, $work_address_working_infor[$i]);
                    }

                    //  mảng data_working_days_infors chưa thông tin của 6 ngày, nếu date trùng với ngày nào thì chèn vào mảng data_working_days_infors đúng với thứ tự đó
                    if ($workAddressWorkingDay->date === $day0) {

                        $data_working_days_infors[0] = $data_working_by_each_time;

                    }
                    if ($workAddressWorkingDay->date === $day1) {

                        $data_working_days_infors[1] = $data_working_by_each_time;

                    }
                    if ($workAddressWorkingDay->date === $day2) {

                        $data_working_days_infors[2] = $data_working_by_each_time;

                    }
                    if ($workAddressWorkingDay->date === $day3) {


                        $data_working_days_infors[3] = $data_working_by_each_time;

                    }
                    if ($workAddressWorkingDay->date === $day4) {

                        $data_working_days_infors[4] = $data_working_by_each_time;

                    }
                    if ($workAddressWorkingDay->date === $day5) {

                        $data_working_days_infors[5] = $data_working_by_each_time;

                    }


                }

            }

            // trong trường hợp chưa có ngày nào trong DB ứng với 1 hàng ngang thì khởi tạo 6 ngày rỗng bỏ vào trong mảng data_working_days_infors
            if (sizeof($working_days) == 0) {
                $data_working_days_infors = array();
                for ($k = 0; $k <= 5; $k++) {

                    $x = array('day', 'time_start', 'time_end', 'data', 'weekday', 'number', 'color');
                    $a = array();
                    array_push($a, "");
                    array_push($a, '');
                    array_push($a, '');
                    array_push($a, "");
                    array_push($a, "");
                    array_push($a, '');
                    array_push($a, "");
                    $data_working_by_each_time = array_combine($x, $a);
                    $data = array();

                    array_push($data_working_days_infors, $data_working_by_each_time);
                }
            }
            // thêm vào mảng 6 ngày nếu phần tử là null thí khởi tạo infor của ngày đó
            for ($l = 0; $l <= 5; $l++) {
                if ($data_working_days_infors[$l] === null) {

                    $x = array('day', 'time_start', 'time_end', 'data', 'weekday', 'number', 'color');
                    $a = array();
                    array_push($a, "");
                    array_push($a, '');
                    array_push($a, '');
                    array_push($a, "");
                    array_push($a, "");
                    array_push($a, '');
                    array_push($a, "");
                    $data_working_by_each_time = array_combine($x, $a);
                    $data = array();

                    $data_working_days_infors[$l] = $data_working_by_each_time;
                }
            }
            // lấy các thông tin của 1 hàng ngang tương ứng với 1 address
            $x = array('data_working_days_infors', 'size_of_infor', 'address_name', 'pr_id', 'id');
            $a = array();
            array_push($a, $data_working_days_infors);
            array_push($a, $size_of_infor);
            array_push($a, $working_address->name);
            array_push($a, $working_address->presentation_id);
            array_push($a, $working_address->id);
            $item = array_combine($x, $a);

            array_push($number_infor_each_time, $item);

        }


        array_push($list_address_infor, $number_infor_each_time);

        }

        // calender
        $calender = array();
        $calender_array = array();
        $item = Carbon::today();
        $x = array();
        $item_day = explode(' ', $item);
        $weekday = $weekMap[$dayOfTheWeek];
        $item_day = explode('-', $item_day[0]);
        $dayOfTheWeek = Carbon::parse($item_day[0] . '-' . $item_day[1] . '-' . '01')->dayOfWeek;
        $month = $item_day[1];
        $year = $item_day[0];
        $number_day_of_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $number_day_of_pre_month = cal_days_in_month(CAL_GREGORIAN, $month , $year);
        $numberWeek = $number_day_of_month / 7;

        for ($i = $dayOfTheWeek - 1; $i >= 0; $i--) {
        array_push($calender_array, $number_day_of_pre_month - $i);
        }
        $max = 7 - $dayOfTheWeek;
        $begin = 0;
        for ($i = 1; $i <= $max; $i++) {
        array_push($calender_array, $i);
        $begin = $i;
        }
        array_push($calender, $calender_array);
        $begin = $begin + 1;

        $b = 0;

        for ($i = 0; $i <= $numberWeek; $i++) {
        $calender_array = array();
        $count = 0;
        for ($j = $begin; $j <= $number_day_of_month; $j++) {
            //var_dump($j);
            array_push($calender_array, $j);
            if ($count == 6) {

                array_push($calender, $calender_array);
                break;
            }
            $count = $count + 1;

        }
        $b = 7 - sizeof($calender_array);
        if ($begin + 6 < $number_day_of_month || $begin + 6 < $number_day_of_month || $begin + 5 < $number_day_of_month || $begin + 4 < $number_day_of_month || $begin + 3 < $number_day_of_month) {
            $begin = $begin + 7;
        }

        }

        for ($k = 1; $k <= $b; $k++) {
        array_push($calender_array, $k);
        }

        Javascript::put([
        'data_working_days_infors' => $list_address_infor,
        'day_infor' => $day_infor,
        'list_address' => $list_working_address,
        'calender' => $calender
        ]);
        return $list_address_infor;
    }
    /*
     * create emty data by for time
     * param time_start
     * param time_end
     * param data is employee and atribute
     * work_address_working_infor as
     * return emty object
     */
    public function emtyDataByEachTime($time_start,$time_end,$data,$weekday,WorkAddressWorkingInformation $work_address_working_infor){
        $x = array('time_start', 'time_end', 'data', 'weekday', 'number', 'color');
        $a = array();
        $candidate_number = '';
        if(!isset($work_address_working_infor)){
            $time_start = '';
            $time_end = '';
            $candidate_number = '';

        }else{
            $candidate_number = $work_address_working_infor->candidate_number;
        }
        array_push($a, $time_start);
        array_push($a, $time_end);
        array_push($a, $data);
        array_push($a, $weekday);
        array_push($a,$candidate_number );
        array_push($a, sizeof($data));
        $data_working_by_each_time = array_combine($x, $a);
        return $data_working_by_each_time;
    }
    /*
    * get day_infor for date picker
    */
    public function getDayInfor()
    {
        $month = Input::get('month');
        $year = Input::get('year');
        $day = Input::get('day');
        $day0 = $year . '-' . $month . '-' . $day . ' ' . '00:00:00.000000';

        $day0 = Carbon::parse($day0);
        $item = $day0;
        $day_infor = array();
        $weekMap = [
        0 => '日',
        1 => '月',
        2 => '化',
        3 => '水',
        4 => '木',
        5 => '金',
        6 => '土',
        ];
        for ($i = 0; $i <= 5; $i++) {
            $x = array();
            $item_day = explode(' ', $item);
            $dayOfTheWeek = Carbon::parse($item_day[0])->dayOfWeek;
            $weekday = $weekMap[$dayOfTheWeek];

            $item_day = explode('-', $item_day[0]);
            $key = array('month', 'day', 'weekDay');
            array_push($x, $item_day[1]);
            array_push($x, $item_day[2]);
            array_push($x, $weekday);
            $d = array_combine($key, $x);
            array_push($day_infor, $d);
            $item = $item->copy()->addDay();
        }
        return response()->json($day_infor);
    }
    /*
    * get calender data
    * @input month
    * @input year
    */
    public function getCalender()
    {
        $month = Input::get('month');
        $year = Input::get('year');

        $weekMap = [
            0 => '日',
            1 => '月',
            2 => '化',
            3 => '水',
            4 => '木',
            5 => '金',
            6 => '土',
        ];
        $calender = array();
        $calender_array = array();

        $dayOfTheWeek = Carbon::parse($year . '-' . $month . '-' . '01')->dayOfWeek;
        $number_day_of_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $number_day_of_pre_month = cal_days_in_month(CAL_GREGORIAN, $month - 1, $year);
        $numberWeek = $number_day_of_month / 7;
        // var_dump($numberWeek);
        for ($i = $dayOfTheWeek - 1; $i >= 0; $i--) {
            array_push($calender_array, $number_day_of_pre_month - $i);
        }
        $max = 7 - $dayOfTheWeek;
        $begin = 0;
        for ($i = 1; $i <= $max; $i++) {
            array_push($calender_array, $i);
            $begin = $i;
        }
        array_push($calender, $calender_array);
        $begin = $begin + 1;

        $b = 0;
        for ($i = 0; $i <= $numberWeek; $i++) {
            $calender_array = array();
            $count = 0;
            for ($j = $begin; $j <= $number_day_of_month; $j++) {

                array_push($calender_array, $j);
                if ($count == 6) {
                    array_push($calender, $calender_array);
                    break;
                }
                $count = $count + 1;

            }
            $b = 7 - sizeof($calender_array);
            if ($begin + 6 < $number_day_of_month || $begin + 5 < $number_day_of_month || $begin + 4 < $number_day_of_month || $begin + 3 < $number_day_of_month) {
                $begin = $begin + 7;
            }

        }

        for ($k = 1; $k <= $b; $k++) {
            array_push($calender_array, $k);
        }
        array_push($calender, $calender_array);

        return response()->json($calender);
    }


}