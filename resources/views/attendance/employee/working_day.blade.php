@extends('layouts.master')

@section('title', '勤怠データ詳細')

@section('header')
    @include('layouts.header', [ 'active' => 3 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/pages/employee_working_day.js') }}"></script>
@endpush

@section('content')
    <main id="attendance_detail">
        <section class="title">
            <p class="breadcrumb"><span>勤怠管理</span>&emsp;&#62;&emsp;<a href="attendance_management.html">勤怠データ管理</a><span>&emsp;&#62;&emsp;勤怠データ詳細</span></p>
            <div class="title_wrapper">
                <h1>勤怠データ詳細</h1>
                <section class="select_one">
                    <section class="select_one_inner">
                        <section class="right_30 ico_position">
                            <a :href="previousDay()"><img class="ico_ico_arrow" src="{{ asset('images/ico_arrow_left1.svg') }}"></a>
                        </section>
                        <section class="right_10 ll_font" v-cloak>@{{ formatDate(currentDate) }}
                            <section class="right_10 ico_position"><a class="modal-open" @click="toggleDatePicker"><img class="ico_ico_arrow" src="{{ asset('images/ico_calendar.svg') }}"></a></section>
                        </section>
                        <section class=" ico_position"> 
                            <a :href="nextDay()"><img class="ico_ico_arrow" src="{{ asset('images/ico_arrow_right1.svg') }}"></a>
                        </section>
                    </section>
                    <section class="caeru_date_picker_wrapper" v-show="showDatePicker">
                        <calendar class="normal_date_picker" v-bind="datePickerOptions" :editable="false"
                            @change-current-time="datePickerChangeTime"
                            @date-picker-cancel="toggleDatePicker"
                            @day-chose="goToThisDay">
                        </calendar>
                        <div class="modal-overlay" v-cloak @click="toggleDatePicker"></div>
                    </section>
                </section>
                <section class="right_position2">
                <table class="close_btn_inner right">
                    <tr>
                        <td class="text_bold bg_gray">管理者１</td>
                        <td><p class="button"><a class="ss_size s_height btn_white" href="#">締める</a></p></td>
                        <td class="text_bold bg_gray">管理者2</td>
                        <td><img class="check_red ico_ico_arrow3" src="{{ asset('images/ico_check_red.svg') }}"></td>
                    </tr>
                </table>
                </section>
            </div>
        </section>
        <section class="select_one bg_light_green">
            <section class="select_one_inner">
                <section class="right_30 text_bold" v-cloak>
                    <span class="right_30">@{{ currentEmployee.presentation_id }}</span><span class="right_30">@{{ currentEmployee.name }}</span><span>@{{ currentEmployee.schedule_type }}</span>
                </section>
            </section>
        </section>
        <section class="select_one4 bottom_0">
            <section class="left">
                <div class="left right_10 description_confirm_list">要確認</div>
            </section>
            <section class="right">
                <p class="button" v-if="canChange"><a class="m_size s_height btn_blue" @click="createNewWorkingInfo">勤務追加</a></p>
                <p class="button left_10"><a class="m_size s_height btn_gray modal-open" data-target="con1" href="#">申請内容確認</a></p>
            </section>
        </section>
        <!-- working-info components -->
        <working-info v-for="info in workingInfos" :key="info.id" :root-data="info" :today="currentDate" :timezone="timezone" :can-change="canChange"
            :employee-id="currentEmployee.id"
            :work-locations-list="workLocations"
            :work-addresses-list="timestampPlaces"
            :working-day-id="workingDayInstanceId"
            :schedule-transfer-data="extractScheduleTransferData(info.id)"
            :alert-setting-data="extractAlertSettingData(info.id)"
            @delete-working-info="removeWorkingInfo"
            @working-info-saved="workingInfoSaved"
            @schedule-transfer="scheduleTransfer">
        </working-info>
        <!--  -->
        <section class="select_one2">
            <section class="right_position">
                <p class="button add_btn" v-if="canChange"><a class="s_size s_height btn_blue" @click="createTimestamp()">追加</a></p>
            </section>
        </section>
        <section class="default_table">
            <table>
                <tr>
                    <th class="sss_size"></th>
                    <th class="s_size">登録日時</th>
                    <th class="s_size">打刻日</th>
                    <th class="s_size">打刻時刻</th>
                    <th class="s_size">登録ユーザ</th>
                    <th class="m_size">勤務先</th>
                    <th class="s_size">勤怠種類</th>
                    <th class="sss_size"></th>
                </tr>
                <!-- The form for newTimestamp -->
                <tr id="" v-cloak v-if="showTimestampForm">
                    <td>
                        <div class="check_onle_wrap">
                            <error-display :message="timestampFormErrors.enable">
                                <label class="checkbox_box"><input type="checkbox" :true-value='true' :false-value='false' v-model="newTimestamp.enable"></label>
                            </error-display>
                        </div>
                    </td>
                    <td></td>
                    <td>
                        <div class="selectbox">
                            <error-display :message="timestampFormErrors.processed_date_value">
                                <select class="m_size" v-model="newTimestamp.processed_date_value">
                                    <option :value="currentDate">@{{ currentDate }}</option>
                                    <option :value="tomorrow()">@{{ tomorrow() }}</option>
                                </select>
                            </error-display>
                        </div>
                    </td>
                    <td>
                        <error-display :message="timestampFormErrors.processed_time_value">
                            <input id="newTimestamp" class="ss_size" :value="newTimestamp.processed_time_value" type="text"
                                @keydown.enter="insertDateToNewTimestamp()"
                                @blur="insertDateToNewTimestamp()"
                            >
                        </error-display>
                    </td>
                    <td></td>
                    <td>
                        <error-display :message="timestampFormErrors.work_location_id">
                            <autocomplete custom-class="mm_size" :suggestions="timestampPlaces" :allow-null="false" @selected="placeSelected"></autocomplete>
                        </error-display>
                    </td>
                    <td>
                        <div class="selectbox">
                            <error-display :message="timestampFormErrors.timestamped_type">
                                <select class="ss_size" v-model.number="newTimestamp.timestamped_type">
                                    <option v-for="(type, key) in timestampTypes" :value="key">@{{ type }}</option>
                                </select>
                            </error-display>
                        </div>
                    </td>
                    <td>
                        <p class="button"><a class="sss_size ss_height btn_greeen" @click="sendNewTimestamp">保存</a></p>
                    </td>
                </tr>
                <!-- end newTimestamp form -->
                <tr v-cloak class="bg_light_blue" v-for="(timestamp, key) in timestamps">
                    <td>
                        <div class="check_onle_wrap">
                            <label class="checkbox_box"><input :disabled="!canChange" v-model="timestamp.enable" type="checkbox" @change="toggleStatusTimestamp(key)"></label>
                        </div>
                    </td>
                    <td>@{{ timestamp.created_at }}</td>
                    <td>@{{ timestamp.processed_date_value }}</td>
                    <td>@{{ timestamp.processed_time_value }}</td>
                    <td>@{{ timestamp.registerer_name }}</td>
                    <td>@{{ timestamp.place_name }}</td>
                    <td>@{{ timestampTypes[timestamp.timestamped_type] }}</td>
                    <td></td>
                </tr>
                <!-- <tr>
                    <td>
                        <div class="check_onle_wrap">
                            <label class="checkbox_box"><input name="hugahuga" type="checkbox"></label>
                        </div>
                    </td>
                    <td>2016/03/01&nbsp;&nbsp;09:30</td>
                    <td>2016/03/01</td>
                    <td>09:30</td>
                    <td>タブレット</td>
                    <td>広島本社</td>
                    <td>出勤</td>
                    <td>
                        <a class="modal-open" data-target="con1"><img class="ico_balloon" src="{{ asset('images/map_balloon.svg') }}"></a>
                    </td>
                </tr> -->
            </table>
        </section>
    </main>
@endsection