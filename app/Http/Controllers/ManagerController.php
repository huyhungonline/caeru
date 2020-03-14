<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Manager;
use App\WorkLocation;
use App\IpAddress;
use App\Http\Requests\ManagerRequest;
use Caeru;

class ManagerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:change_manager_info');
        $this->middleware('require_work_location')->only(['create', 'store']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company = $request->user()->company;

        return view('manager.list')->with([
            'managers'    =>  $company->managers()->orderBy('view_order')->orderBy('id')->paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('manager.new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ManagerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ManagerRequest $request)
    {
        $manager = new Manager($request->only([
            'presentation_id',
            'first_name',
            'first_name_furigana',
            'last_name',
            'last_name_furigana',
            'password',
            'telephone',
            'email',
            'enable',
            'company_wide_authority',
        ]));

        $manager->company()->associate($request->user()->company);

        $manager->save();

        $request->session()->flash('success', '保存しました');

        return Caeru::redirect('edit_manager', $manager->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function edit($manager, $page = 1)
    {
        return view('manager.edit', [
            'manager'   => $manager,
            'page'      => $page,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ManagerRequest  $request
     * @param  Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function update(ManagerRequest $request, Manager $manager, $page = 1)
    {
        $manager->update($request->only([
            'presentation_id',
            'first_name',
            'first_name_furigana',
            'last_name',
            'last_name_furigana',
            'telephone',
            'email',
            'enable',
            'company_wide_authority',
        ]));

        // If the password is changed (meaning this field is not null) then save it
        if ($request->input('password')) {
            $manager->update(['password' => $request->input('password')]);
        }

        // Make sure that the super user will never be disable or have authority changed
        if ($manager->super) {
            $manager->enable = 1;
            $manager->company_wide_authority = 1;
        }

        $request->session()->flash('success', '保存しました');

        return Caeru::redirect('edit_manager', [$manager, $page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
   
}
