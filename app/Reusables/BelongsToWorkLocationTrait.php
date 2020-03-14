<?php

namespace App\Reusables;
use App\Setting;
use App\Employee;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
trait BelongsToWorkLocationTrait
{
    /**
     * Scope a query to get the enable model .
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWorkLocationEnable($query)
    {
        return $query->whereHas('workLocation', function($query) {
            $query->where('enable', true);
        });
    }
    /**
     * Scope a query to search checklist. Condition is employeeName
     *
     * @param Builder $query
     * @param string $employeeName The employee name
     *
     * @return Builder
     */
    public function scopeSearchEmployeeName($query,$employeeName=null){
        if(empty($employeeName)) {
            return $query;
        }
/*        return*/ $query = $query
        ->whereRaw('concat('.\DB::getTablePrefix().'employees.first_name, '.\DB::getTablePrefix().'employees.last_name) like "%?%"',[$employeeName]);
        dd($query->toSql());
        // ->where(function($q) use ($employeeName){
        //     $q->where('employees.last_name', 'like', '%'. $employeeName.'%')
        //       ->orWhere('employees.first_name', 'like', '%'. $employeeName.'%');
        // });
    }
    /**
     * Scope a query to search checklist. Conditon is employeeId 
     *
     * @param Builder $query
     * @param string $employeeId The employee identifier
     *
     * @return Builder
     */
    public function scopeSearchEmployeeId($query,$employeeId=null){
        if(empty($employeeId)) {
            return $query;
        }
        return $query
         ->where('employees.presentation_id', 'like','%' . $employeeId . '%');
    }
    /**
     * scope aquery to get a all checklist with worklocation 
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCheckList($query, $item_type, $work_location_id, $beginDate,$endDate, $errtype=[], $employeeId=null, $employeeName=null)
    {
        return $query
        ->select(['checklist_items.date', 'checklist_items.error_type', 'employees.presentation_id', 'employees.first_name', 'employees.last_name'])
        ->join('employees', function($join) {
            $join->on('employees.id', '=', 'checklist_items.employee_id');
        })
        ->join('work_locations', function($join) {
            $join->on('work_locations.id', '=', 'employees.work_location_id');
        })
        ->join('settings', function($join) {
            $join->on('settings.work_location_id', '=', 'work_locations.id')
            ->orWhereNull('settings.work_location_id');
        })
        ->where(function($query) {
            $query
            ->where(function($q) {
                $q
                ->whereNull('settings.work_location_id')
                ->whereNotIn('work_locations.id', function($q) {
                    $q
                    ->select('work_location_id')
                    ->from('settings')
                    ->whereNotNull('work_location_id');
                });
            })
            ->orWhere(function($q) {
                $q
                ->whereNotNull('settings.work_location_id')
                ->whereIn('work_locations.id', function($q) {
                    $q
                    ->select('work_location_id')
                    ->from('settings')
                    ->whereNotNull('work_location_id');
                });
            });
        })
        ->whereBetween('checklist_items.date',[$beginDate,$endDate])
        ->where('item_type', $item_type)
        ->workLocations($work_location_id)
        ->errList($errtype)
        ->searchEmployeeId($employeeId)
        ->searchEmployeeName($employeeName);
    }

    /**
     * get checklist base on worklocation
     *
     * @param Builder $query
     * @param string $work_location_id
     *
     * @return Builder
     */
    public function scopeWorkLocations($query, $work_location_id){
        if($work_location_id == 'all') {  
            return $query;
        } else {
            return $query
            ->whereIn('work_locations.id',[$work_location_id]);
        }
    }
}