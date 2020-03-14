@extends('layouts.master')

@section('title', '管理者詳細')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    @include('manager.form', [
        'route' => Caeru::route('store_manager'),
        'default_presentation_id' => '',
        'default_manager' => null,
        'default_password' => '',
        'default_password_confirm' => '',
        'default_ip_address' => '',
        'default_email' => '',
        'default_enable' => 1,
        'default_super' => false,
        'default_company_wide_authority' => 1,
        'default_authorized_locations' => array(),
        'default_authority' => null
    ])
@endsection