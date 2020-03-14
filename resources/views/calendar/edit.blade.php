@extends('layouts.master')

@section('title', 'カレンダー ')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/pages/calendar_page.js') }}"></script>
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
@endpush

@section('content')
    <main id="basic">
        <section class="title">
            <p class="breadcrumb"><span>基本情報</span><span>&emsp;&#62;&emsp;カレンダー</span></p>
            <div class="title_wrapper">
                <h1>カレンダー</h1>
                <div class="worklocation">
                    <div class="worklocation_inner">
                        <span class="right_10">{{ $current_work_location }}</span>
                        @if (isset($picker_list))
                            <p class="button"><a class="modal-open ss_size s_height btn_gray" @click="open">変更</a></p>
                        @endif
                    </div>
                    @include('layouts.work_location_picker', ['list' => $picker_list, 'target' => 'edit_calendar', 'singular' => true, 'restricted' => !Auth::user()->company->initial_calendar_completed])
                </div>
            </div>
        </section>
        <section id="calendar_container">
            <section class="select_one bg_light_green bottom_10">
                <section class="select_one_inner">
                    <section class="right_30 ico_position">
                        <a @click="getDataOfPreviousYear"><img class="ico_ico_arrow" src="{{ asset('images/ico_arrow_left1.svg') }}"></a>
                    </section>
                    <section v-cloak class="right_30 ll_font">
                        @{{ currentYear }}年
                    </section>
                    <section class="ico_position">
                        <a @click="getDataOfNextYear"><img class="ico_ico_arrow" src="{{ asset('images/ico_arrow_right1.svg') }}"></a>
                    </section>
                </section>
            </section>
            <section class="select_one2">
                <section class="tab">
                    <div class="search_box left right_30">
                        <p class="holiday_box pink_holiday left right_10"></p>
                        <p class="holiday left"><span class="right_10">法定休日</span><span class="right_10">1</span><span>日</span></p>
                    </div>
                    <div class="search_box left">
                        <p class="holiday_box blue_holiday left right_10"></p>
                        <p class="holiday left"><span class="right_10">一般休日</span><span class="right_10">1</span><span>日</span></p>
                    </div>
                    <p class="left left_30 red">フレックスの場合はカレンダーの月の横に所定労働時間を記入します</p>
                </section>
            </section>
            <section class="calendar">
                <ul class="calendar_table">
                    <li v-for="index in 6">
                        <calendar v-bind="optionsArray[index-1]" :month="index" @can('change_calendar') :editable="true" @endcan
                            @rest-day-changed="restDayChanged"
                            @flex-total-time-changed="flexTotalTimeChanged"
                        ></calendar>
                    </li>
                </ul>
                <ul class="calendar_table">
                    <li v-for="index in 12" v-if="index >6">
                        <calendar v-bind="optionsArray[index-1]" :month="index" @can('change_calendar') :editable="true" @endcan
                            @rest-day-changed="restDayChanged"
                            @flex-total-time-changed="flexTotalTimeChanged"
                        ></calendar>
                    </li>
                </ul>
            </section>
            <section class="btn">
                <p class="button right_30" v-if="!workLocation"><a class="m_size l_height btn_greeen l_font" @click="submit">勤務地に反映</a></p>
                <p class="button right_30" v-else><a class="m_size l_height btn_greeen l_font" @click="submit">保存</a></p>
                <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('edit_calendar') }}">キャンセル</a></p>
            </section>
        </section>
    </main>
@endsection