<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Http\Requests\UpdateCompanyInfoRequest;
use App\Company;

class CompanyController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view_company_info')->except('dashboard');
        $this->middleware('can:change_company_info')->only('update');
    }

    /**
	 * The index function for the home page
	 */
	public function dashboard()
    {
		return view('company.dashboard');
	}

    /**
     * Go to edit page of Company's Information
     */
    public function edit()
    {
        return view('company.edit', [
            'company' => Company::first()
        ]);
    }

    /**
     * Update the Company's Information
     */
    public function update(UpdateCompanyInfoRequest $request)
    {

        $company = Company::first();

        $company->update($request->only([
            'name',
            'furigana',
            'postal_code',
            'todofuken',
            'address',
            'telephone',
            'fax',
            'ceo_first_name',
            'ceo_last_name',
            'ceo_first_name_furigana',
            'ceo_last_name_furigana',
            'ceo_email',
            'billing_person_first_name',
            'billing_person_last_name',
            'billing_person_first_name_furigana',
            'billing_person_last_name_furigana',
            'billing_person_email',
            'date_separate_time',
            'date_separate_type',
            'use_address_system'
        ]));

        $request->session()->flash('success', '保存しました');

        return redirect()->refresh();
    }

}
