<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\PlannedSchedule;
use App\WorkAddress;
use App\Employee;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;

class WorkAddressSearchBoxComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // First check if there's search history, and if yes send them
        $search_history = [
            'conditions'    => session('work_address_search_history')['conditions'],
        ];

        if (!empty($search_history))
            Javascript::put([
                "search_history" => $search_history,
            ]);


        // Then process to get all the other presentation data and send them
        $manager = Auth::user();

        if ($manager->company_wide_authority) {

            $work_addresses = $manager->company->workAddresses;

            $employees = $manager->company->employees();

        } else {

            $work_locations = $manager->workLocations;

            $work_addresses = WorkAddress::whereIn('work_location_id', $work_locations->pluck('id'))->get();

            $employees = Employee::whereIn('work_location_id', $work_locations->pluck('id'));
        }

        // Get a name list of employees that have schedule that has work address
        $employee_names = $employees->whereHas('schedules', function($query) {
            $query->whereNotNull('work_address_id');
        })->get()->map(function($item) {
            return ['name' => $item->last_name . $item->first_name];
        })->toArray();

        // We have to do this because the address field is not always presented on an object of work_address model.
        // If we use map function, the result array will have null value, which make the autocomplete malfunction.
        $work_address_names = [];
        $work_address_addresses = [];
        foreach ($work_addresses as $work_address) {
            $work_address_names[] = [ 'name' => $work_address->name ];
            if (isset($work_address->todofuken) && isset($work_address->address)) {
                $work_address_addresses[] = [ 'name' => $work_address->address ];
            }
        }

        Javascript::put([
            'work_address_names'        => $work_address_names,
            'work_address_addresses'    => $work_address_addresses,
            'employee_names'            => $employee_names,
        ]);
    }
}