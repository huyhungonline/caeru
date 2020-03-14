<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Employee;
use App\WorkLocation;
use App\OptionItem;
use App\Http\Controllers\Reusables\GetEmployeeBaseOnWorkLocationTrait as GetEmployeeTrait;
use App\Http\Controllers\Reusables\GetWorkAddressesBaseOnWorkLocationTrait as GetWorkAddressesTrait;
use Constants;

class SearchController extends Controller
{
    use GetEmployeeTrait, GetWorkAddressesTrait;
    

    /**
     * The default search condition for the employee list page.
     *
     *
     */
    public  $employee_default_conditions = null;


    /**
     * The default search condition for the work address list page.
     *
     *
     */
    public  $work_address_default_conditions = null;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('choose');
        $this->middleware('can:see_employee_tab')->only('searchEmployee');
        $this->middleware('can:view_work_address_info')->only('searchWorkAddress');

        // Declare the default conditions
        $this->employee_default_conditions = [
            null,
            null,
            null,
            null,
            [],
            config('constants.working'),
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        ];

        $this->work_address_default_conditions = [
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
        ];
    }


    /**
     * Search employee
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function searchEmployee(Request $request)
    {

        $conditions = $request->input('conditions') or $this::employee_default_conditions;

        $result = $this->getEmployeesApplyConditionsSaveResultToSession($conditions);

        return view('employee.search_result')->with([
            'employees' => $result->paginate(20),
        ]);
    }

    /**
     * Search work address
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function searchWorkAddress(Request $request)
    {

        $conditions = $request->input('conditions') or $this::work_address_default_conditions;

        $result = $this->getWorkAddressesApplyConditionsSaveResultToSession($conditions);

        return view('work_address.search_result')->with([
            'list_work_address' => $result->paginate(20),
        ]);
    }


    /**
     * Get data base on the current work location, then apply the given conditions to it,
     * then generate and save a search history to session.
     *
     * @param   array                   $condition
     * @return  QueryBuilder
     */
    public function getEmployeesApplyConditionsSaveResultToSession($conditions)
    {
        $employees_list = $this->getEmployeesBaseOnCurrentWorkLocation();

        $result = $this->applyEmployeeConditions($employees_list, $conditions);

        $this->saveSearchHistoryToSession($conditions, $result);

        return $result;
    }


    /**
     * Get data base on the current work location, then apply the given conditions to it,
     * then generate and save a search history to session.
     *
     * @param   array                   $condition
     * @return  QueryBuilder
     */
    public function getWorkAddressesApplyConditionsSaveResultToSession($conditions)
    {
        $work_address_list = $this->getWorkAddressesBaseOnCurrentWorkLocation();

        $result = $this->applyWorkAddressConditions($work_address_list, $conditions);

        $this->saveWorkAddressSearchHistoryToSession($conditions, $result);

        return $result;
    }


    /**
     * Get data base on the current work location, then apply the given conditions to it,
     * then return. The purpose of this function is to be use by the Change View Order component. Actually, it can
     * be use in the list page too.
     *
     * @param   array                   $condition
     * @return  QueryBuilder
     */
    public function getEmployeesApplyConditions($conditions)
    {
        $employees_list = $this->getEmployeesBaseOnCurrentWorkLocation();

        $result = $this->applyEmployeeConditions($employees_list, $conditions);

        return $result;
    }


    /**
     * Get data base on the current work location, then apply the given conditions to it,
     * then return.
     *
     * @param   array                   $condition
     * @return  QueryBuilder
     */
    public function getWorkAddressApplyConditions($conditions)
    {
        $work_addresses_list = $this->getWorkAddressesBaseOnCurrentWorkLocation();

        $result = $this->applyWorkAddressConditions($work_addresses_list, $conditions);

        return $result;
    }


    /**
     * Apply the conditions to search employee
     *
     * @param   QueryBuilder            $query
     * @param   array                   $condition
     * @return  QueryBuilder
     */
    public function applyEmployeeConditions($query, $conditions){

        if (isset($conditions[0]))
            $query = $query->where('employees.presentation_id', '=', $conditions[0]);
        if (isset($conditions[1]))
            $query = $query->where(\DB::raw('CONCAT_WS("", last_name, first_name)'), 'like', '%' . $conditions[1] . '%');
        if (isset($conditions[2]))
            $query = $query->where('schedule_type', '=', $conditions[2]);
        if (isset($conditions[3]))
            $query = $query->where('employment_type', '=', $conditions[3]);
        if (!empty($conditions[4]))
            $query = $query->whereIn('department_id', $conditions[4]);
        if (isset($conditions[5]))
            $query = $query->where('work_status', '=', $conditions[5]);
        if (isset($conditions[6]))
            $query = $query->where('salary_type', '=', $conditions[6]);
        if (isset($conditions[7]))
            $query = $query->where('employees.address', 'like', '%' . $conditions[7] . '%');
        if (isset($conditions[8]))
            $query = $query->where('gender', '=', $conditions[8]);
        if (isset($conditions[9]))
            $query = $query->whereDate('birthday', '>=', $conditions[9]);
        if (isset($conditions[10]))
            $query = $query->whereDate('birthday', '<=', $conditions[10]);
        if (isset($conditions[11]))
            $query = $query->whereYear('joined_date', '=', $conditions[11]);
        if (isset($conditions[12]))
            $query = $query->whereMonth('joined_date', '=', $conditions[12]);
        if (isset($conditions[13]))
            $query = $query->whereDay('joined_date', '=', $conditions[13]);
        if (isset($conditions[14]))
            $query = $query->whereYear('resigned_date', '=', $conditions[14]);
        if (isset($conditions[15]))
            $query = $query->whereMonth('resigned_date', '=', $conditions[15]);
        if (isset($conditions[16]))
            $query = $query->whereDay('resigned_date', '=', $conditions[16]);
        if (isset($conditions[17]))
            if ($conditions[17] == true)
                $query = $query->has('subordinates');
            else
                $query = $query->doesntHave('subordinates');
        if (isset($conditions[18]))
            if ($conditions[18] == true)
                $query = $query->whereNotNull('card_number');
            else
                $query = $query->whereNull('card_number');
        if (isset($conditions[19]))
            $query = $query->whereHas('schedules', function($query) use ($conditions) {
                $query->whereHas('workLocation', function($query) use ($conditions) {
                    $query->where('work_locations.id', '=', $conditions[19]);
                });
            });
        if (isset($conditions[20]))
            $query = $query->whereHas('schedules', function($query) use ($conditions) {
                $query->whereHas('workAddress', function($query) use ($conditions) {
                    $query->where('work_addresses.name', 'like', '%' . $conditions[20] . '%');
                });
            });


        // Finally, we order the list
        return $query->orderBy('work_location_id')->orderBy('view_order');
    }


    /**
     * Generate a search history of the request and save it to session.
     *
     * @param   array                   $condition
     * @param   QueryBuilder            $result
     * @return  void
     */
    private function saveSearchHistoryToSession($conditions, $result) {
        $result_text = [];
        if (isset($conditions[0]))
            $result_text['従業員ID'] = $conditions[0];
        if (isset($conditions[1]))
            $result_text['従業員名'] = $conditions[1];
        if (isset($conditions[2]))
            $result_text['就労形態'] = Constants::scheduleTypes()[$conditions[2]];
        if (isset($conditions[3]))
            $result_text['採用形態'] = Constants::employmentTypes()[$conditions[3]];
        if (!empty($conditions[4]))
            $result_text['部署'] = OptionItem::where('type', 3)->whereIn('id', $conditions[4])->pluck('name')->toArray();
        if (isset($conditions[5]))
            $result_text['雇用状態'] = Constants::workStatuses()[$conditions[5]];
        if (isset($conditions[6]))
            $result_text['給与形態'] = Constants::salaryTypes()[$conditions[6]];
        if (isset($conditions[7]))
            $result_text['住所'] = $conditions[7];
        if (isset($conditions[8]))
            $result_text['性別'] = Constants::genders()[$conditions[8]];
        if (isset($conditions[9]))
            $result_text['生年月日'][] = $conditions[9];
        if (isset($result_text['生年月日']))
            $result_text['生年月日'][] = '〜';
        if (isset($conditions[10]))
            if (empty($result_text['生年月日']))
                $result_text['生年月日'][] = '〜 ' . $conditions[10];
            else
                $result_text['生年月日'][] = $conditions[10];
        if (isset($conditions[11]))
            $result_text['入社年月日'][] = $conditions[11] . '年';
        if (isset($conditions[12]))
            $result_text['入社年月日'][] = $conditions[12] . '月';
        if (isset($conditions[13]))
            $result_text['入社年月日'][] = $conditions[13] . '日';
        if (isset($conditions[14]))
            $result_text['退職日'][] = $conditions[14] . '年';
        if (isset($conditions[15]))
            $result_text['退職日'][] = $conditions[15] . '月';
        if (isset($conditions[16]))
            $result_text['退職日'][] = $conditions[16] . '日';
        if (isset($conditions[17]))
            $result_text['承認対象者'] = $conditions[17] ? '有' : '無';
        if (isset($conditions[18]))
            $result_text['ICカード登録'] = $conditions[18] ? '有' : '無';
        if (isset($conditions[19]))
            $result_text['勤務地'] = WorkLocation::find($conditions[19])->name;
        if (isset($conditions[20]))
            $result_text['訪問先'] = $conditions[20];


        $search_result_order = $result->get()->transform(function($employee, $key) {
            return [
                'id'                =>  $employee->id,
                'presentation_id'   =>  $employee->presentation_id,
                'name'              =>  $employee->last_name . $employee->first_name,
                'true_view_order'   =>  $employee->view_order,
            ];
        })->toArray();
        $search_history = [
            'conditions'            =>  $conditions,
            'count'                 =>  $result->count(),
            'result_text'           =>  $result_text,
            'result_order'          =>  $search_result_order,
            'default'               =>  $conditions == $this->employee_default_conditions,
        ];

        session(['employee_search_history' => $search_history]);
    }


    /**
     * Apply the conditions to search work address
     *
     * @param   QueryBuilder            $query
     * @param   array                   $condition
     * @return  QueryBuilder
     */
    private function applyWorkAddressConditions($query, $conditions){

        if (isset($conditions[0]))
            $query = $query->where('work_addresses.presentation_id', '=', $conditions[0]);
        if (isset($conditions[1]))
            $query = $query->where('work_addresses.name', 'like', '%' . $conditions[1] . '%');
        if (isset($conditions[2]))
            $query = $query->whereHas('schedules', function($query) use ($conditions) {
                $query->whereHas('employee', function($query) use ($conditions) {
                    $query->where('employees.presentation_id', '=', $conditions[2]);
                });
            });
        if (isset($conditions[3]))
            $query = $query->whereHas('schedules', function($query) use ($conditions) {
                $query->whereHas('employee', function($query) use ($conditions) {
                    $query->where(\DB::raw('CONCAT_WS("", last_name, first_name)'), 'like', '%' . $conditions[3] . '%');
                });
            });
        if (isset($conditions[4]))
            $query = $query->where('work_addresses.enable', '=', $conditions[4]);
        if (isset($conditions[5]))
            $query = $query->where('work_addresses.address', 'like', '%' . $conditions[5] . '%');
        if (isset($conditions[6]))
            $query = $query->where('work_addresses.login_range', '>=', $conditions[6]);
        if (isset($conditions[7]))
            $query = $query->where('work_addresses.login_range', '<=', $conditions[7]);

        // Finally, we order the list
        return $query->orderBy('work_locations.view_order')->orderBy('furigana');
    }


    /**
     * Generate a work address search history of the request and save it to session.
     *
     * @param   array                   $condition
     * @param   QueryBuilder            $result
     * @return  void
     */
    private function saveWorkAddressSearchHistoryToSession($conditions, $result) {
        $result_text = [];
        if (isset($conditions[0]))
            $result_text['訪問先ID'] = $conditions[0];
        if (isset($conditions[1]))
            $result_text['訪問先名'] = $conditions[1];
        if (isset($conditions[2]))
            $result_text['従業員ID'] = $conditions[2];
        if (isset($conditions[3]))
            $result_text['従業員名'] = $conditions[3];
        if (isset($conditions[4]))
            $result_text['状態'] = $conditions[4] ? '有効' : '無効';
        if (isset($conditions[5]))
            $result_text['住所'] = $conditions[5];
        if (isset($conditions[6]))
            $result_text['許容範囲'][] = $conditions[6] . 'm';
        if (isset($result_text['許容範囲']))
            $result_text['許容範囲'][] = '〜';
        if (isset($conditions[7]))
            if (empty($result_text['許容範囲']))
                $result_text['許容範囲'][] = '〜 ' . $conditions[7] . 'm';
            else
                $result_text['許容範囲'][] = $conditions[7] . 'm';

        $search_result_order = $result->get()->transform(function($work_address) {
            return [
                'id'                =>  $work_address->id,
                'presentation_id'   =>  $work_address->presentation_id,
                'name'              =>  $work_address->name,
            ];
        })->toArray();
        $search_history = [
            'conditions'            =>  $conditions,
            'count'                 =>  $result->count(),
            'result_text'           =>  $result_text,
            'result_order'          =>  $search_result_order,
            'default'               =>  $conditions == $this->work_address_default_conditions,
        ];

        session(['work_address_search_history' => $search_history]);
    }
}
