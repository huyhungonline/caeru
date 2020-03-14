@extends('layouts.master')

@section('title', 'チェックリスト')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/multiple-select.js') }}"></script>
    <script defer src="{{ asset('/js/components/checklist_searcher.js') }}"></script>
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
@endpush

@section('content')
    <main id="attendance_detail">
        <section class="title">
            <p class="breadcrumb"><span>勤怠管理</span><span>&emsp;&#62;&emsp;チェックリスト</span></p>
            <div class="title_wrapper">
                <h1>チェックリスト</h1>
                <div class="worklocation">
                    <div class="worklocation_inner">
                        <span class="right_10">{{ $current_work_location }}</span>
                        @if (isset($picker_list))
                            <p class="button"><a class="modal-open ss_size s_height btn_gray" @click="open">変更</a></p>
                        @endif
                    </div>
                    @include('layouts.work_location_picker', ['list' => $picker_list, 'target' => 'checklists_list', 'singular' => false])
                </div>
            </div>
        </section>
        @include('checklist.search_box',['show_toggle_button' => true])
        @include('checklist.search_result')
    </main>
@endsection