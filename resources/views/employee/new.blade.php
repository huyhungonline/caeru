@extends('layouts.master')

@section('title', '従業員入力')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    @include('employee.form', [
        'route' => Caeru::route('store_employee'),
        'default_presentation_id' => '',
        'default_gender' => 1,
        'default_todofuken' => '',
        'default_address' => '',
        'default_email' => '',
        'default_work_location' => is_numeric(session('current_work_location')) ? session('current_work_location') : '',
        'default_department' => '',
        'default_schedule_type' => config('constants.normal_schedule'),
        'default_employment_type' => config('constants.official_employee'),
        'default_salary_type' => config('constants.monthly_salary'),
        'default_work_status' => config('constants.working'),
        'default_employee' => null,
        'picker_redirect_to' => 'create_employee',
        'use_searcher'  => false,
    ])
@endsection