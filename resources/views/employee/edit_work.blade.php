@extends('layouts.master')

@section('title', '従業員詳細')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
    <script defer src="{{ asset('/js/multiple-select.js') }}"></script>
    <script defer src="{{ asset('/js/pages/employee_edit_work_page.js') }}"></script>
    <script defer src="{{ asset('/js/components/employee_searcher.js') }}"></script>
@endpush

@section('content')
    <main id="basic">
        <section class="title">
            <p class="breadcrumb"><span>基本情報</span>&emsp;&#62;&emsp;<a href="{{ Caeru::route('employees_list', ['page' => $page]) }}">従業員一覧</a><span>&emsp;&#62;&emsp;従業員詳細</span></p>
            <div class="title_wrapper">
                <h1>従業員詳細</h1>
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
        @include('employee.search_box')
        @include('layouts.search_result_navigation')
        <section class="select_one2">
            <section class="tab">
                @can('view_employee_basic_info')
                    <p class="tab_button"><a class="tab_size tab_btn_gray left right_10" href="{{ Caeru::route('edit_employee', [$employee->id, $page]) }}">基本</a></p>
                @endcan
                <p class="tab_button"><a class="tab_size btn_yellow left" href="">勤怠</a></p>
            </section>
        </section>
        <section id="container">
            <form id="main" method="POST" form-single-submit action="{{ Caeru::route('update_employee_work', [$employee->id, $page]) }}">
                <section class="setting_table bottom_30">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <table>
                            <tr>
                                <td class="input_items">ICカード登録用番号</td>
                                <td>{{ $employee->card_registration_number }}</td>
                            </tr>
                            <tr>
                                <td class="input_items">カード番号</td>
                                <td>
                                    <span class="right_30 left">{{ $employee->card_number }}</span>
                                    @if (isset($employee->card_number) && Auth::user()->can('change_employee_work_info'))
                                        <section class="check_box_wrap">
                                            @include('layouts.form.checkbox_field', ['field' => 'delete_card', 'label' => 'カード削除', 'object' => null, 'default' => 0])
                                        </section>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="input_items">有給休暇</td>
                                <td>
                                    @can('change_employee_work_info')
                                        <section class="second">
                                            <section class="check_box_wrap right_30 side_input_block">
                                                @include('layouts.form.checkbox_field', ['field' => 'paid_holiday_exception', 'label' => '有給対象外', 'object' => $employee, 'default' => 0])
                                            </section>
                                            <span class="right_10">更新日</span>
                                            @component('layouts.form.error', ['field' => 'holidays_update_day'])
                                                <input class="ss_size right_30" name="holidays_update_day" type="text" value="{{ old('holidays_update_day', $employee->holidays_update_day) }}">
                                            @endcomponent
                                        </section>
                                        <section class="second dash_line">
                                            <section class="right_30 side_input_block">
                                                <span class="right_10">現在の１日の労働時間</span>
                                                @component('layouts.form.error', ['field' => 'work_time_per_day'])
                                                    <input class="ss_size right_4" name="work_time_per_day" type="text" value="{{ old('work_time_per_day', $employee->work_time_per_day) }}"><span>時間</span>
                                                @endcomponent
                                            </section>
                                            <section class="right_30 side_input_block">
                                                @component('layouts.form.error', ['field' => 'work_time_change_date'])
                                                    <input class="s_size right_4" name="work_time_change_date" type="text" value="{{ old('work_time_change_date') }}">
                                                @endcomponent
                                                <span class="right_10">から１日の労働時間を</span>
                                                @component('layouts.form.error', ['field' => 'work_time_change_to'])
                                                    <input class="ss_size right_10" name="work_time_change_to" type="text" value="{{ old('work_time_change_to') }}">
                                                @endcomponent
                                                <span>時間に変更</span>
                                            </section>
                                        </section>
                                        <section class="second dash_line">
                                            <div class="side_input_block">
                                                @component('layouts.form.error', ['field' => 'holiday_bonus_type_extra'])
                                                    <label class="radio_text right_30">
                                                        <input autocomplete="off" name="holiday_bonus_type_extra" type="radio" value="1"
                                                            {{ old('holiday_bonus_type_extra', ($employee->holiday_bonus_type == $normal_type_value) ? 1 : 0) == 1 ? 'checked':'' }}
                                                        >一般
                                                    </label>
                                                    <label class="radio_text right_10">
                                                        <input autocomplete="off" name="holiday_bonus_type_extra" type="radio" value="0"
                                                            {{ old('holiday_bonus_type_extra', ($employee->holiday_bonus_type == $normal_type_value) ? 1 : 0) == 0 ? 'checked':'' }}
                                                        >短時間労働者
                                                    </label>
                                                @endcomponent
                                                @component('layouts.form.error', ['field' => 'holiday_bonus_type'])
                                                    @include('layouts.form.nullable_select_field', ['field' => 'holiday_bonus_type', 'class' => 's_size', 'default' => $employee->holiday_bonus_type, 'items' => $holiday_bonus_types, 'multiple' => false])
                                                @endcomponent
                                            </div>
                                        </section>
                                    @else
                                        @if (isset($employee->holidays_update_day))
                                            <span class="right_10">更新日</span><span class="right_30">{{ $employee->holidays_update_day }}</span>
                                        @endif
                                        @if (isset($employee->work_time_per_day))
                                            <span class="right_10">１日の労働時間</span><span class="right_30">{{ $employee->work_time_per_day }}時間</span>
                                        @endif
                                        @if (isset($employee->holiday_bonus_type))
                                            <span class="right_10">短時間労働者</span><span>{{ ($employee->holiday_bonus_type == $normal_type_value) ? '一般' : $holiday_bonus_types[$employee->holiday_bonus_type] }}</span>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        </table>
                </section>
                <section class="btn">
                    @can('change_employee_work_info')
                        <p class="button right_30"><button class="m_size l_height btn_greeen l_font">設定を保存</button></p>
                        <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('edit_employee_work', [$employee->id, $page]) }}">キャンセル</a></p>
                    @endcan
                    <p class="button"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('employees_list', ['page' => $page]) }}">一覧に戻る</a></p>
                </section>
            </form>
            <section class="select_one2">
                <section class="right_position">
                    @can('change_employee_work_info')
                        <p class="button"><a class="s_size s_height btn_blue" @click="addSchedule">追加</a></p>
                    @endcan
                </section>
            </section>
            <section class="work_plan">
                <table>
                    <th colspan="2" class="normal_height">勤務設定</th>
                </table>
                <work-schedule :presentation-data="presentation_data" v-for="(model, index) in model_data" :key="model.id" :model-data="model" :employee-id="{{ $employee->id }}" @remove="removeSchedule(index)" @copy="copySchedule"></work-schedule>
            </section>
            <section class="btn">
                @can('change_employee_work_info')
                    <p class="button right_30"><button class="mm_size l_height btn_greeen l_font" @click="submitMainForm">スケジュールを保存</button></p>
                    <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('edit_employee_work', [$employee->id, $page]) }}">キャンセル</a></p>
                @endcan
                <p class="button"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('employees_list', ['page' => $page]) }}">一覧に戻る</a></p>
            </section>
        </section>
    </main>
@endsection