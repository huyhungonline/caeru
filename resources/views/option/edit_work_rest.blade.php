@extends('layouts.master')

@section('title', '項目詳細')

@section('header')
@include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/pages/option_page_work_rest.js') }}"></script>
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
@endpush

@section('content')
<main id="basic">
    <section class="title bottom_10">
        <p class="breadcrumb"><span>基本情報</span><span>&emsp;&#62;&emsp;</span><span>項目詳細</span></p>
        <div class="title_wrapper">
            <h1>項目詳細</h1>
            <div class="worklocation">
                <div class="worklocation_inner">
                    <span class="right_10">{{ $current_work_location }}</span>
                    @if (isset($picker_list))
                        <p class="button"><a class="modal-open ss_size s_height btn_gray" @click="open">変更</a></p>
                    @endif
                </div>
                @include('layouts.work_location_picker', ['list' => $picker_list, 'target' => 'edit_option_work_rest', 'singular' => true])
            </div>
        </div>
    </section>
    <section id="option_item_content">
        <section class="select_one2">
            <section class="tab">
                @can('view_option_info')
                    <p class="tab_button"><button class="tab_size tab_btn_blue left right_10">形態</button></p>
                @endcan
                @can('view_department_info')
                    <p class="tab_button"><a class="tab_size tab_btn_gray left"  href="{{ Caeru::route('edit_option_department') }}">部署</a></p>
                @endcan
            </section>
        </section>
        <section id="form1">
            @include('option.form-first')
        </section>
    </section>
</main>
@endsection


