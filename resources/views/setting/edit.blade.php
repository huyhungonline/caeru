@extends('layouts.master')

@section('title', '設定詳細')

@section('header')
@include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
@endpush

@section('content')
<main id="basic">
    <section class="title bottom_10">
        <p class="breadcrumb"><span>基本情報</span><span>&emsp;&#62;&emsp;設定詳細</span></p>
        <div class="title_wrapper">
            <h1>設定詳細</h1>
            <div class="worklocation">
                <div class="worklocation_inner">
                    <span class="right_10">{{ $current_work_location }}</span>
                    @if (isset($picker_list))
                        <p class="button"><a class="modal-open ss_size s_height btn_gray" @click="open">変更</a></p>
                    @endif
                </div>
                @include('layouts.work_location_picker', ['list' => $picker_list, 'target' => 'edit_setting', 'singular' => true, 'restricted' => !Auth::user()->company->initial_setting_completed ])
            </div>
        </div>
    </section>
    <section class="select_one2">
        <section class="tab">
            <p class="tab_button"><a class="tab_size tab_size btn_yellow left right_10" href="#">勤怠</a></p>
        </section>
    </section>

    <form method="POST" form-single-submit action="{{ Caeru::route('update_setting') }}">
    {{ csrf_field() }}
        <section class="setting_table">
            <table>
                <tr>
                    <td class="input_items">@can('change_setting_info')<span class="required">必須</span>@endcan タイムゾーン</td>
                    <td>
                        @can('change_setting_info')
                            <div class="selectbox">
                                @component('layouts.form.error', ['field' => 'timezone'])
                                    @include('layouts.form.select_field', ['field' => 'timezone', 'class' => 'l_size', 'default' => $setting->timezone, 'items' => $data_time_zones, 'multiple' => false])
                                @endcomponent
                            </div>
                        @else
                            {{ $data_time_zones[$setting->timezone] }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_setting_info')<span class="required">必須</span>@endcan 給与計算締日</td>
                    <td>
                        @can('change_setting_info')
                            <span class="right_10">毎月</span>
                            @component('layouts.form.error', ['field' => 'salary_accounting_day'])
                                <input class="right_10 s_size" name="salary_accounting_day" value="{{ old('salary_accounting_day',$setting->salary_accounting_day) }}" type="text">
                            @endcomponent
                            <span class="right_30">日</span><span class="light_gray">※末日の場合は0と入力</span>
                        @else
                            @if ($setting->salary_accounting_day == "0")
                                {{ '末日' }}
                            @else
                                <span class="right_10">毎月{{$setting->salary_accounting_day }}日</span>
                            @endif
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_setting_info')<span class="required">必須</span>@endcan 給与支払い日</td>
                    <td>
                        @can('change_setting_info')
                            <div class="selectbox right_10 side_input_block">
                                @component('layouts.form.error', ['field' => 'pay_month'])
                                    @include('layouts.form.select_field', ['field' => 'pay_month', 'class' => 's_size', 'default' => $setting->pay_month, 'items' => $data_pay_month, 'multiple' => false])
                                @endcomponent
                            </div>
                            @component('layouts.form.error', ['field' => 'pay_day'])
                                <input class="s_size right_4 side_input_block" name="pay_day" value="{{ old('pay_day',$setting->pay_day) }}" type="text">
                            @endcomponent
                            <span class="right_30 side_input_block">日</span><span class="light_gray side_input_block">※末日の場合は0と入力</span>
                        @else
                            @if ($setting->salary_accounting_day == "0")
                                {{ $data_pay_month[$setting->pay_month].'末日' }}
                            @else
                                <span class="right_10">{{ $data_pay_month[$setting->pay_month].$setting->pay_day }}</span>
                            @endif
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_setting_info')<span class="required">必須</span>@endcan 週起算日</td>
                    <td>
                        @can('change_setting_info')
                            <div class="selectbox side_input_block right_4">
                                @component('layouts.form.error', ['field' => 'start_day_of_week'])
                                @include('layouts.form.select_field', ['field' => 'start_day_of_week', 'class' => 's_size', 'default' => $setting->start_day_of_week, 'items' => $data_day_of_week, 'multiple' => false])
                                @endcomponent
                            </div>
                        @else
                            {{ $data_day_of_week[$setting->start_day_of_week] }}
                        @endcan
                        
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_setting_info')<span class="required">必須</span>@endcan 法定休日</td>
                    <td>
                        @can('change_setting_info')
                            <div class="selectbox side_input_block right_4">
                                @include('layouts.form.radio_field', ['field' => 'law_rest_day_mode', 'object' => $setting, 'options' => $law_rest_day_modes, 'default' => 1 ])
                            </div>
                        @else
                            {{ $law_rest_day_modes[$setting->law_rest_day_mode] }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_setting_info')<span class="required">必須</span>@endcan まるめ設定</td>
                    <td>
                        <span class="right_10">出勤時刻単位</span>
                        @can('change_setting_info')
                            @component('layouts.form.error', ['field' => 'start_time_round_up'])
                                <input class="ss_size right_4" name="start_time_round_up" value="{{ old('start_time_round_up',$setting->start_time_round_up) }}" type="text">
                            @endcomponent
                        @else
                            {{ $setting->start_time_round_up }}
                        @endcan
                        <span class="right_30">分</span> <span class="right_10">退勤時刻単位</span>
                        @can('change_setting_info')
                            @component('layouts.form.error', ['field' => 'end_time_round_down'])
                                <input class="ss_size right_4" name="end_time_round_down" value="{{ old('end_time_round_down',$setting->end_time_round_down) }}" type="text">
                            @endcomponent
                        @else
                            {{ $setting->end_time_round_down }}
                        @endcan
                            <span class="right_30">分</span><span class="right_10">外出時間</span>
                        @can('change_setting_info')
                            @component('layouts.form.error', ['field' => 'break_time_round_up'])
                                <input class="ss_size right_4" name="break_time_round_up" value="{{ old('break_time_round_up',$setting->break_time_round_up) }}" type="text">
                            @endcomponent
                        @else
                            {{ $setting->break_time_round_up }}
                        @endcan
                        <span class="right_30">分</span>
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_setting_info')<span class="required">必須</span>@endcan 予定時刻外</td>
                    <td>
                        <span class="right_10">出勤予定時刻より</span>
                        @can('change_setting_info')
                            @component('layouts.form.error', ['field' => 'start_time_diff_limit'])
                                <input class="ss_size right_4" name="start_time_diff_limit" value="{{ old('start_time_diff_limit',$setting->start_time_diff_limit) }}" type="text">
                            @endcomponent
                        @else
                            {{ $setting->start_time_diff_limit }}
                        @endcan
                        <span class="right_30">分以上差がある時にアラート</span><span class="right_10">退勤予定時刻より</span>
                        @can('change_setting_info')
                            @component('layouts.form.error', ['field' => 'end_time_diff_limit'])
                                <input class="ss_size right_4" name="end_time_diff_limit" value="{{ old('end_time_diff_limit',$setting->end_time_diff_limit) }}" type="text">
                            @endcomponent
                        @else
                            {{ $setting->end_time_diff_limit }}
                        @endcan
                        <span class="right_30">分以上差がある時にアラート</span>
                    </td>
                </tr>
                <tr>
                    <td class="input_items">外出ボタン</td>
                    <td>
                        @can('change_setting_info')
                            @component('layouts.form.error', ['field' => 'go_out_button_usage'])
                                <label class="radio_text right_30">
                                <input name="go_out_button_usage" type="radio" value={{$not_use_go_out}} {{ $setting->go_out_button_usage == $not_use_go_out ? 'checked' : '' }}>
                                利用しない</label>
                                <span>利用する(休憩時間として打刻</span>
                                <label class="radio_text right_10">
                                <input name="go_out_button_usage" type="radio" value={{$use_as_break_time}} {{ $setting->go_out_button_usage == $use_as_break_time ? 'checked' : '' }}>
                                する</label>
                                <label class="radio_text">
                                <input name="go_out_button_usage" type="radio" value={{$use_go_out}} {{ $setting->go_out_button_usage == $use_go_out ? 'checked' : '' }}>
                                しない</label>
                                )
                             @endcomponent
                        @else
                            @if ($setting->go_out_button_usage == $not_use_go_out)
                                <span>{{ '利用しない' }}</span>
                            @else
                                <span>利用する（休憩時間として打刻{{ $setting->go_out_button_usage == $use_as_break_time ? 'する' : 'しない' }} ）</span>
                            @endif
                        @endcan

                        <span class="left_30">勤怠データ管理画面に表示</span>
                        @can('change_setting_info')
                            @include('layouts.form.radio_field', ['field' => 'display_go_out_time', 'object' => $setting, 'options' => [1 => 'する', 0 => 'しない'], 'default' => 0 ])
                        @else
                            <span>{{ $setting->display_go_out_time == 1 ? 'する' : 'しない' }}</span>
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">残業ボタン</td>
                    <td>
                        @can('change_setting_info')
                            @include('layouts.form.radio_field', ['field' => 'use_overtime_button', 'object' => $setting, 'options' => [1 => '利用する', 0 => '利用しない'], 'default' => 0 ])
                        @else
                            <span>{{ $setting->use_overtime_button == 1 ? '利用する' : '利用しない' }}</span>
                        @endcan
                    </td>
                </tr>
            </table>
        </section>
        <section class="setting_table secound_block">
            <h2>有給休暇設定</h2>
            <table>
                @can('change_setting_info')
                    <tr>
                        <td class="input_items">入社有給</td>
                        <td class="s_18" rowspan="2">
                            @component('layouts.form.error', ['field' => 'paid_holiday_after_joined_period'])
                                <input class="ss_size" name="paid_holiday_after_joined_period" value="{{ old('paid_holiday_after_joined_period',$setting->paid_holiday_after_joined_period) }}" type="text">
                            @endcomponent
                            <span class="right_10">ヶ月後</span>
                        </td>
                        <td>
                            <section class="second">
                                @component('layouts.form.error', ['field' => 'paid_holiday_first_time_normal_type'])
                                    <input class="ss_size right_4" name="paid_holiday_first_time_normal_type" value="{{ old('paid_holiday_first_time_normal_type',$setting->paid_holiday_first_time_normal_type) }}" type="text">
                                @endcomponent
                                <span class="right_10">日</span><span>(一般)</span>
                            </section>
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">入社有給(短時間労働者)</td>
                        <td>
                            <section class="second">
                                @component('layouts.form.error', ['field' => 'paid_holiday_first_time_4wdpw_type'])
                                    <input class="ss_size right_4" name="paid_holiday_first_time_4wdpw_type" value="{{ old('paid_holiday_first_time_4wdpw_type',$setting->paid_holiday_first_time_4wdpw_type) }}" type="text">
                                @endcomponent
                                <span class="right_10">日</span><span>(週4日勤務)</span>
                            </section>
                            <section class="second dash_line">
                                @component('layouts.form.error', ['field' => 'paid_holiday_first_time_3wdpw_type'])
                                    <input class="ss_size right_4" name="paid_holiday_first_time_3wdpw_type" value="{{ old('paid_holiday_first_time_3wdpw_type',$setting->paid_holiday_first_time_3wdpw_type) }}" type="text">
                                @endcomponent
                                <span class="right_10">日</span><span>(週3日勤務)</span>
                            </section>
                            <section class="second dash_line">
                                @component('layouts.form.error', ['field' => 'paid_holiday_first_time_2wdpw_type'])
                                    <input class="ss_size right_4" name="paid_holiday_first_time_2wdpw_type" value="{{ old('paid_holiday_first_time_2wdpw_type',$setting->paid_holiday_first_time_2wdpw_type) }}" type="text">
                                @endcomponent
                                <span class="right_10">日</span><span>(週2日勤務)</span>
                            </section>
                            <section class="second dash_line">
                                @component('layouts.form.error', ['field' => 'paid_holiday_first_time_1wdpw_type'])
                                    <input class="ss_size right_4" name="paid_holiday_first_time_1wdpw_type" value="{{ old('paid_holiday_first_time_1wdpw_type',$setting->paid_holiday_first_time_1wdpw_type) }}" type="text">
                                @endcomponent
                                <span class="right_10">日</span><span>(週1日勤務)</span>
                            </section>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td class="input_items">入社有給</td>
                        <td>
                            <span class="right_30">入社後{{ $setting->paid_holiday_after_joined_period }}ヶ月後</span>
                            <span class="right_10">{{ $setting->paid_holiday_first_time_normal_type }}日(一般)</span>
                            <span class="right_10">{{ $setting->paid_holiday_first_time_4wdpw_type }}日(週4日勤務)</span>
                            <span class="right_10">{{ $setting->paid_holiday_first_time_3wdpw_type }}日(週3日勤務)</span>
                            <span class="right_10">{{ $setting->paid_holiday_first_time_2wdpw_type }}日(週2日勤務)</span>
                            <span class="right_10">{{ $setting->paid_holiday_first_time_1wdpw_type }}日(週1日勤務)</span>
                        </td>
                    </tr>
                @endcan
                @can('change_setting_info')
                    <tr>
                        <td class="input_items">年別有給日</td>
                        <td colspan="2">
                            <section class="second">
                                @component('layouts.form.error', ['field' => 'paid_holiday_increase_rate_normal_type'])
                                    <input class="mm_size" name="paid_holiday_increase_rate_normal_type" value="{{ old('paid_holiday_increase_rate_normal_type',$setting->paid_holiday_increase_rate_normal_type) }}" type="text">
                                @endcomponent
                                <span class="left_10">(一般)</span>
                            </section>
                        </td>                
                    </tr>
                    <tr>
                        <td class="input_items">年別有給日(短時間労働者)</td>
                        <td colspan="2">
                            <section class="second">
                                @component('layouts.form.error', ['field' => 'paid_holiday_increase_rate_4wdpw_type'])
                                    <input class="mm_size" name="paid_holiday_increase_rate_4wdpw_type" value="{{ old('paid_holiday_increase_rate_4wdpw_type',$setting->paid_holiday_increase_rate_4wdpw_type) }}" type="text">
                                @endcomponent
                                <span class="left_10">(週4日勤務)</span>
                            </section>
                            <section class="second dash_line">
                                @component('layouts.form.error', ['field' => 'paid_holiday_increase_rate_3wdpw_type'])
                                    <input class="mm_size" name="paid_holiday_increase_rate_3wdpw_type" value="{{ old('paid_holiday_increase_rate_3wdpw_type',$setting->paid_holiday_increase_rate_3wdpw_type) }}" type="text">
                                @endcomponent
                                <span class="left_10">(週3日勤務)</span>
                            </section>
                            <section class="second dash_line">
                                @component('layouts.form.error', ['field' => 'paid_holiday_increase_rate_2wdpw_type'])
                                    <input class="mm_size" name="paid_holiday_increase_rate_2wdpw_type" value="{{ old('paid_holiday_increase_rate_2wdpw_type',$setting->paid_holiday_increase_rate_2wdpw_type) }}" type="text">
                                @endcomponent
                                <span class="left_10">(週2日勤務)</span>
                            </section>
                            <section class="second dash_line">
                                @component('layouts.form.error', ['field' => 'paid_holiday_increase_rate_1wdpw_type'])
                                    <input class="mm_size" name="paid_holiday_increase_rate_1wdpw_type" value="{{ old('paid_holiday_increase_rate_1wdpw_type',$setting->paid_holiday_increase_rate_1wdpw_type) }}" type="text">
                                @endcomponent
                                <span class="left_10">(週1日勤務)</span>
                            </section>
                        </td>
                    </tr>
                    @else
                        <tr>
                            <td class="input_items">年別有給日</td>
                            <td>
                                <span class="right_6">{{ $setting->paid_holiday_increase_rate_normal_type }}</span><span class="right_30">(一般)</span>
                                <span class="right_6">{{ $setting->paid_holiday_increase_rate_4wdpw_type }}</span><span class="right_30">(週4日勤務)</span>
                                <span class="right_6">{{ $setting->paid_holiday_increase_rate_3wdpw_type }}</span><span class="right_30">(週3日勤務)</span>
                                <span class="right_6">{{ $setting->paid_holiday_increase_rate_2wdpw_type }}</span><span class="right_30">(週2日勤務)</span>
                                <span>{{ $setting->paid_holiday_increase_rate_1wdpw_type }}</span><span class="right_10">(週1日勤務)</span>
                            </td>
                        </tr>
                    @endcan
            </table>
        </section>
        <section class="btn">
            <p class="button right_30"><button class="m_size l_height btn_greeen l_font" >{{ (Session::get('current_work_location') == "all") ? "勤務地に反映" : "保存"  }}</button></p>
            <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('edit_setting') }}">キャンセル</a></p>
        </section>
    </form>
</main>
@endsection