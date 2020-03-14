@extends('layouts.master')

@section('title', '承認者詳細')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/multiple-select.js') }}"></script>
    <script defer src="{{ asset('/js/pages/employees_approval.js') }}"></script>
@endpush

@section('content')
<main id="basic">
    <section class="title">
        <p class="breadcrumb"><span>基本情報</span><span>&emsp;&#62;&emsp;承認者詳細</span></p>
        <div class="title_wrapper">
            <h1>承認者詳細</h1>
            <section class="select_one const">
                <section class="select_one_inner">
                    <div class="search_setting">
                        <span class="right_30">承認者</span>
                        <span class="right_10">従業員ID</span>
                        <input type="text" class="vue_place_holder s_size" v-cloak>
                        <autocomplete :suggestions="employees" custom-class="s_size" :linked="true" :allow-null="false"
                            :initial-id="currentAutocompleteId"
                            :current-id="currentAutocompleteId"
                            filtered-field-name="presentation_id"
                            @selected="currentEmployeeSelected"
                        ></autocomplete>
                    </div>
                    <div class="search_setting">
                    <span class="right_10">従業員名</span>
                    <input type="text" class="vue_place_holder m_size" v-cloak>
                    <autocomplete :suggestions="employees" custom-class="m_size" :linked="true" :allow-null="false"
                            :initial-id="currentAutocompleteId"
                            :current-id="currentAutocompleteId"
                            @selected="currentEmployeeSelected"
                        ></autocomplete>
                    </div>
                </section>
            </section>
            <section class="right_position2">
                <div class="button">
                    <a class="m_size s_height btn_greeen modal-open"  data-target="con1" @click="showPopup">承認者機能の移行</a>
                </div>
                <transition name="fade">
                    <div class="approval_pop_up_wrapper" v-cloak v-show="displayPopup">
                        <div class="approval_pop_up">
                            <section class="select_one">
                                <section class="select_one_innner">
                                    <h2>承認者機能の移行</h2>
                                </section>
                            </section>
                            <section class="select_one bottom_10 authorizer">
                                <section class="select_one_inner">
                                    <div>
                                        <div class="right_30 search_setting approval_text"><span class="ll_font">承認者</span></div>
                                        <div class="search_setting approval_text">
                                            <span class="right_10 ll_font">@{{ employees[currentAutocompleteId].presentation_id }}</span>
                                            <span class="right_30 ll_font">@{{ employees[currentAutocompleteId].name }}</span>
                                            <span class="arrow_font"><i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                                        </div>
                                        <div class="search_setting">
                                            <span class="right_10">従業員ID</span>
                                            <autocomplete :suggestions="employees" custom-class="s_size" :linked="true"
                                                :initial-id="newAutocompleteId"
                                                :current-id="newAutocompleteId"
                                                filtered-field-name="presentation_id"
                                                @selected="newEmployeeSelected"
                                            ></autocomplete>
                                        </div>
                                        <div class="search_setting">
                                            <span class="right_10">従業員名</span>
                                            <autocomplete :suggestions="employees" custom-class="m_size" :linked="true"
                                                :initial-id="newAutocompleteId"
                                                :current-id="newAutocompleteId"
                                                @selected="newEmployeeSelected"
                                            ></autocomplete>
                                        </div>
                                    </div>
                                </section>
                            </section>
                            <section class="btn">
                                <p class="button right_30"><a class="modal-close m_size l_height btn_greeen l_font" @click="moveRelationship">移行する</a></p>
                                <p class="button"><a class="modal-close m_size l_height btn_gray l_font" @click="closePopup">キャンセル</a></p>
                            </section>
                        </div>
                        <div class="modal-overlay" @click="closePopup"></div>
                    </div>
                </transition>
            </section>
        </div>
    </section>
     <section class="search_box_wrapper">
        <div class="search_box_innner2">
            <div>
                <form action="" @submit.prevent="">
                    <div class="search_setting">
                        <span class="right_10">従業員ID</span><input class="s_size" v-model="fields[0]" type="text">
                    </div>
                    <div class="search_setting">
                        <span class="right_10">従業員名</span><input class="m_size" v-model="fields[1]" type="text">
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10 left">雇用状態</span>
                        <div class="selectbox s_size left">
                            <select class="s_size" v-model="fields[2]">
                                <option value=""></option>
                                @foreach ($work_statuses as $key => $status)
                                    <option value="{{ $key }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">所属先</span><div class="selectbox mm_size side_input_block">
                        <select class="mm_size" v-model="fields[3]">
                            <option value=""></option>
                            @foreach ($work_locations as $key => $work_location)
                                <option value="{{ $key }}">{{ $work_location }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">部署</span>
                        <section class="form-group mm_size">
                            <select class="mm_size ms" multiple="multiple" v-model="fields[4]">
                                @foreach ($departments as $index => $department)
                                    <option value="{{ $index }}">{{ $department }}</option>
                                @endforeach
                            </select>
                        </section>
                    </div>
                    <div class="button bottom_10 search_setting right_10">
                        <button class="s_size s_height btn_greeen" @click="submit">検索</button>
                    </div>
                    <div class="button bottom_10 search_setting">
                        <a class="s_size s_height btn_gray" @click="resetConditions">リセット</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section class="approval_search_result">
        <paginator :total="subordinates.length" :current-page="currentPage" :per-page="perPage" @changed="changePage"></paginator>
        <section class="default_table">
            <table v-cloak class='table_with_fixed_header'>
                <tr class="fixed_header">
                    <th class="s_4"></th>
                    <th class="s_10">従業員ID</th>
                    <th class="s_14">従業員名</th>
                    <th class="s_8">所属先</th>
                    <th class="s_8">雇用状態</th>
                </tr>
                <tr v-for="(subordinate, key) in paginatedData">
                    <td>
                        <div class="check_onle_wrap">
                            <label class="checkbox_box"><input type="checkbox" @change="updateRelationship(key)" v-model="subordinate.attached"></label>
                        </div>
                    </td>
                    <td>@{{ subordinate.presentation_id }}</td>
                    <td>@{{ subordinate.name }}</td>
                    <td>@{{ subordinate.work_location }}</td>
                    <td>@{{ subordinate.work_status }}</td>
                </tr>
            </table>
        </section>
        <paginator :total="subordinates.length" :current-page="currentPage" :per-page="perPage" :sum-line="false" custom-class='last' @changed="changePage"></paginator>
    </section>
    <section class="btn">
        <p class="button"><a class="m_size l_height btn_gray l_font" href="{{ $return }}">戻る</a></p>
    </section>
</main>
@endsection