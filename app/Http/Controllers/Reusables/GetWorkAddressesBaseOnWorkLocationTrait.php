<?php

namespace App\Http\Controllers\Reusables;

use Illuminate\Support\Facades\Auth;
use App\WorkAddress;
use App\WorkLocation;

trait GetWorkAddressesBaseOnWorkLocationTrait
{
    /**
     * Get the list of employee base on the current work location which the manager is using.
     *
     * @return QueryBuilder
     */
    private function getWorkAddressesBaseOnCurrentWorkLocation()
    {
        $chosen_work_location = session('current_work_location');

        $work_address_list = null;

        if ($chosen_work_location == "all") {

            $work_address_list = Auth::user()->company->workAddresses()->workLocationEnable();

        } elseif (is_array($chosen_work_location)) {

            $work_address_list = Auth::user()->company->workAddresses()->workLocationEnable()->whereIn('work_location_id', $chosen_work_location);

        } else {

            $work_address_list = Auth::user()->company->workAddresses()->where('work_location_id', $chosen_work_location);
        }

        return $work_address_list;
    }
}