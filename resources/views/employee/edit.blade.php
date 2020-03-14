@extends('layouts.master')

@section('title', '従業員詳細')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    @include('employee.form', [
        'route' => Caeru::route('update_employee', [$employee->id, $page]),
        'default_presentation_id' => $employee->presentation_id,
        'default_gender' => $employee->gender,
        'default_todofuken' => $employee->todofuken,
        'default_address' => $employee->address,
        'default_email' => $employee->email,
        'default_work_location' => $employee->workLocation->id,
        'default_department' => $employee->departmentName(),
        'default_schedule_type' => $employee->schedule_type,
        'default_employment_type' => $employee->employment_type,
        'default_salary_type' => $employee->salary_type,
        'default_work_status' => $employee->work_status,
        'default_employee' => $employee,
        'picker_redirect_to' => 'employees_list',
        'use_searcher'  => true,
    ])
@endsection