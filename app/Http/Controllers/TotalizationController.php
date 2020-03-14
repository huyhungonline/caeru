<?php
namespace App\Http\Controllers;

use Auth;
use Caeru;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\WorkLocation;
use App\Employee;
use App\ChecklistItem;


class TotalizationController extends Controller
{
	/**
	 * Create a new controller instance
	 */
	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('choose');
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response.
	 */
	public function index(Request $request)
	{
		$current_work_location = request()->session()->get('current_work_location');
		//dd($current_work_location);
		$currentYear = Carbon::now()->year;
		$currentMonth = Carbon::now()->month;
		$employees = Employee::paginate(20);
		// $employees = Employee::join()
		return view('totalization.list')->with([
			'employees'=>$employees,
			'currentMonth'=>$currentMonth,
			'currentYear'=>$currentYear,
		]);

	}
}