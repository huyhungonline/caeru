@extends('layouts.master')

@section('title', '訪問先一覧')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/components/work_address_searcher.js') }}"></script>
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
@endpush

@section('content')
    <main id="basic">
        <section class="title">
            <p class="breadcrumb"><span>基本情報</span><span>&emsp;&#62;&emsp;訪問先一覧</span></p>
            <div class="title_wrapper">
                <h1>訪問先一覧</h1>
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
        @include('work_address.search_box')
        <section class="searcher">
            @include('work_address.search_result')
        </section>
        <section class="search_result">
            <p class="button right"><a class="mm_size m_height btn_gray" href="#">フォーマットダウンロード</a></p>
            <p class="button right_10 right"><a class="m_size m_height btn_gray modal-open" data-target="con1" href="#">インポート</a></p>
        </section>
    </main>
@endsection