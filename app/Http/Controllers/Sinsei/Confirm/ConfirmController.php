<?php

namespace App\Http\Controllers\Sinsei\Confirm;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ConfirmController extends Controller
{
	public function __construct()
	{
		$this->middleware('sinsei_auth');
	}
	
	public function confirm()
	{
		return view('sinsei.confirm.confirm');
	}
	
	public function confirmMultiple()
	{
		return view('sinsei.confirm.confirm_multiple');
	}
	
	public function confirmOvertimeFlextime()
	{
		return view('sinsei.confirm.confirm_overtime_flextime');
	}
	
	public function confirmFlextime()
	{
		return view('sinsei.confirm.confirm_flextime');
	}
}
