@extends('layouts.master')

@section('title', '勤務地詳細')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    @include('work_location.form', [
        'route' => Caeru::route('update_work_location', [$work_location->id, $page]),
        'default_presentation_id' => $work_location->presentation_id,
        'default_name' => $work_location->name,
        'default_furigana' => $work_location->furigana,
        'default_enable' => $work_location->enable,
        'default_work_location' => $work_location,
        'default_todofuken' => $work_location->todofuken,
        'default_address' => $work_location->address,
        'default_login_range' => $work_location->login_range,
        'default_latitude' => $work_location->latitude,
        'default_longitude' => $work_location->longitude,
        'default_chief_email' => $work_location->chief_email,
        'employees_count' => $work_location->employees->count()
    ])
@endsection