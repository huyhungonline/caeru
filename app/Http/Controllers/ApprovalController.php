<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\Events\EmployeeApprovalRelationshipChanged;
use App\Http\Requests\UpdateApprovalRelationshipRequest;
use App\Http\Requests\MoveApprovalRelationshipRequest;
use App\Employee;
use Caeru;
use Constants;

class ApprovalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Get the subordinates list of an employee and display the page
     *
     * @param Employee      $employee
     * @return \Illuminate\Http\Response
     */
    public function list(Employee $employee, $page = 1, $return = 'list')
    {
        $subordinates = $this->load($employee);

        Javascript::put([
            'current_employee'      => $employee->id,
            'subordinates'          => $subordinates,
        ]);

        // To be remove later
        return view('employee.approval', [
            'current_employee'      => $employee->id,
            'subordinates'          => $subordinates,
            'return'                => ($return === 'list') ? Caeru::route('employees_list', ['page' => $page]) : Caeru::route('edit_employee', [$employee->id, $page]),
        ]);
    }


    /**
     * Search for employees
     *
     * @param Request      $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $conditions = $request->input('conditions');

        $result = $this->applyConditions($conditions);

        $result = $result->get()->map(function($employee) {
            return [
                'attached'          =>      false,
                'id'                =>      $employee->id,
                'presentation_id'   =>      $employee->presentation_id,
                'name'              =>      $employee->last_name . $employee->first_name,
                'work_location'     =>      $employee->workLocation->name,
                'work_status'       =>      Constants::workStatuses()[$employee->work_status],
            ];
        });

        return $result;
    }


    /**
     * Search for subordinates of this employee
     *
     * @param Employee      $employee
     * @return \Illuminate\Http\Response
     */
    public function load(Employee $employee)
    {
        $subordinates = $employee->subordinates()->orderBy('last_name_furigana')->orderBy('first_name_furigana')->get()->map(function($subordinate) use($employee) {
            return [
                'attached'          =>      true,
                'id'                =>      $subordinate->id,
                'presentation_id'   =>      $subordinate->presentation_id,
                'name'              =>      $subordinate->last_name . $subordinate->first_name,
                'work_location'     =>      $subordinate->workLocation->name,
                'work_status'       =>      Constants::workStatuses()[$subordinate->work_status],
            ];
        });

        return $subordinates;
    }


    /**
     * Update the relationship for this pair of employees
     *
     * @param UpdateApprovalRelationshipRequest      $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateApprovalRelationshipRequest $request)
    {

        $chief = Employee::find($request->input('current'));

        $subordinate_id = $request->input('target');

        if ($request->input('status') === true) {
            $chief->subordinates()->attach($subordinate_id);
        } else {
            $chief->subordinates()->detach($subordinate_id);
        }

        event(new EmployeeApprovalRelationshipChanged());

        return [ 'success' => '保存しました'];
    }


    /**
     * Move the entire relationship of an employee to another
     *
     * @param MoveApprovalRelationshipRequest      $request
     * @return \Illuminate\Http\Response
     */
    public function move(MoveApprovalRelationshipRequest $request)
    {

        $old_chief = Employee::find($request->input('current'));

        $new_chief = Employee::find($request->input('new'));

        $subordinates = $old_chief->subordinates->pluck('id');

        $new_chief->subordinates()->attach($subordinates);

        $old_chief->subordinates()->detach();

        event(new EmployeeApprovalRelationshipChanged());

        return [ 'success' => '保存しました'];
    }


    /**
     * Apply the conditions to query for employees
     *
     * @param int           $chief
     * @param array         $conditions
     * @return QueryBuilder
     */
    private function applyConditions($conditions)
    {
        $query = Auth::user()->company->employees();

        if ($conditions[0])
            $query = $query->where('employees.presentation_id', 'like', '%' . $conditions[0] . '%');
        if ($conditions[1])
            $query = $query->where(\DB::raw('CONCAT_WS("", last_name, first_name)'), 'like', '%' . $conditions[1] . '%');
        if ($conditions[2])
            $query = $query->where('work_status', '=', $conditions[2]);
        if ($conditions[3])
            $query = $query->whereHas('workLocation', function($query) use ($conditions) {
                $query->where('work_locations.id', '=', $conditions[3]);
            });
        if ($conditions[4])
            $query = $query->whereIn('department_id', $conditions[4]);

        return $query->orderBy('last_name_furigana')->orderBy('first_name_furigana');
    }
}
