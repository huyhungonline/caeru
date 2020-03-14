<?php
namespace App\Http\Controllers;

use Auth;
use Caeru;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\ChecklistItem;

class HomeController extends Controller 
{
	/**
	 * Display home page with checklist link and list post (お知らせ)
	 *
	 * @param \Illuminate\Http\Request $request The request
	 */
	public function index(Request $request) {

		$dakoku = ChecklistItem::where('item_type','100')->count();
		$hyou = ChecklistItem::where('item_type','200')->count();
		
		return view('home.index',compact('dakoku', 'hyou'));
	}
}