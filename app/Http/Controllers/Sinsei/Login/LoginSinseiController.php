<?php

namespace App\Http\Controllers\Sinsei\Login;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Employee;
use Caeru;
use Illuminate\Support\Facades\Hash;

//use Auth;

class LoginSinseiController extends Controller
{
	/**
	 * Create a new authentication controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest:,sinsei', ['except' => 'logout']);
	}
	
    public function showLoginPage()
    {
    	return view('sinsei.login.login');
    }
	
	public function login(LoginRequest $request)
	{
		$sinsei_user = $this->attemptLogin($request);
		if ($sinsei_user) {
			$request->session()->regenerate();
			session(['sinsei_user' => $sinsei_user]);
			$current_company_code = $request->route('company_code');
			session(['current_company_code' => $current_company_code]);
			return Caeru::redirect('personal_detail');
		}
		
		$errors = ['auth_failed' => trans('auth.failed')];
		if ($request->expectsJson()) {
			return response()->json($errors, 422);
		}
		
		return redirect()->back()
			->withInput($request->only('presentation_id'))
			->withErrors($errors);
	}
	
	/**
	 * Attempt to log the user into the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return bool
	 */
	protected function attemptLogin(Request $request)
	{
		$employee = Employee::where('presentation_id', '=', $request->input('presentation_id'))
						  ->first();
		if ($employee && Hash::check($request->input('password'),$employee->password)){
			return $employee;
		}
		return null;
	}
	
	/**
	 * Log the user out of the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function logout(Request $request)
	{
		// At this point, there is no 'company_code' parameter in the rout. So we have to get this code from the session before it get flushed,
		// or else we can not redirect the user to the loggin page of the right company.
		$current_company_code = session('current_company_code');
		$request->session()->flush();
		$request->session()->regenerate();
		return Caeru::redirect('ss_login', ['company_code' => $current_company_code]);
	}
	
	
}
