<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\WorkLocation;


class WorkLocationPickerComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $manager = Auth::user();
        $authority = $manager->workLocations()->pluck('id')->toArray();

        $list = null;
        $current = null;
        if (!$manager->company_wide_authority && count($authority) == 1) {
            $current = $manager->workLocations->first()->name;
        } else {

            $work_locations = null;
            if ($manager->company_wide_authority) {
                
                $work_locations = $manager->company->workLocations()->withCount('employees')->orderBy('view_order')->get();
                $list['company'] = [
                    'presentation_id'       =>  null,
                    'name'                  =>  '会社',
                    'todofuken'             =>  $manager->company->todofuken(),
                    'employees_count'       =>  $manager->company->employees->count()
                ];

            } else {
                
                $work_locations = WorkLocation::whereIn('id', $authority)->withCount('employees')->orderBy('view_order')->get();
                $list['multi'] = [
                    'presentation_id'   =>  null,
                    'name'              =>  '全勤務地',
                    'todofuken'         =>  null,
                    'employees_count'    =>  $work_locations->sum('employees_count'),
                ];
            }
            $list['work_locations'] = $work_locations;
            // $list = $list->transform(function($work_location, $index) {
            //     return [
            //         'id'                =>  $work_location->id,
            //         'presentation_id'   =>  $work_location->presentation_id,
            //         'name'              =>  $work_location->name,
            //         'enable'            =>  $work_location->enable,
            //         'todofuken'         =>  $work_location->todofuken(),
            //         'employee_count'    =>  $work_location->employees_count,
            //     ];
            // });
            $current_work_location = session()->get('current_work_location');
            if ($current_work_location === 'all')
                $current = '会社';
            elseif (is_array($current_work_location))
                $current = '全勤務地';
            else
                $current = WorkLocation::find($current_work_location)->name;
        }
        
        // $list is either null or an array
        // $current is either a full name of a work location or 'all' or 'multi' or a work location id
        // Javascript::put([
        //     'list'      => $list,
        //     'current'   => $current,
        // ]);
        $view->with([
            'picker_list'          => $list,
            'current_work_location'   => $current,
        ]);
    }
}