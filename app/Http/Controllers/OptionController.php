<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\WorkLocation;
use App\Employee;
use Laracasts\Utilities\JavaScript\JavaScriptFacade; 
use JavaScript;
use App\Http\Requests\OptionItemRequest;
use App\WorkStatus;
use App\RestStatus;
use App\Department;
use DB;

class OptionController extends Controller
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('choose:singular');
        $this->middleware('can:view_option_item_info');
        $this->middleware('can:change_option_info')->only(['updateWorkAndRest']);
        $this->middleware('can:change_department_info')->only(['updateDepartments']);
        $this->middleware('can:view_option_info')->only(['redirectWorkAndRest']);
        $this->middleware('can:view_department_info')->only(['redirectDepartment']);
    }

    /**
     * get all data of work status from store
     * add attribute' status if it was work location
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getWorkStatuses($request)
    {
        $current_work_location =  $request->session()->get('current_work_location');
        $data_option_item = $request->user()->company->workStatuses;
        if ($current_work_location != "all") {
            $list_data_usage = WorkLocation::find($current_work_location)->activatingWorkStatuses();
            foreach ($data_option_item as $item) {
                ($list_data_usage->contains('id', $item->id)) ? $item->setAttribute('status' , 1) : $item->setAttribute('status', 0);
            }
        }
        return $data_option_item;
    }

    /**
     * get all data of rest status from store
     * add attribute' status if it was work location
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRestStatuses($request)
    {
        $current_work_location =  $request->session()->get('current_work_location');
        $data_option_item = $request->user()->company->restStatuses;

        if ($current_work_location != "all") {
            $list_data_usage = WorkLocation::find($current_work_location)->activatingRestStatuses();
            foreach ($data_option_item as $item) {
                ($list_data_usage->contains('id', $item->id)) ? $item->setAttribute('status' , 1) : $item->setAttribute('status', 0);
            }
        }
        return $data_option_item;
    }

    /**
     * get all data of department from store
     * add attribute' status if it was work location
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDepartments($request)
    {
        $current_work_location =  $request->session()->get('current_work_location');
        $data_option_item = $request->user()->company->departments;
        if ($current_work_location != "all") {
            $list_data_usage = WorkLocation::find($current_work_location)->activatingDepartments();
            foreach ($data_option_item as $item) {
                ($list_data_usage->contains('id', $item->id)) ? $item->setAttribute('status' , 1) : $item->setAttribute('status', 0);
            }
        }
        return $data_option_item;
    }

    /**
     * Redirect to page's work and rest
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function redirectWorkAndRest(Request $request)
    {
        JavaScript::put([
            'list_work_status_default' => $this->getWorkStatuses($request)->whereIn('name', WorkStatus::defaults())->sortBy('id')->values(),
            'list_work_status_customize' => $this->getWorkStatuses($request)->whereNotIn('name', WorkStatus::defaults())->sortBy('id')->values(),
            'list_rest_status_default' => $this->getRestStatuses($request)->whereIn('name', RestStatus::defaults())->sortBy('id')->values(),
            'list_rest_status_customize' => $this->getRestStatuses($request)->whereNotIn('name', RestStatus::defaults())->sortBy('id')->values(),
            'default_data' => RestStatus::defaults(),
            'default_work_status' => WorkStatus::defaults()
        ]);
        return view('option.edit_work_rest');
    }

    /**
     * Redirect to page's department
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function redirectDepartment(Request $request)
    {
        JavaScript::put([
            'list_department_status' => $this->getDepartments($request)->sortBy('id')->values()
        ]);
        return view('option.edit_department');
    }

    /**
     * Update the list rest and work of option item in storage.
     *
     * @param  App\Http\Requests\OptionItemRequest;  $request
     * @return \Illuminate\Http\Response
     */
    public function updateWorkAndRest(OptionItemRequest $request)
    {
        if ($request->session()->get('current_work_location') == "all") {

            if (isset($request->list_work_status_customize))
                $this->removeAndCreateWorkStatus($request->list_work_status_customize, $request->user()->company->id, WorkStatus::defaults());

            if (isset($request->list_work_status_default))
                $this->removeAndReStoreDefaultWorkStatus($request->list_work_status_default, $request->user()->company->id, WorkStatus::defaults());

            if (isset($request->list_rest_status_customize))
                $this->removeAndCreateRestStatus($request->list_rest_status_customize, $request->user()->company->id, RestStatus::defaults());

            if (isset($request->list_rest_status_default))
                $this->removeAndReStoreDefaultRestStatus($request->list_rest_status_default, $request->user()->company->id, RestStatus::defaults());

            return [
                'success' => '保存しました',
                'list_work_status_default' => $this->getWorkStatuses($request)->whereIn('name', WorkStatus::defaults())->sortBy('id')->values(),
                'list_work_status_customize' => $this->getWorkStatuses($request)->whereNotIn('name', WorkStatus::defaults())->sortBy('id')->values(),
                'list_rest_status_default' => $this->getRestStatuses($request)->whereIn('name', RestStatus::defaults())->sortBy('id')->values(),
                'list_rest_status_customize' => $this->getRestStatuses($request)->whereNotIn('name', RestStatus::defaults())->sortBy('id')->values(),
            ];

        }else{

            if (isset($request->list_work_status_default))
                $this->updateUsedWorkStatus($request->list_work_status_default, $request->session()->get('current_work_location'));

            if (isset($request->list_work_status_customize))
                $this->updateUsedWorkStatus($request->list_work_status_customize, $request->session()->get('current_work_location'));

            if (isset($request->list_rest_status_default))
                $this->updateUsedRestStatus($request->list_rest_status_default, $request->session()->get('current_work_location'));

            if (isset($request->list_rest_status_customize))
                $this->updateUsedRestStatus($request->list_rest_status_customize, $request->session()->get('current_work_location'));

            return [ 'success' => '保存しました' ];
        }
    }

    /**
     * Update the list departments in storage.
     *
     * @param  App\Http\Requests\OptionItemRequest;  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDepartments(OptionItemRequest $request)
    {
        if ($request->session()->get('current_work_location') == "all") {
            if (!$request->confirm) {
                if ($this->checkEmployeeHasUsedTo($request->list_department_status, $request->user()->company->id))
                    return response([
                        'confirm'=>true,
                        'list_department_status'=>$request->user()->company->departments
                    ]);
            }
            if (isset($request->list_department_status))
                $this->removeAndCreateDepartment($request->list_department_status, $request->user()->company->id);

            $this->updateEmployeeWithDepartment($request->user()->company);

            return [
                'success' => '保存しました',
                'list_department_status' =>  $this->getDepartments($request)->sortBy('id')->values(),
            ];

        }else {
            if (isset($request->list_department_status)) $this->updateUsedDepartment($request->list_department_status, $request->session()->get('current_work_location'));
            return [ 'success' => '保存しました' ];
        }
    }

    /**
     * create new field of work status
     * remove element if it wasn't find in $array
     *
     * @param  array  $array_work_status_new
     * @param  company_id(integer)   $company
     * @param  array($string) $array_default
     * @return list work status
     */
    private function removeAndCreateWorkStatus($array_work_status_new, $company, $array_default)
    {
        $work_statuses = WorkStatus::where('company_id', $company)->whereNotIn('name', $array_default)->get();
        foreach ($work_statuses as $work_status) {
            $check = false;
            foreach ($array_work_status_new as $work_status_new) {
                if ($work_status_new["name"] == $work_status->name) {
                    $check = true;
                    break;
                }
            }
            if (!$check) {
                DB::table('work_statuses_unused')->where('work_status_id', '=', $work_status->id)->delete();
                $work_status->forceDelete();
            }
        }
        foreach ($array_work_status_new as $work_status_new) {
            if (!isset($work_status_new["id"]) || empty($work_status_new["id"])) {
                $work_status = new WorkStatus();
                $work_status->name = $work_status_new["name"];
                $work_status->company_id = $company;
                $work_status->save();
            }
        }
    }

    /**
     * create new field of rest status
     * remove element if it wasn't find in $array
     *
     * @param  array  $array_rest_status_new
     * @param  company_id(integer)   $company
     * @param  array($string) $array_default
     * @return list work status
     */
    private function removeAndCreateRestStatus($array_rest_status_new, $company, $array_default)
    {
        $rest_statuses = RestStatus::where('company_id', $company)->whereNotIn('name', $array_default)->get();
        foreach ($rest_statuses as $rest_status) {
            $check = false;
            foreach ($array_rest_status_new as $rest_status_new) {
                if ($rest_status_new["name"] == $rest_status->name) {
                    $check = true;
                    break;
                }
            }
            if (!$check) {
                DB::table('rest_statuses_unused')->where('rest_status_id', '=', $rest_status->id)->delete();
                $rest_status->forceDelete();
            }
        }
        foreach ($array_rest_status_new as $rest_status_new) {
            if (isset($rest_status_new["id"]) && !empty($rest_status_new["id"]))
                $rest_status = RestStatus::find($rest_status_new["id"]);
            else
                $rest_status = new RestStatus();
            $rest_status->name = $rest_status_new["name"];
            $rest_status->unit_type = $rest_status_new["unit_type"];
            $rest_status->paid_type = $rest_status_new["paid_type"];
            $rest_status->company_id = $company;
            $rest_status->save();
        }
    }

    /**
     * create new field of departmetn
     * remove element if it wasn't find in $array
     *
     * @param  array  $array_rest_status_new
     * @param  company_id(integer)   $company
     * @param  array($string) $array_default
     * @return list work status
     */
    private function removeAndCreateDepartment($array_department_new, $company)
    {
        $departments = Department::where('company_id', $company)->get();
        foreach ($departments as $department) {
            $check = false;
            foreach ($array_department_new as $department_new) {
                if ($department_new["name"] == $department->name) {
                    $check = true;
                    break;
                }
            }
            if (!$check) {
                DB::table('departments_unused')->where('department_id', '=', $department->id)->delete();
                $department->forceDelete();
            }
        }
        foreach ($array_department_new as $department_new) {
            if (!isset($department_new["id"]) || empty($department_new["id"])){
                $department = new Department();
                $department->name = $department_new["name"];
                $department->company_id = $company;
                $department->save();
            }
        }
    }

    /**
     * Remove and restore the resource from trash's work status.
     *
     * @param  array  $array_type_new
     * @param  company_id  $company
     * @param  $array(string) $default
     * @return list rest status defaut
     */
    private function removeAndReStoreDefaultWorkStatus($array_type_new, $company, $default)
    {
        WorkStatus::where('company_id', $company)->whereIn('name', $default)->delete();
        foreach ($array_type_new as $type_default) {
            foreach ($default as $key => $value) {
                if ($type_default["name"] == $value) {
                    WorkStatus::withTrashed()->where('name', $value)->restore();
                }
            }
        }
        return WorkStatus::where('company_id', $company)->whereIn('name', $default)->orderBy('id')->get();
    }

    /**
     * Remove and restore the resource from trash's rest status.
     *
     * @param  array  $array_type_new
     * @param  company_id  $company
     * @param  $array(string) $default
     * @return list rest status defaut
     */
    private function removeAndReStoreDefaultRestStatus($array_type_new, $company, $default)
    {
        RestStatus::where('company_id', $company)->whereIn('name', $default)->delete();
        foreach ($array_type_new as $type_default) {
            foreach ($default as $key => $value) {
                if ($type_default["name"] == $value) {
                    RestStatus::withTrashed()->where('name', $value)->restore();
                }
            }
        }
        return RestStatus::where('company_id', $company)->whereIn('name', $default)->orderBy('id')->get();
    }

    /**
     * Remove and restore the resource from trash's rest and work.
     *
     * @param  array  $array_type_new
     * @param  company_id  $company
     * @param  type of option  $type
     * @param  $array(string) $default
     * @return list rest status defaut
     */
    private function removeAndReStoreDefault($array_type_new, $company, $type, $default)
    {
        OptionItem::where('company_id', $company)->where('type',  $type)->whereIn('name', $default)->whereNull('unit_type')->delete();
        foreach ($array_type_new as $type_default) {
            foreach ($default as $key => $value) {
                if ($type_default["name"] == $value) {
                    OptionItem::withTrashed()->where('name', $value)->where('type',  $type)->whereNull('unit_type')->restore();
                }
            }
        }
        return OptionItem::where('company_id', $company)->where('type',  $type)->whereIn('name', $default)->whereNull('unit_type')->orderBy('id')->get();
    }

    /**
     * Update work status from work status unused.
     *
     * @param  array  $array_type_new
     * @param  work_location_id(integer)  $work_location
     * @return void
     */
    private function updateUsedWorkStatus($array_type_new, $work_location)
    {
        foreach ($array_type_new as $option_item_useage) {
            if ($option_item_useage["status"] == 0) {
                DB::table('work_statuses_unused')->insert(
                    ['work_status_id' => $option_item_useage["id"], 'work_location_id' => $work_location]
                );
            }else
                DB::table('work_statuses_unused')->where('work_location_id', '=', $work_location)->where('work_status_id', '=', $option_item_useage["id"])->delete();
        }
    }

    /**
     * Update work status from rest status unused.
     *
     * @param  array  $array_type_new
     * @param  work_location_id(integer)  $work_location
     * @return void
     */
    private function updateUsedRestStatus($array_type_new, $work_location)
    {
        foreach ($array_type_new as $option_item_useage) {
            if ($option_item_useage["status"] == 0) {
                DB::table('rest_statuses_unused')->insert(
                    ['rest_status_id' => $option_item_useage["id"], 'work_location_id' => $work_location]
                );
            }else
                DB::table('rest_statuses_unused')->where('work_location_id', '=', $work_location)->where('rest_status_id', '=', $option_item_useage["id"])->delete();
        }
    }

    /**
     * Update work status from department unused.
     *
     * @param  array  $array_type_new
     * @param  work_location_id(integer)  $work_location
     * @return void
     */
    private function updateUsedDepartment($array_type_new, $work_location)
    {
        foreach ($array_type_new as $option_item_useage) {
            if ($option_item_useage["status"] == 0) {
                DB::table('departments_unused')->insert(
                    ['department_id' => $option_item_useage["id"], 'work_location_id' => $work_location]
                );
            }else
                DB::table('departments_unused')->where('work_location_id', '=', $work_location)->where('department_id', '=', $option_item_useage["id"])->delete();
        }
    }

    /**
     * Check employee is using option item delete.
     *
     * @param  array  $array_department
     * @param  company_id(integer)  $company
     * @return boolean
     */
    private function checkEmployeeHasUsedTo($array_department, $company)
    {
        $list_departments = Department::where('company_id', $company)->pluck('id');
        foreach ($list_departments as $item) {
            $check = false;
            foreach($array_department as $department){
                if (isset($department["id"]) && $item == $department["id"]) {
                    $check = true;
                    break;
                }
            }
            if (!$check && (Employee::where('department_id', $item)->count() > 0)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Update all employ with department
     *
     * @param  company_id(integer)  $company
     * @return void
     */
    private function updateEmployeeWithDepartment($company)
    {
        foreach ($company->employees as $employee) {
            if (isset($employee->department_id) && !$company->departments->contains('id',$employee->department_id)) {
                $employee->update(['department_id' => null]);
            }
        }
    }
}