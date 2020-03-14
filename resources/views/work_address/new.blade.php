@extends('layouts.master')

@section('title', '訪問先詳細')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    @include('work_address.form', [
        'route' => Caeru::route('store_work_address'),
        'default_presentation_id' => '',
        'default_name' => '',
        'default_furigana' => '',
        'default_enable' => 1,
        'default_work_address' => null,
        'default_work_location_id' => is_numeric(session('current_work_location')) ? session('current_work_location') : '',
        'default_todofuken' => '',
        'default_address' => '',
        'default_login_range' => '',
        'default_latitude' => '',
        'default_longitude' => '',
        'default_chief_email' => '',
        'picker_redirect_to' => 'create_work_address',
        'use_searcher'  => false,
    ])
@endsection