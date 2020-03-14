<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\WorkLocation;

class EmployeeFormComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Handle the logic for the departments
        $department_list = [];
        $current_work_location = session('current_work_location');
        if (is_numeric($current_work_location)) {
            $department_list = [
                $current_work_location => WorkLocation::find($current_work_location)->activatingDepartments()->pluck('name', 'id')->toArray(),
            ];
        } else {
            $work_locations = [];
            if ($current_work_location === "all")
                $work_locations = Auth::user()->company->workLocations;
            else
                $work_locations = WorkLocation::whereIn('id', $current_work_location)->get();

            $department_list = $work_locations->keyBy('id')->transform(function($work_location) {
                return $work_location->activatingDepartments()->pluck('name', 'id')->toArray();
            })->toArray();
        }

        $default_value = null;
        if ($old_value = session()->getOldInput('department_id'))
            $default_value = $old_value;
        else
            if(isset($view->getData()['employee']))
                $default_value = $view->getData()['employee']->department_id;

        // Preparing the data for displaying the chief section
        $employees = Auth::user()->company->employees->map(function($employee) {
            return [
                'id'    =>  $employee->id,
                'name'  =>  $employee->last_name . $employee->first_name,
            ];
        });

        $chiefs = null;
        if ($old_value = session()->getOldInput('chiefs')) {
            $chiefs = $old_value;
        } else {
            if (isset($view->getData()['employee']))
                $chiefs = $view->getData()['employee']->chiefs->pluck('id')->toArray();
        }

        Javascript::put([
            'department_list'   => $department_list,
            'default_value'     => $default_value,
            'chiefs'            => $chiefs,
            'employees'         => $employees,
        ]);
    }
}