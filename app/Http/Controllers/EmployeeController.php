<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\Employee;
use App\WorkLocation;
use App\Events\EmployeeInformationChanged;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\EmployeeWorkRequest;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Reusables\GetEmployeeBaseOnWorkLocationTrait as GetEmployeeTrait;
use App\Http\Controllers\Reusables\GenerateNumberTrait;
use Caeru;

class EmployeeController extends Controller
{
    use GetEmployeeTrait, GenerateNumberTrait;

    // The search controller instance
    private $search_controller = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SearchController $controller)
    {
        $this->middleware('auth');
        $this->middleware('choose');
        $this->middleware('require_work_location')->only(['create', 'store']);
        $this->middleware('can:see_employee_tab');
        $this->middleware('can:view_employee_basic_info')->only(['edit', 'create', 'store', 'update']);
        $this->middleware('can:change_employee_basic_info')->only(['create', 'store', 'update']);
        $this->middleware('can:view_employee_work_info')->only(['editWork', 'updateWork']);
        $this->middleware('can:change_employee_work_info')->only('updateWork');
        $this->search_controller = $controller;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (session('employee_search_history')) {
            $search_history_conditions = session('employee_search_history')['conditions'];

            $employees_list = $this->search_controller->getEmployeesApplyConditions($search_history_conditions);
        } else {
            // By default, the list page will only list employees with working status '勤務中'
            $default_conditions =  $this->search_controller->employee_default_conditions;

            $employees_list = $this->search_controller->getEmployeesApplyConditionsSaveResultToSession($default_conditions);
        }


        return view('employee.list')->with([
            'employees' => $employees_list->paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('employee.new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\EmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        $employee = new Employee($request->only([
            'presentation_id',
            'password',
            'first_name',
            'first_name_furigana',
            'last_name',
            'last_name_furigana',
            'birthday',
            'gender',
            'postal_code',
            'todofuken',
            'address',
            'telephone',
            'email',
            'work_location_id',
            'joined_date',
            'department_id',
            'schedule_type',
            'employment_type',
            'salary_type',
            'work_status',
            'resigned_date',
        ]));

        $employee->card_registration_number = $this->generateUniqueNumber(Employee::class, 'card_registration_number');

        $employee->save();

        $employee->chiefs()->sync($request->input('chiefs'));

        event(new EmployeeInformationChanged());

        $request->session()->flash('success', '保存しました');

        return Caeru::redirect('edit_employee', $employee->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee, $page = 1)
    {
        return view('employee.edit', [
            'employee'  => $employee,
            'page'      => $page,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EmployeeRequest  $request
     * @param  Employee     $employee
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, Employee $employee, $page = 1)
    {
        $employee->update($request->only([
            'presentation_id',
            'first_name',
            'first_name_furigana',
            'last_name',
            'last_name_furigana',
            'birthday',
            'gender',
            'postal_code',
            'todofuken',
            'address',
            'telephone',
            'email',
            'work_location_id',
            'joined_date',
            'department_id',
            'schedule_type',
            'employment_type',
            'salary_type',
            'work_status',
            'resigned_date',
        ]));

        $employee->chiefs()->sync($request->input('chiefs'));

        // If the password is changed (meaning this field is not null) then save it
        if ($request->input('password')) {
            $employee->update(['password' => $request->input('password')]);
        }

        event(new EmployeeInformationChanged());

        $request->session()->flash('success', '保存しました');

        return Caeru::redirect('edit_employee', [$employee->id, $page]);
    }

    /**
     * Show the form for editing the employee's work.
     *
     * @param  Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function editWork(Employee $employee, $page = 1)
    {
        Javascript::put([
            'model_data' => $employee->schedules->toArray()
        ]);
        return view('employee.edit_work', [
            'employee' => $employee,
            'page'      => $page,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EmployeeRequest  $request
     * @param  Employee     $employee
     * @return \Illuminate\Http\Response
     */
    public function updateWork(EmployeeWorkRequest $request, Employee $employee, $page = 1)
    {

        $employee->update($request->only([
            'holidays_update_day',
            'work_time_per_day',
            'holiday_bonus_type'
        ]));

        $employee->update([
            'paid_holiday_exception'    => $request->input('paid_holiday_exception') !== null,
        ]);

        if ($request->input('delete_card')) {
            $employee->update(['card_number' => null]);
        }

        $request->session()->flash('success', '保存しました');

        return Caeru::redirect('edit_employee_work', [$employee->id, $page]);;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
