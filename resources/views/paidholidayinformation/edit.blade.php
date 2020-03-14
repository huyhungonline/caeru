@extends('layouts.master')

@section('title', '有給休暇詳細')

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
            <p class="breadcrumb"><span>勤怠管理</span><span>&emsp;&#62;&emsp;有給休暇詳細</span></p>
            <div class="title_wrapper">
                <h1>有給休暇詳細</h1>
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
        <section class="select_one bg_light_green bottom_10">
            <section class="select_one_inner">
                @if(isset($search_navi_previous))
                <section class="right_30 ico_position">
                    <a href="{{ $search_navi_previous }}">
                        <img class="ico_ico_arrow" src="{{ asset('images/ico_arrow_left1.svg') }}">
                    </a>
                </section>
                @endif
                <section class="right_30 text_bold">
                    <span class="right_30">{{ $employee->presentation_id }}</span><span class="right_30">{{ $employee->first_name . $employee->last_name }}</span><span>{{  $schedule_types[$employee->schedule_type] }}</span>
                </section>
                @if(isset($search_navi_next))
                <section class="ico_position">
                    <a href="{{ $search_navi_next }}">
                        <img class="ico_ico_arrow" src="{{ asset('images/ico_arrow_right1.svg') }}">
                    </a>
                </section>
                @endif
            </section>
        </section>
        <section class="setting_table bottom_30">
            <table>
                <tr>
                    <td class="input_items">従業員ID</td>
                    <td>{{ $employee->presentation_id }}</td>
                </tr>
                <tr>
                    <td class="input_items">従業員名</td>
                    <td>{{ $employee->first_name . $employee->last_name }}</td>
                </tr>
                <tr>
                    <td class="input_items">従業員名(カナ)</td>
                    <td>{{ $employee->first_name_furigana . $employee->last_name_furigana }}</td>
                </tr>
                <tr>
                    <td class="input_items">採用形態</td>
                    <td>{{ $employment_types[$employee->employment_type] }}</td>
                </tr>
                <tr>
                    <td class="input_items">有給更新日</td>
                    <td>{{ $employee->holidays_update_day }}</td>
                </tr>
                <tr>
                    <td class="input_items">部署</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="input_items">雇用状態</td>
                    <td>{{ $work_statuses[$employee->work_status] }}</td>
                </tr>
                <tr>
                    <td class="input_items">入社日</td>
                    <td>{{ $employee->joined_date }}</td>
                </tr>
                <tr>
                    <td class="input_items">勤続年数</td>
                    <td>{{ $getEmployeeJoinedDate }}</td>
                </tr>
                <tr>
                    <td class="input_items">週勤務日数</td>
                    <td>{{ $holiday_bonus_types[$employee->holiday_bonus_type] }}</td>
                </tr>
            </table>
        </section>
        <section>
            <form id="form" method="POST" form-single-submit action="{{ Caeru::route('update_paid_holiday',  [$employee->id, $page] ) }}">
                {{ csrf_field() }}
                <section>
                    <table>
                        <tr>
                            <th class="s_14">期間</th>
                            <th class="s_8">１日の労働時間</th>
                            <th class="s_6">出勤率</th>
                            <th class="s_6">付与日数</th>
                            <th class="s_10" colspan="2"><p>繰越日数</p><p>日:時間:分</p></th>
                            <th class="s_10" colspan="2"><p>取得日数</p><p>日:時間:分</p></th>
                            <th class="s_10" colspan="2"><p>残日数</p><p>日:時間:分</p></th>
                            <th class="s_18">備考</th>
                            <th class="s_8">操作年月日</th>
                            <th>最終変更者</th>
                        </tr>
                          <!--　所定が変更になっていない場合 -->
                        @foreach($paidholidayinformations as $paidholidayinformation)
                            @php
                                list($availableDay, $availableHour, $availableMinute) = $paidholidayinformation->getAvailablePaidHoliday();
                                list($consumedDay, $consumedHour, $consumedMinute) = $paidholidayinformation->getConsumedPaidHoliday();
                                list($carriedforwardDay, $carriedforwardHour, $carriedforwardMinute) = $paidholidayinformation->getCarriedForwardPaidHoliday();
                            @endphp
                            @if($loop->first)
                            <tr>
                                <td>
                                    {{ $paidholidayinformation->period_start ."~". $paidholidayinformation->period_end }}

                <input type="hidden" name="presentation_id" value="{{ $paidholidayinformation->id }}"> 
                                </td>
                                <td>{{ $paidholidayinformation->work_time_per_day }}時間</td>
                                <td>
                                    @component('layouts.form.error', ['field' => 'attendance_rate'])
                                        <input class="sss_size right_4" name="attendance_rate" value="{{ old('attendance_rate', $paidholidayinformation->attendance_rate) }}" type="text"><span>％</span>
                                    @endcomponent
                                        
                                </td>
                                <td>
                                    @component('layouts.form.error', ['field' => 'provided_paid_holidays'])
                                        <input class="sss_size" name="provided_paid_holidays" value="{{ old('provided_paid_holidays', $paidholidayinformation->provided_paid_holidays) }}" type="text">
                                    @endcomponent
                                </td>
                                <td>
                                    @component('layouts.form.error', ['field' => 'carried_forward_day'])
                                        <input class="ss_size" name="carried_forward_day" value="{{ old('carried_forward_day', $carriedforwardDay . '日') }}" type="text">
                                    @endcomponent
                                </td>
                                <td>
                                    @component('layouts.form.error', ['field' => 'carried_forward_time'])
                                        <input class="ss_size" name="carried_forward_time" value="{{ old('carried_forward_time', $carriedforwardHour .':'. $carriedforwardMinute) }}" type="text">
                                    @endcomponent
                                </td>
                                <td>{{ $consumedDay }}日</td>
                                <td>{{ $consumedHour}}:{{ $consumedMinute }}</td>
                                <td>{{ $availableDay }}日</td>
                                <td>{{ $availableHour}}:{{ $availableMinute }}</td>
                                 <td>
                                    @component('layouts.form.error', ['field' => 'note'])
                                        <input class="m_size" name="note" value="{{ old('note', $paidholidayinformation->note) }}" type="text">
                                    @endcomponent
                                </td>
                                <td>{{ $paidholidayinformation->last_modified_date }}</td>
                                <td>{{ $paidholidayinformation->manager->first_name }}</td>
                            </tr>
                            @else
                            <tr>
                                <td>{{ $paidholidayinformation->period_start ."~". $paidholidayinformation->period_end }}</td>
                                <td>{{ $paidholidayinformation->work_time_per_day }}時間</td>
                                <td>{{$paidholidayinformation->attendance_rate }}％</td>
                                <td>{{ $paidholidayinformation->provided_paid_holidays }}</td>
                                <td>{{ $carriedforwardDay }}日</td>
                                <td>{{ $carriedforwardHour}}:{{ $carriedforwardMinute }}</td>
                                <td>{{ $consumedDay }}日</td>
                                <td>{{ $consumedHour}}:{{ $consumedMinute }}</td>
                                <td>{{ $availableDay }}日</td>
                                <td>{{ $availableHour}}:{{ $availableMinute }}</td>
                                <td>{{ $paidholidayinformation->note }}</td>
                                <td>{{ $paidholidayinformation->last_modified_date }}</td>
                                <td>{{ $paidholidayinformation->manager->first_name }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </table>
                </section>
                <section class="btn">
                    <p class="button right_30"><button class="m_size l_height btn_greeen l_font" >保存</button></p>
                    <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ $employee ? Caeru::route('edit_paid_holiday', [$employee->id, $page]) : Caeru::route('edit_paid_holiday') }}">キャンセル</a></p>
                    <p class="button"><a class="m_size l_height btn_gray l_font" href="{{ $employee ? Caeru::route('paid_holiday_list', ['page' => $page]) : Caeru::route('paid_holiday_list') }}">一覧に戻る</a></p>
                </section>
            </form>
        </section>
    </main>
@endsection