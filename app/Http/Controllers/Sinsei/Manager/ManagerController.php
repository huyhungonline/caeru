<?php

namespace App\Http\Controllers\Sinsei\Manager;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
	public function __construct()
	{
		$this->middleware('sinsei_auth');
	}
	
	public function lists()
	{
		return view('sinsei.manager.list');
	}
	
	public function detail()
	{
		return view('sinsei.manager.detail');
	}
	
	public function detailFlextime()
	{
		return view('sinsei.manager.detail_flextime');
	}
}
