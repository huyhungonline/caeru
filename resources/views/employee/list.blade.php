@extends('layouts.master')

@section('title', '勤務地一覧')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/multiple-select.js') }}"></script>
    <script defer src="{{ asset('/js/components/employee_searcher.js') }}"></script>
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
@endpush

@section('content')
    <main id="basic">
        <section class="title">
            <p class="breadcrumb"><span>基本情報</span><span>&emsp;&#62;&emsp;従業員一覧</span></p>
            <div class="title_wrapper">
                <h1>従業員一覧</h1>
                <div class="worklocation">
                    <div class="worklocation_inner">
                        <span class="right_10">{{ $current_work_location }}</span>
                        @if (isset($picker_list))
                            <p class="button"><a class="modal-open ss_size s_height btn_gray" @click="open">変更</a></p>
                        @endif
                    </div>
                    @include('layouts.work_location_picker', ['list' => $picker_list, 'target' => 'employees_list', 'singular' => false])
                </div>
            </div>
        </section>
        <section class="list_page_search_box">
            @include('employee.search_box', ['show_toggle_button' => true])
        </section>
        <section class="searcher">
            @include('employee.search_result')
        </section>
        @can('change_employee_basic_info')
            @include('layouts.import', [ 'url' => Caeru::route('upload_employee') ])
        @endcan
    </main>
@endsection