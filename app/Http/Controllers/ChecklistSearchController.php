<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Employee;
use App\WorkLocation;
use App\OptionItem;
use App\ChecklistItem;
use Carbon\Carbon;
use App\Setting;
use Caeru;
use Auth;

use App\Http\Controllers\Reusables\GetEmployeeBaseOnWorkLocationTrait as GetEmployeeTrait;
use App\Http\Controllers\Reusables\GetWorkAddressesBaseOnWorkLocationTrait as GetWorkAddressesTrait;
use App\Http\Controllers\Reusables\GetCheckListTrait;

class ChecklistSearchController extends Controller
{
    use GetEmployeeTrait, GetWorkAddressesTrait, GetCheckListTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('choose');
    }

   /**
    * Search Checklist
    *
    * @param \Illuminate\Http\Request $request The request
    *
    * @return json
    */
   public function searchCheckList(Request $request){
        $results = $this->search();
        return response()->json($results);
   }
}
