@extends('layouts.master')

@section('title', '訪問先詳細')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
    <script defer src="{{ asset('/js/components/work_address_searcher.js') }}"></script>
    <script defer src="{{ asset('/js/pages/work_address_edit_detail.js') }}"></script>
@endpush

@section('content')
    <main id="basic">
        <section class="title">
            <p class="breadcrumb"><span>基本情報</span>&emsp;&#62;&emsp;<a href="{{ Caeru::route('work_address_list', ['page' => $page]) }}">訪問先一覧</a><span>&emsp;&#62;&emsp;訪問先詳細</span></p>
            <div class="title_wrapper">
                <h1>訪問先詳細</h1>
                <div class="worklocation">
                    <div class="worklocation_inner">
                        <span class="right_10">{{ $current_work_location }}</span>
                        @if (isset($picker_list))
                            <p class="button"><a class="modal-open ss_size s_height btn_gray" @click="open">変更</a></p>
                        @endif
                    </div>
                    @include('layouts.work_location_picker', ['list' => $picker_list, 'target' => 'work_address_list', 'singular' => false])
                </div>
            </div>
        </section>
        @include('work_address.search_box', ['not_display_search_box' => true])
        @include('layouts.search_result_navigation')
        <section class="select_one2">
            <section class="tab">
                <p class="tab_button"><a class="tab_size tab_btn_gray left right_10" href="{{ Caeru::route('edit_work_address', [$work_address->id, $page]) }}">設定</a></p>
                <p class="tab_button"><a class="tab_size btn_greeen  left" href="">訪問予定</a></p>

            </section>
        </section>
        <section id="container">
            <section class="select_one2">
                <section class="right_position">
                    @can('change_work_address_info')
                        <p class="button"><a class="s_size s_height btn_blue" @click="addSchedule">追加</a></p>
                    @endcan
                </section>
            </section>
            <section class="work_plan">
                <table>
                    <th colspan="2" class="normal_height">勤務設定</th>
                </table>
                <work-schedule :presentation-data="presentation_data" v-for="(model, index) in model_data" :key="model.id" :model-data="model" @remove="removeSchedule(index)" @copy="copySchedule" :work-address-id="{{ $work_address->id }}" :work-location-id="{{ $work_address->workLocation->id }}"></work-schedule>
            </section>
            <section class="btn">
                @can('change_work_address_info')
                    <p class="button right_30"><button class="m_size l_height btn_greeen l_font" @click="submitMainForm">保存</button></p>
                    <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('edit_work_address_detail', [$work_address->id, $page]) }}">キャンセル</a></p>
                @endcan
                <p class="button"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('work_address_list', ['page' => $page]) }}">一覧に戻る</a></p>
            </section>
        </section>
    </main>
@endsection