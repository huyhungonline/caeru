@extends('layouts.master')

@section('title', '勤務地入力')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    @include('work_location.form', [
        'route' => Caeru::route('store_work_location'),
        'default_presentation_id' => '',
        'default_name' => '',
        'default_furigana' => '',
        'default_enable' => 1,
        'default_work_location' => null,
        'default_todofuken' => '',
        'default_address' => '',
        'default_login_range' => '',
        'default_latitude' => '',
        'default_longitude' => '',
        'default_chief_email' => '',
    ])
@endsection