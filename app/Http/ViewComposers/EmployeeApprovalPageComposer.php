<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\WorkLocation;
use Constants;

class EmployeeApprovalPageComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $company = Auth::user()->company;

        $employees = $company->employees->map(function($employee) {
            return [
                'id'                => $employee->id,
                'presentation_id'   => $employee->presentation_id,
                'name'              => $employee->last_name . $employee->first_name,
            ];
        });

        $work_locations = $company->workLocations->pluck('name', 'id')->toArray();

        $departments = $company->departments()->pluck('name', 'id')->toArray();

        $work_statuses = Constants::workStatuses();

        Javascript::put([
            'employees'   => $employees,
        ]);

        $view->with([
            'work_locations'    =>  $work_locations,
            'departments'       =>  $departments,
            'work_statuses'     =>  $work_statuses,
        ]);
    }
}