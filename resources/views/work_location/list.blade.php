@extends('layouts.master')

@section('title', '勤務地一覧')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    <main id="basic">
        <section class="title">
            <p class="breadcrumb"><span>基本情報</span><span>&emsp;&#62;&emsp;勤務地一覧</span></p>
            <div class="title_wrapper">
                <h1>勤務地一覧</h1>
            </div>
        </section>
        <section class="pager">
            {{ $work_locations->links(null, ['sum_line' => true]) }}
            @can('change_work_location_info')
            <p class="button right_position"><a class="m_size s_height btn_blue" href="{{ Caeru::route('create_work_location') }}">新規登録</a></p>
            @endcan
        </section>
        <section class="default_table">
            @include('work_location.list_table', ['work_locations' => $work_locations])
        </section>
        <section class="pager">
            {{ $work_locations->links(null, ['sum_line' => false]) }}
        </section>
    </main>
@endsection