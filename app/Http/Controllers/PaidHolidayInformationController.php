<?php

namespace App\Http\Controllers;

use Auth;
use Caeru;
Use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PaidHolidayInformationRequest;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\WorkLocation;
use App\Employee;
use App\PaidHolidayInformation;
use App\Setting;
use App;
use DB;
use App\Reusables\BelongsToWorkLocationTrait;

class PaidHolidayInformationController extends Controller
{
	use BelongsToWorkLocationTrait;

	/**
	 * Create a new controller instance.
	 */
	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('choose');
	}

	/**
	 * 
	 * Display a listing of the resource.
	 * 
	 */
	public function index(Request $request)
	{
		$current_work_location = request()->session()->get('current_work_location');
		$employees = Employee::leftJoin('work_locations', function($join){
			$join->on('work_locations.id','=','employees.work_location_id');

		})
		->workLocations($current_work_location)
		->select('employees.*','work_locations.name as work_location_name')
		->paginate(20);
		return view('paidholidayinformation.list')
		->with([
			'employees'=>$employees,
			'current_work_location'=>$current_work_location,
		]);
	}

	/**
	 *
	 * Show employee information and paidholiday list of current employee.
	 *
	 */
	public function edit(Employee $employee, $page =1)
	{
		$paidholidayinformations = PaidHolidayInformation::where('employee_id','=', $employee->id)
		->orderBy('id','desc')
		->get();
		$current_work_location = request()->session()->get('current_work_location');
		$getEmployeeJoinedDate = $this->getEmployeeJoinedDate($employee->joined_date);


		return view('paidholidayinformation.edit')
		->with([
			'employee'=>$employee,
			'page'=>$page,
			'current_work_location'=>$current_work_location,
			'getEmployeeJoinedDate'=>$getEmployeeJoinedDate,
			'paidholidayinformations'=>$paidholidayinformations,

		]);
	}

	/**
	 *
	 * Get day of carried_work_paid_holidays without '日'
	 *
	 */
	private function getCarriedForwardDay($carriedForwardDay)
	{
		return preg_replace('/日/','', $carriedForwardDay);
	}


	/**
	 *
	 * get carried_work_paid_holidays without ':'
	 *
	 */
	private function getCarriedForwardTime($carrtedForwardTime)
	{
		return preg_split('/:/', $carrtedForwardTime);
	}

	/**
	 * 
	 * get carried_forward_paid_holidays from Form and revert to float
	 * 
	 */
	private function getCarriedForwardPaidHolidays($paidholidayinformation, $carriedForwardDay, $carrtedForwardTime)
	{
		return $paidholidayinformation->revertDate(
			$this->getCarriedForwardDay($carriedForwardDay),
			$this->getCarriedForwardTime($carrtedForwardTime)
		);
	}

	/**
	 *
	 * update paid holiday information of current employee
	 *
	 */
	public function update(PaidHolidayInformationRequest $request, Employee $employee, $page =1)
	{
		$paidholidayinformation = PaidHolidayInformation::find($request->presentation_id);
		$paidholidayinformation->attendance_rate = $request->attendance_rate;
		$paidholidayinformation->provided_paid_holidays = $request->provided_paid_holidays;
		$paidholidayinformation->note = $request->note;
		$paidholidayinformation->last_modified_date = Carbon::now(); 
		$paidholidayinformation->carried_forward_paid_holidays = $this->getCarriedForwardPaidHolidays($paidholidayinformation, $request->carried_forward_day, $request->carried_forward_time);
		$paidholidayinformation->last_modified_manager_id = auth()->user()->id;
		$paidholidayinformation->save();

		return back()->with('success', '保存しました');
	}

	/**
	 * Compute worked time.
	 *
	 * @param Carbon $joined_date
	 *
	 * @return string.
	 */
	private function getEmployeeJoinedDate(Carbon $joined_date)
	{
		$result = '';
		Carbon::setLocale('ja');
		$currYear = Carbon::now()->year;
		$currMonth = Carbon::now()->month;
		if($currYear !== $joined_date->year)
		{
			$year = Carbon::now()->diffForHumans($joined_date, true, false);
			$result .= $year;

		}
		if($currMonth !== $joined_date->month)
		{
			$employeeDate = Carbon::create(null,$joined_date->month, null);
			$month = Carbon::now()->diffForHumans($employeeDate, true, false);
			$result .= $month;
		}
		return $result;

	}
}