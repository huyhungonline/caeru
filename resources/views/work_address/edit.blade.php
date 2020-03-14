@extends('layouts.master')

@section('title', '訪問先詳細')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    @include('work_address.form', [
        'route' => Caeru::route('update_work_address', [$work_address->id, $page]),
        'default_presentation_id' => $work_address->presentation_id,
        'default_name' => $work_address->name,
        'default_furigana' => $work_address->furigana,
        'default_enable' => $work_address->enable,
        'default_work_address' => $work_address,
        'default_work_location_id' => $work_address->work_location_id,
        'default_todofuken' => $work_address->todofuken,
        'default_address' => $work_address->address,
        'default_login_range' => $work_address->login_range,
        'default_latitude' => $work_address->latitude,
        'default_longitude' => $work_address->longitude,
        'default_chief_email' => $work_address->chief_email,
        'picker_redirect_to' => 'work_address_list',
        'use_searcher'  => true,
    ])
@endsection