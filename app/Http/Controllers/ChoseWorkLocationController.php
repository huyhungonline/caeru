<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Events\CurrentWorkLocationChanged;
use Caeru;

class ChoseWorkLocationController extends Controller
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
     * Let the manager choose the current work location. It will show the page to select work locations.
     * Depend on the needs, this function will prepend to the work location list one first-entry.
     * This entry can be 'company' or 'all the work locations', bases on that manager's authority.
     *
     * @param Request $request
     * @return void
     */
    public function list(Request $request)
    {
        if (session('current_work_location')) {
            return Caeru::redirect('dashboard');
        }

        $current_user = $request->user();

        $list = [];
        $work_locations = null;

        if($current_user->company_wide_authority == true) {

            $work_locations = $current_user->company->workLocations()->orderBy('view_order')->orderBy('id')->get();

            $list['first'] = [
                'presentation_id'   =>  null,
                'name'              =>  '会社',
                'todofuken'         =>  $current_user->company->todofuken(),
                'employee_count'    =>  $current_user->company->employees->count()
            ];

        } else {

            $work_locations = $current_user->workLocations()->withCount('employees')->orderBy('view_order')->orderBy('id')->get();

            if ($request->singular == 0) {
                $list['first'] = [
                    'presentation_id'   =>  null,
                    'name'              =>  '全勤務地',
                    'todofuken'         =>  null,
                    'employee_count'    =>  $work_locations->sum('employees_count'),
                ];
            }

        }

        $list['work_locations'] = $work_locations;

        return view('layouts.chose_work_location', ['list' => $list]);
    }

    /**
     * Save the chosen work location to session then redirect to the previous pages.
     * After the work location was chosen correctly, it will be saved as the variable: "current_work_location" to the session.
     * The current_work_location variable may be:
     *      - a string "all". That means the current manager has company-wide authority and has chosen "all company"(ie. "会社").
     *      - an array . That means the current manager has authority on some work locations and has chosen "all of them"(ie. "全勤務地").
     *      - an integer . That means the current manager has chosen the work location with that id as the current work location.
     *
     * @param int|string $chosen
     * @return void
     */
    public function choose(Request $request, $chosen, $target = null)
    {
        $current_user = $request->user();

        $available_ids = $current_user->company->workLocations()->pluck('id')->toArray();

        if ($chosen == 'all' || in_array($chosen, $available_ids)) {
            
            if ($chosen == 'all') {

                if (!$current_user->company_wide_authority) {

                    $chosen = $current_user->workLocations()->pluck('id')->toArray();

                }
            }
            session(['current_work_location' => $chosen]);

            event(new CurrentWorkLocationChanged());
        }

        if ($target)
            return Caeru::redirect($target);
        else
            return redirect()->intended('/');

    }
}