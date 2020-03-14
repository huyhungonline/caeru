@extends('layouts.master')

@section('title', '管理者詳細')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    @include('manager.form', [
        'route' => Caeru::route('update_manager', [$manager->id, $page]),
        'default_presentation_id' => $manager->presentation_id,
        'default_manager' => $manager,
        'default_password' => '',
        'default_password_confirm' => '',
        'default_ip_address' => $manager->ip_address,
        'default_email' => $manager->email,
        'default_enable' => $manager->enable,
        'default_super' => $manager->super,
        'default_company_wide_authority' => $manager->company_wide_authority,
        'default_authorized_locations' => $manager->workLocations()->pluck('id')->toArray(),
        'default_authority' => $manager->managerAuthority,
    ])
@endsection