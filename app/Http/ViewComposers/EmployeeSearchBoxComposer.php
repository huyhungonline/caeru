<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\WorkLocation;
use App\Employee;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;

class EmployeeSearchBoxComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // No need to throw the search result order to the javascript side. It will become a mess out there.
        $search_history = [
            'conditions'    => session('employee_search_history')['conditions'],
        ];
        $current_work_location = session('current_work_location');

        // Get the list of department and employee's name, base on the current work location
        if ($current_work_location === 'all') {

            $departments = Auth::user()->company->departments->pluck('name', 'id')->toArray();

            $employee_names = Auth::user()->company->employees->map(function($item) {
                return ['name' => $item->last_name . $item->first_name];
            })->toArray();

        } elseif (is_array($current_work_location)) {

            $departments = Auth::user()->company->departments->pluck('name', 'id')->toArray();

            $employee_names = Employee::whereIn('work_location_id', $current_work_location)->get()->map(function($item) {
                return ['name' => $item->last_name . $item->first_name];
            })->toArray();

        } else {

            $work_location = WorkLocation::with('employees')->find($current_work_location);

            $departments = $work_location->activatingDepartments()->pluck('name', 'id')->toArray();

            $employee_names = $work_location->employees->map(function($item) {
                return ['name' => $item->last_name . $item->first_name];
            })->toArray();

        }

        // Get the list of work address
        $work_addresses = Auth::user()->company->workAddresses->map(function($work_address) {
            return [
                'id'    =>  $work_address->id,
                'name'  =>  $work_address->name,
            ];
        })->toArray();


        // If there is a search history, send it to the javascript side too. (Right now, there will be a search history at all time.)
        if (!empty($search_history))
            Javascript::put([
                'search_history'    => $search_history,
            ]);

        // Send the list of work addresses and employee names to the javascript side
        Javascript::put([
            'work_addresses'    => $work_addresses,
            'employee_names'    => $employee_names,
        ]);

        // Send the list of work locations and departments to the blade side
        $view->with([
            'departments'       => $departments,
            'work_locations'    => Auth::user()->company->workLocations()->pluck('name', 'id')->toArray(),
        ]);
    }
}