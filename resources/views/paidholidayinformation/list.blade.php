@extends('layouts.master')

@section('title', '有給休暇管理')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/multiple-select.js') }}"></script>
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
@endpush

@section('content')
    <main id="attendance_detail">
        <section class="title">
            <p class="breadcrumb"><span>勤怠管理</span><span>&emsp;&#62;&emsp;有給休暇管理</span></p>
            <div class="title_wrapper">
                <h1>有給休暇管理</h1>
                <div class="worklocation">
                    <div class="worklocation_inner">
                        <span class="right_10">{{ $current_work_location }}</span>
                        @if (isset($picker_list))
                            <p class="button"><a class="modal-open ss_size s_height btn_gray" @click="open">変更</a></p>
                        @endif
                    </div>
                    @include('layouts.work_location_picker', ['list' => $picker_list, 'target' => 'paid_holiday_list', 'singular' => false])
                </div>
            </div>
        </section>
        @include('paidholidayinformation.search_box')
        @include('paidholidayinformation.search_result', ['employees' => $employees, 'current_work_location'=>$current_work_location])
    </main>
@endsection