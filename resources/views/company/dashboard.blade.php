@extends('layouts.master')

@section('title', 'ホーム')

@section('header')
    @include('layouts.header', [ 'active' => 1 ])
@endsection

@section('content')
    <main id ="wrapper">
        <section class="">
            <h1>This is the Dashboard!</h1>
        </section>
    </main>
@endsection