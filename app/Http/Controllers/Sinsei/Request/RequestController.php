<?php

namespace App\Http\Controllers\Sinsei\Request;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
	public function __construct()
	{
		$this->middleware('sinsei_auth');
	}
	
	public function applying()
	{
		return view('sinsei.request.applying');
	}
	
	public function applyingFlextime()
	{
		return view('sinsei.request.applying_flextime');
	}
	
	public function approval()
	{
		return view('sinsei.request.approval');
	}
	
	public function approvalFlextime()
	{
		return view('sinsei.request.approval_flextime');
	}
	
	public function index()
	{
		return view('sinsei.request.index');
	}
	
	public function requestFlextime()
	{
		return view('sinsei.request.request_flextime');
	}
	
	public function requestRejection()
	{
		return view('sinsei.request.request_rejection');
	}
	
	public function requestRejectionFlextime()
	{
		return view('sinsei.request.request_rejection_flextime');
	}
}
