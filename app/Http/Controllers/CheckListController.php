<?php
namespace App\Http\Controllers;

use Auth;
use Caeru;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\Http\Controllers\ChecklistSearchController;
use App\Setting;
use App\Reusables;
use App\ChecklistItem;

use App\Http\Controllers\Reusables\GetCheckListTrait;

class CheckListController extends Controller
{
    use GetCheckListTrait;

    private $checklist_search_controller = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct(ChecklistSearchController $controller)
    {
        $this->middleware('auth');
        $this->middleware('choose');
        $this->checklist_search_controller = $controller;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $current_work_location = request()->session()->get('current_work_location');
        $refreshSession = !!!$request->refreshSession;
        $results = $this->search($refreshSession);
        
        $currentMonth = $results['currentMonth'];
        $currentYear = $results['currentYear'];
        $checklistsJson = json_encode($results['checklistsJson']);
        $checklistsHistory = json_encode($results['checklistsHistory']);
        $displayHistory = true;
        return view('checklist.list', compact(
            'checklistsJson', 'currentMonth', 'currentYear', 'checklistsHistory','displayHistory'));
    }
}