<?php

namespace App\Http\Controllers\Sinsei\Personal;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PersonalController extends Controller
{
	public function __construct()
	{
		$this->middleware('sinsei_auth');
	}
	
	public function account()
	{
		return view('sinsei.personal.account');
	}
	
	public function detail()
	{
		return view('sinsei.personal.detail');
	}
	
	public function detailFlextime()
	{
		return view('sinsei.personal.detail_flextime');
	}
}
