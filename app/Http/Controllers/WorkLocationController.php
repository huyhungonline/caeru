<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\WorkLocationRequest;
use App\WorkLocation;
use Caeru;
use App\Http\Controllers\Reusables\GenerateNumberTrait;

class WorkLocationController extends Controller
{
    use GenerateNumberTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:change_work_location_info')->only(['create', 'store', 'update']);
        $this->middleware('can:view_work_location_info');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $manager = $request->user();
        $can_change_view_order = false;

        if ($manager->company_wide_authority == true) {
            $work_locations = $manager->company->workLocations()->orderBy('view_order')->paginate(20);
            $can_change_view_order = true;
        } else {
            $work_locations = $manager->workLocations()->orderBy('view_order')->paginate(20);
        }

        return view('work_location.list')->with([
            'work_locations'    =>  $work_locations,
            'can_change_view_order' => $can_change_view_order,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('work_location.new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\WorkLocationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WorkLocationRequest $request)
    {
        $work_location = new WorkLocation($request->only([
            'presentation_id',
            'name',
            'furigana',
            'enable',
            'postal_code',
            'todofuken',
            'address',
            'login_range',
            'latitude',
            'longitude',
            'telephone',
            'chief_first_name',
            'chief_last_name',
            'chief_first_name_furigana',
            'chief_last_name_furigana',
            'chief_email',
        ]));

        $work_location->registration_number = $this->generateUniqueNumber(WorkLocation::class, 'registration_number');
        $work_location->company()->associate($request->user()->company);

        $work_location->save();

        $request->session()->flash('success', '保存しました');

        return Caeru::redirect('edit_work_location', $work_location->id);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  WorkLocation  $work_location
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkLocation $work_location, $page = 1)
    {
        return view('work_location.edit', [
            'work_location' => $work_location,
            'page'          => $page,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\WorkLocationRequest  $request
     * @param  WorkLocation  $work_location
     * @return \Illuminate\Http\Response
     */
    public function update(WorkLocationRequest $request, WorkLocation $work_location, $page = 1)
    {
        $work_location->update($request->only([
            'presentation_id',
            'name',
            'furigana',
            'enable',
            'postal_code',
            'todofuken',
            'address',
            'login_range',
            'latitude',
            'longitude',
            'telephone',
            'chief_first_name',
            'chief_last_name',
            'chief_first_name_furigana',
            'chief_last_name_furigana',
            'chief_email',
        ]));

        $request->session()->flash('success', '保存しました');

        return Caeru::redirect('edit_work_location', [$work_location->id, $page]);
    }

}
