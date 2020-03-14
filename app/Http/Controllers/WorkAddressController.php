<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\WorkAddress;
use App\WorkLocation;
use App\Events\WorkAddressInformationChanged;
use App\Http\Requests\WorkAddressRequest;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Reusables\GetWorkAddressesBaseOnWorkLocationTrait as GetWorkAddressesTrait;
use Caeru;

class WorkAddressController extends Controller
{
    use GetWorkAddressesTrait;

    // The search controller instance
    private $search_controller = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SearchController $controller)
    {
        $this->middleware('auth');
        $this->middleware('choose');
        $this->middleware('require_work_location')->only(['create', 'store']);
        $this->middleware('can:view_work_address_info');
        $this->middleware('can:change_work_address_info')->only(['create', 'store', 'update']);
        $this->search_controller = $controller;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (session('work_address_search_history')) {

            $search_history_conditions = session('work_address_search_history')['conditions'];

            $work_arrdesses = $this->search_controller->getWorkAddressApplyConditions($search_history_conditions);
        } else {
            // By default, the list page will only list work address with status '有効'
            $default_conditions =  $this->search_controller->work_address_default_conditions;

            $work_arrdesses = $this->search_controller->getWorkAddressesApplyConditionsSaveResultToSession($default_conditions);
        }

        return view('work_address.list')->with([
            'list_work_address'    =>  $work_arrdesses->paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('work_address.new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\WorkAddressRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WorkAddressRequest $request)
    {
        $work_address = new WorkAddress($request->only([
            'presentation_id',
            'name',
            'furigana',
            'enable',
            'work_location_id',
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

        $work_address->save();

        event(new WorkAddressInformationChanged());

        $request->session()->flash('success', '保存しました');

        return Caeru::redirect('edit_work_address', $work_address->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WorkAddress  $workAddress
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkAddress $work_address, $page = 1)
    {
        return view('work_address.edit', [
            'work_address'  => $work_address,
            'page'          => $page,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\WorkAddressRequest  $request
     * @param  \App\WorkAddress  $workAddress
     * @return \Illuminate\Http\Response
     */
    public function update(WorkAddressRequest $request, WorkAddress $work_address, $page = 1)
    {
        $work_address->update($request->only([
            'presentation_id',
            'name',
            'furigana',
            'enable',
            'work_location_id',
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

        event(new WorkAddressInformationChanged());

        $request->session()->flash('success', '保存しました');

        return Caeru::redirect('edit_work_address', [$work_address->id, $page]);
    }

    /**
     * Show the form for editing the work_address's page.
     *
     * @param  WorkAddress  $work_address
     * @return \Illuminate\Http\Response
     */
    public function editDetail(WorkAddress $work_address, $page = 1)
    {
        Javascript::put([
            'model_data' => $work_address->schedules->toArray(),
        ]);
        return view('work_address.edit_detail', [
            'work_address'  => $work_address,
            'page'          => $page,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WorkAddress  $workAddress
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkAddress $work_address)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WorkAddress  $workAddress
     * @return \Illuminate\Http\Response
     */
    public function show(WorkAddress $work_address)
    {
        //
    }

}
