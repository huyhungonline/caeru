<template>
    <section>
        <section class="pager bottom_10">
            <section class="select_one_inner l_font v_10">
                <span class="right_10">所定</span>
                <span class="right_10">出勤</span>
                <!-- schedule_start_work_time -->
                <span v-if="workAddressMode || !canChange" class="right_10">{{ computedScheduleStartWorkTime | reformatTime | ensureSpace }}</span>
                <template v-else>
                    <input id="schedule_start_work_time" class="ss_size" type="text"
                        :value="computedScheduleStartWorkTime | reformatTime"
                        v-if="changingSchedule['schedule_start_work_time']"
                        @keydown.enter="applySchedule($event, 'computedScheduleStartWorkTime', 'schedule_start_work_time')"
                        @blur="applySchedule($event, 'computedScheduleStartWorkTime', 'schedule_start_work_time')"
                    />
                    <span class="right_10 box_add" @click="changeSchedule('schedule_start_work_time')" v-else
                        :class="{'text_green': changed['schedule_start_work_time'] !== null}"
                    >
                        {{ computedScheduleStartWorkTime | reformatTime | ensureSpace }}
                    </span>
                </template>
                <!-- end -->
                <span class="right_10">〜</span>
                <span class="right_10">退勤</span>
                <!-- schedule_end_work_time -->
                <span v-if="workAddressMode || !canChange" class="right_10">{{ computedScheduleEndWorkTime | reformatTime | ensureSpace }}</span>
                <template v-else>
                    <input id="schedule_end_work_time" class="ss_size" type="text"
                        :value="computedScheduleEndWorkTime | reformatTime"
                        v-if="changingSchedule['schedule_end_work_time']"
                        @keydown.enter="applySchedule($event, 'computedScheduleEndWorkTime', 'schedule_end_work_time')"
                        @blur="applySchedule($event, 'computedScheduleEndWorkTime', 'schedule_end_work_time')"
                    />
                    <span class="right_10 box_add" @click="changeSchedule('schedule_end_work_time')" v-else
                        :class="{'text_green': changed['schedule_end_work_time'] !== null}"
                    >
                        {{ computedScheduleEndWorkTime | reformatTime | ensureSpace }}
                    </span>
                </template>
                <!-- end -->
                <span class="right_10">休憩</span>
                <!-- schedule_break_time -->
                <span v-if="workAddressMode || !canChange" class="right_10">{{ computedScheduleBreakTime | ensureSpace }}</span>
                <template v-else>
                    <input id="schedule_break_time" class="ss_size" type="text"
                        :value="computedScheduleBreakTime"
                        v-if="changingSchedule['schedule_break_time']"
                        @keydown.enter="applySchedule($event, 'computedScheduleBreakTime', 'schedule_break_time')"
                        @blur="applySchedule($event, 'computedScheduleBreakTime', 'schedule_break_time')"
                    />
                    <span class="right_10 box_add" @click="changeSchedule('schedule_break_time')" v-else
                        :class="{'text_green': changed['schedule_break_time'] !== null}"
                    >
                        {{ computedScheduleBreakTime | ensureSpace }}
                    </span>
                </template>
                <!-- end -->
                <span class="right_10 ">（内）深休</span>
                <!-- schedule_night_break_time -->
                <span v-if="workAddressMode || !canChange" class="right_10">{{ computedScheduleNightBreakTime | ensureSpace }}</span>
                <template v-else>
                    <input id="schedule_night_break_time" class="ss_size" type="text"
                        :value="computedScheduleNightBreakTime"
                        v-if="changingSchedule['schedule_night_break_time']"
                        @keydown.enter="applySchedule($event, 'computedScheduleNightBreakTime', 'schedule_night_break_time')"
                        @blur="applySchedule($event, 'computedScheduleNightBreakTime', 'schedule_night_break_time')"
                    />
                    <span class="right_10 box_add" @click="changeSchedule('schedule_night_break_time')" v-else
                        :class="{'text_green': changed['schedule_night_break_time'] !== null}"
                    >
                        {{ computedScheduleNightBreakTime | ensureSpace }}
                    </span>
                </template>
                <!-- end -->
                <span class="right_10">=</span>
                <!-- schedule_working_hour -->
                <span v-if="workAddressMode || !canChange" class="right_10">{{ computedScheduleWorkingHour | reformatTime | ensureSpace }}</span>
                <template v-else>
                    <input id="schedule_working_hour" class="ss_size" type="text"
                        :value="computedScheduleWorkingHour | reformatTime"
                        v-if="changingSchedule['schedule_working_hour']"
                        @keydown.enter="applySchedule($event, 'computedScheduleWorkingHour', 'schedule_working_hour')"
                        @blur="applySchedule($event, 'computedScheduleWorkingHour', 'schedule_working_hour')"
                    />
                    <span class="right_10 box_add" @click="changeSchedule('schedule_working_hour')" v-else
                        :class="{'text_green': changed['schedule_working_hour'] !== null}"
                    >
                        {{ computedScheduleWorkingHour | reformatTime | ensureSpace }}
                    </span>
                </template>
                <!-- end -->
                <section class="transfer_wrap">
                    <div class="caeru_date_picker_wrapper"  v-show="showTranferPicker">
                        <calendar :id="rootData.id" class="schedule_transfer" v-if="transferPickerOptions !== null" v-bind="transferPickerOptions" :editable="false" :start-date="scheduleTransferData['start_date']" :end-date="scheduleTransferData['end_date']"
                            @date-picker-cancel="toggleTransferPicker()"
                            @schedule-transfer="scheduleTransfer"
                            @change-current-transfer-time="changeCurrentTransferTime">
                        </calendar>
                        <div class="modal-overlay" @click="toggleTransferPicker()"></div>
                    </div>
                    <p class="button" v-if="scheduleTransferData !== null  &&  canChange"><button class="ss_size s_height btn_gray transfer_btn right_10" @click="toggleTransferPicker()">振替</button>
                    </p>
                </section>
                <p class="button" v-if="showDeleteButton  &&  canChange"><a class="ss_size s_height btn_black modal-open" @click="deleteThisWorkingInfo()">削除</a></p>
            </section>
        </section>
        <section class="detail_table">
            <table class="left first">
                <tr>
                    <th class="s_6" rowspan="3"></th>
                    <th class="s_6" rowspan="3">勤務形態</th>
                    <th class="s_6" rowspan="3">休日形態</th>
                    <th class="s_12" rowspan="3">時間休暇</th>
                    <th class="s_20" rowspan="3">
                        <template v-if="!workAddressMode" >勤務先</template>
                        <p class="button" v-else><a class="ss_size s_height btn_gray left_10" href="#">訪問</a></p>
                    </th>
                    <th colspan="2">出勤</th>
                    <th colspan="2">退勤</th>
                    <th class="s_6" rowspan="3">総労働時間</th>
                </tr>
                <tr>
                    <th class="s_6">予定</th>
                    <th class="s_6" rowspan="2">計算時刻</th>
                    <th class="s_6">予定</th>
                    <th class="s_6" rowspan="2">計算時刻</th>
                </tr>
                <tr>
                    <th>打刻時刻</th>
                    <th>打刻時刻</th>
                </tr>
                <tr>
                    <td class="bg_light_gray">予定</td>
                    <td rowspan="2">
                        <div class="selectbox">
                            <!-- work status -->
                            <select class="ss_size" v-model="computedPlannedWorkStatusId" :disabled="(computedPlannedRestStatusId !== null && computedPlannedRestStatusId !== '')  ||  !canChange">
                                <option value=""></option>
                                <option v-for="workStatus in currentWorkStatusesList" :value="workStatus.id" :disabled="evaluateToDisableWorkStatus(workStatus.id)">{{ workStatus.name }}</option>
                            </select>
                        </div>
                    </td>
                    <td rowspan="2">
                        <div class="selectbox">
                            <!-- rest status -->
                            <select class="ss_size" v-model="computedPlannedRestStatusId" :disabled="(computedPlannedWorkStatusId !== null && computedPlannedWorkStatusId !== '')  ||  !canChange">
                                <option value=""></option>
                                <option v-for="restStatus in currentRestStatusesList" :value="restStatus.id">{{ restStatus.name }}</option>
                            </select>
                        </div>
                    </td>
                    <td rowspan="2">
                        <input id="paid_rest_time_start" class="ss_size right_4" :value="computedPaidRestTimeStart | reformatTime" type="text"
                            @keydown.enter = "updateAndReload('computedPaidRestTimeStart', $event)"
                            @blur = "updateAndReload('computedPaidRestTimeStart', $event)"
                            :disabled = "evaluateToDisablePaidRestTime  ||  !canChange"
                        >
                        <span class="right_4">〜</span>
                        <input id="paid_rest_time_end" class="ss_size" :value="computedPaidRestTimeEnd | reformatTime" type="text"
                            @keydown.enter = "updateAndReload('computedPaidRestTimeEnd', $event)"
                            @blur = "updateAndReload('computedPaidRestTimeEnd', $event)"
                            :disabled = "evaluateToDisablePaidRestTime  ||  !canChange"
                        >
                    </td>
                    <td class="bg_light_gray">
                        <div class="selectbox" v-if="!workAddressMode">
                            <select class="mm_size" v-model="computedPlannedWorkLocationId" autocomplete="off" :disabled="!canChange">
                                <option v-for="workLocation in workLocationsList" :value="workLocation.id">{{ workLocation.name }}</option>
                            </select>
                        </div>
                        <template v-else>{{ currentPlannedWorkAddressName }}</template>
                    </td>
                    <td class="bg_light_gray">{{ computedPlannedStartWorkTime | reformatTime}}</td>
                    <td rowspan="2">{{ computedRealStartWorkTime | reformatTime }}</td>
                    <td class="bg_light_gray">{{ computedPlannedEndWorkTime | reformatTime}}</td>
                    <td rowspan="2">{{ computedRealEndWorkTime | reformatTime }}</td>
                    <td class="bg_light_gray">{{ computedPlannedWorkingHour | reformatTime}}</td>
                </tr>
                <tr>
                    <td>実績</td>
                    <td>{{ currentRealWorkAddressName !== null ? currentRealWorkAddressName : currentRealWorkLocationName }}</td>
                    <td :class="{ red : alertRealStartWorkTime }">{{ computedTimestampedStartWorkTime | reformatTime}}</td>
                    <td :class="{ red : alertRealEndWorkTime }">{{ computedTimestampedEndWorkTime | reformatTime}}</td>
                    <td>{{ computedRealWorkingHour | reformatTime}}</td>
                </tr>
            </table>
            <table class="right secound">
                <tr>
                    <th>備考</th>
                </tr>
                <tr>
                    <td><textarea name="remarks" :disabled="!canChange" v-model="computedNote" ></textarea></td>
                </tr>
            </table>
    <!--            <div class="lock_close2"></div>-->
    <!--            <div class="lock_request2"></div>-->
        </section>
        <section class="button salary_btn open_btn"><p class="s_size m_height btn_details" @click="toggleDisplaySalaryTable()">開く</p></section>
        <section class="default_table">
            <table>
                <tr>
                    <th class="s_6"></th>
                    <th class="s_12">早出</th>
                    <th class="s_6">遅刻</th>
                    <th class="s_12">所定内</th>
                    <th class="s_6">休憩</th>
                    <th class="s_6">(内)深休</th>
                    <th class="s_6">外出</th>
                    <th class="s_6">早退</th>
                    <th class="s_12">残業</th>
                    <th class="s_8">最終変更者</th>
                </tr>
                <tr>
                    <td class="bg_light_gray">予定</td>
                    <td class="bg_light_gray">
                        <input id="planned_early_arrive_start" class="ss_size right_4" :value="computedPlannedEarlyArriveStart | reformatTime" type="text"
                            @keydown.enter = "updateAndReload('computedPlannedEarlyArriveStart', $event)"
                            @blur = "updateAndReload('computedPlannedEarlyArriveStart', $event)"
                            :disabled = "computedPlannedLateTime !== null  ||  !canChange"
                        >
                        <span class="right_4">〜</span>
                        <input id="planned_early_arrive_end" class="ss_size" :value="computedPlannedEarlyArriveEnd | reformatTime" type="text"
                            @keydown.enter = "updateAndReload('computedPlannedEarlyArriveEnd', $event)"
                            @blur = "updateAndReload('computedPlannedEarlyArriveEnd', $event)"
                            :disabled = "computedPlannedLateTime !== null  ||  !canChange"
                        >
                    </td>
                    <td class="bg_light_gray">
                        <input id="planned_late_time" class="ss_size" :value="computedPlannedLateTime" type="text"
                            @keydown.enter = "updateAndReload('computedPlannedLateTime', $event, false)"
                            @blur = "updateAndReload('computedPlannedLateTime', $event, false)"
                            :disabled = "computedPlannedEarlyArriveStart !== null || computedPlannedEarlyArriveEnd !== null || (computedScheduleStartWorkTime === null && computedScheduleEndWorkTime === null && computedScheduleWorkingHour !== null)  ||  !canChange"
                        >
                    </td>
                    <td class="bg_light_gray">
                        <template v-if="(computedPlannedWorkSpanStart !== null) && (computedPlannedWorkSpanEnd !== null) && (remainingWorkSpanTimeInMinutes > 0)">
                            <span class="right_4">{{ computedPlannedWorkSpanStart | reformatTime}}</span>
                            <span class="right_4">〜</span>
                            <span>{{ computedPlannedWorkSpanEnd | reformatTime}}</span>
                        </template>
                        <span v-else>{{ computedPlannedWorkSpan | reformatTime}}</span>
                    </td>
                    <td class="bg_light_gray">
                        <input id="planned_break_time" class="ss_size" :value="computedPlannedBreakTime" type="text"
                            @keydown.enter = "updateAndReload('computedPlannedBreakTime', $event, false)"
                            @blur = "updateAndReload('computedPlannedBreakTime', $event, false)"
                            :disabled="!canChange"
                        >
                    </td>
                    <td class="bg_light_gray">
                        <input id="planned_night_break_time" class="ss_size" :value="computedPlannedNightBreakTime" type="text"
                            @keydown.enter = "updateAndReload('computedPlannedNightBreakTime', $event, false)"
                            @blur = "updateAndReload('computedPlannedNightBreakTime', $event, false)"
                            :disabled="!canChange"
                        >
                    </td>
                    <td class="bg_light_gray"></td>
                    <td class="bg_light_gray">
                        <input id="planned_early_leave_time" class="ss_size" :value="computedPlannedEarlyLeaveTime" type="text"
                            @keydown.enter = "updateAndReload('computedPlannedEarlyLeaveTime', $event, false)"
                            @blur = "updateAndReload('computedPlannedEarlyLeaveTime', $event, false)"
                            :disabled = "computedPlannedOvertimeStart !== null || computedPlannedOvertimeEnd !== null  ||  !canChange"
                        >
                    </td>
                    <td class="bg_light_gray">
                        <input id="planned_overtime_start" class="ss_size right_4" :value="computedPlannedOvertimeStart | reformatTime" type="text"
                            @keydown.enter = "updateAndReload('computedPlannedOvertimeStart', $event)"
                            @blur = "updateAndReload('computedPlannedOvertimeStart', $event)"
                            :disabled = "computedPlannedEarlyLeaveTime !== null  ||  !canChange"
                        >
                        <span class="right_4">〜</span>
                        <input id="planned_overtime_end" class="ss_size" :value="computedPlannedOvertimeEnd | reformatTime" type="text"
                            @keydown.enter = "updateAndReload('computedPlannedOvertimeEnd', $event)"
                            @blur = "updateAndReload('computedPlannedOvertimeEnd', $event)"
                            :disabled = "computedPlannedEarlyLeaveTime !== null  ||  !canChange"
                        >
                    </td>
                    <td rowspan="2">{{ this.root['last_modified_manager_name'] }}</td>
                </tr>
                <tr>
                    <td>実績</td>
                    <td>{{ computedRealEarlyArriveStart | reformatTime}}&nbsp;&nbsp;〜&nbsp;&nbsp;{{ computedRealEarlyArriveEnd | reformatTime}}</td>
                    <td>{{ computedRealLateTime }}</td>
                    <td>
                        <template v-if="(computedPlannedWorkSpanStart !== null) && (computedPlannedWorkSpanEnd !== null) && (remainingWorkSpanTimeInMinutes > 0)">
                            <span class="right_4">{{ computedRealWorkSpanStart | reformatTime}}</span>
                            <span class="right_4">〜</span>
                            <span>{{ computedRealWorkSpanEnd | reformatTime}}</span>
                        </template>
                        <span v-else>{{ computedRealWorkSpan | reformatTime}}</span>
                    </td>
                    <td>
                        <input id="real_break_time" class="ss_size" :value="computedRealBreakTime" type="text"
                            @keydown.enter = "updateAndReload('computedRealBreakTime', $event, false)"
                            @blur = "updateAndReload('computedRealBreakTime', $event, false)"
                            :disabled="!canChange"
                        >
                    </td>
                    <td>
                        <input id="real_night_break_time" class="ss_size" :value="computedRealNightBreakTime" type="text"
                            @keydown.enter = "updateAndReload('computedRealNightBreakTime', $event, false)"
                            @blur = "updateAndReload('computedRealNightBreakTime', $event, false)"
                            :disabled="!canChange"
                        >
                    </td>
                    <td :class="{ red : alertRealGoOutTime }">{{ computedRealGoOutTime }}</td>
                    <td>{{ computedRealEarlyLeaveTime }}</td>
                    <td>{{ computedRealOvertimeStart | reformatTime}}&nbsp;&nbsp;〜&nbsp;&nbsp;{{ computedRealOvertimeEnd | reformatTime}}</td>
                </tr>
            </table>
            <!--給与条件プルダウン-->
            <transition name="slide-down">
                <section class="salary_table" v-if="display_salary_table">
                    <table>
                        <tr>
                            <th>基本給</th>
                            <th>深夜</th>
                            <th>残業</th>
                            <th>控除</th>
                            <th>深夜控除</th>
                            <th>交通費</th>
                        </tr>
                        <tr>
                            <td>
                                <input class="s_size side_input_block right_10" :value="getSalaryAttributes('basic_salary')" type="text"
                                    @keydown.enter="updateAndReloadSalaryFields('basic_salary', $event)"
                                    @blur="updateAndReloadSalaryFields('basic_salary', $event)"
                                    :disabled="!canChange"
                                >
                                <span class="side_input_block">円</span>
                            </td>
                            <td>
                                <input class="ss_size side_input_block right_10" :value="getSalaryAttributes('night_salary')" type="text"
                                    @keydown.enter="updateAndReloadSalaryFields('night_salary', $event)"
                                    @blur="updateAndReloadSalaryFields('night_salary', $event)"
                                    :disabled="!canChange"
                                >
                                <span class="side_input_block">円</span>
                            </td>
                            <td>
                                <input class="ss_size side_input_block right_10" :value="getSalaryAttributes('overtime_salary')" type="text"
                                    @keydown.enter="updateAndReloadSalaryFields('overtime_salary', $event)"
                                    @blur="updateAndReloadSalaryFields('overtime_salary', $event)"
                                    :disabled="!canChange"
                                >
                                <span class="side_input_block">円</span>
                            </td>
                            <td>
                                <input class="ss_size side_input_block right_10" :value="getSalaryAttributes('deduction_salary')" type="text"
                                    @keydown.enter="updateAndReloadSalaryFields('deduction_salary', $event)"
                                    @blur="updateAndReloadSalaryFields('deduction_salary', $event)"
                                    :disabled="!canChange"
                                >
                                <span class="side_input_block">円</span>
                            </td>
                            <td>
                                <input class="ss_size side_input_block right_10" :value="getSalaryAttributes('night_deduction_salary')" type="text"
                                    @keydown.enter="updateAndReloadSalaryFields('night_deduction_salary', $event)"
                                    @blur="updateAndReloadSalaryFields('night_deduction_salary', $event)"
                                    :disabled="!canChange"
                                >
                                <span class="side_input_block">円</span>
                            </td>
                            <td>
                                <div class="selectbox side_input_block right_10">
                                    <select class="s_size" v-model="display_monthly_fee">
                                        <option selected="selected" :value="true">月額</option>
                                        <option :value="false">日額</option>
                                    </select>
                                </div>
                                <input class="s_size side_input_block right_10" v-if="display_monthly_fee" :value="getSalaryAttributes('monthly_traffic_expense')" type="text"
                                    @keydown.enter="updateAndReloadSalaryFields('monthly_traffic_expense', $event)"
                                    @blur="updateAndReloadSalaryFields('monthly_traffic_expense', $event)"
                                    :disabled="!canChange"
                                >
                                <input class="s_size side_input_block right_10" v-if="!display_monthly_fee" :value="getSalaryAttributes('daily_traffic_expense')" type="text"
                                    @keydown.enter="updateAndReloadSalaryFields('daily_traffic_expense', $event)"
                                    @blur="updateAndReloadSalaryFields('daily_traffic_expense', $event)"
                                    :disabled="!canChange"
                                >
                                <span class="side_input_block">円</span>
                            </td>
                        </tr>
                    </table>
                </section>
            </transition>
        </section>
        <section class="btn">
            <p class="button right_30 save_btn" v-if="canChange"><a class="m_size l_height btn_greeen l_font" @click="sendData()">保存</a></p>
            <p class="button right_30" v-if="canChange"><a class="m_size l_height btn_gray l_font" @click="resetThisWorkingInfo()">キャンセル</a></p>
            <p class="button"><a class="m_size l_height btn_gray l_font" href="attendance_management.html">一覧に戻る</a></p>
        </section>
        <!--複数勤務の場合のみhrタグを利用-->
        <hr>
    </section>
</template>
<script>
import Calendar from '../components/caeru_calendar.vue';


export default {
    props: {
        today: {
            type: String,
            required: true,
        },
        rootData: {
            type: Object,
            required: false,
        },
        workLocationsList: {
            type: Array,
            required: true,
        },
        workAddressesList: {
            type: Array,
            required: false,
        },
        timezone: {
            type: Number,
            required: true,
        },
        workingDayId: {
            type: Number,
            required: true,
        },
        employeeId: {
            type: Number,
            required: false,
        },
        scheduleTransferData: {
            required: false,
        },
        alertSettingData: {
            required: false,
        },
        canChange: {
            type: Boolean,
            required: true,
        },
    },
    data: function() {
        return {
            // This is the original data
            root: {
                schedule_start_work_time: null,
                schedule_end_work_time: null,
                schedule_break_time: null,
                schedule_night_break_time: null,
                schedule_working_hour: null,
                planned_work_location_id: null,
                real_work_location_id: null,
                planned_work_address_id: null,
                real_work_address_id: null,
                planned_work_status_id: null,
                real_work_status_id: null,
                planned_rest_status_id: null,
                real_rest_status_id: null,
                paid_rest_time_start: null,
                paid_rest_time_end: null,
                real_paid_rest_time: null,
                real_customized_rest_time: null,
                current_work_time_per_day: null,
                planned_start_work_time: null,
                timestamped_start_work_time: null,
                real_start_work_time: null,
                planned_end_work_time: null,
                timestamped_end_work_time: null,
                real_end_work_time: null,
                planned_working_hour: null,
                real_working_hour: null,
                note: null,
                planned_early_arrive_start: null,
                real_early_arrive_start: null,
                planned_early_arrive_end: null,
                real_early_arrive_end: null,
                planned_late_time: null,
                real_late_time: null,
                planned_work_span_start: null,
                real_work_span_start: null,
                planned_work_span_end: null,
                real_work_span_end: null,
                planned_work_span: null,
                real_work_span: null,
                planned_break_time: null,
                real_break_time: null,
                planned_night_break_time: null,
                real_night_break_time: null,
                planned_go_out_time: null,
                real_go_out_time: null,
                planned_early_leave_time: null,
                real_early_leave_time: null,
                planned_overtime_start: null,
                real_overtime_start: null,
                planned_overtime_end: null,
                real_overtime_end: null,
                last_modified_manager_name: null,
                basic_salary: null,
                night_salary: null,
                overtime_salary: null,
                deduction_salary: null,
                night_deduction_salary: null,
                monthly_traffic_expense: null,
                daily_traffic_expense: null,
                manually_modified: null,
                date_upper_limit: null,
                date_of_the_upper_limit: null,
            },

            // Need attributes need to be change-detected
            changed: {
                schedule_start_work_time: null,
                schedule_end_work_time: null,
                schedule_break_time: null,
                schedule_night_break_time: null,
                schedule_working_hour: null,

                planned_work_location_id: null,
                planned_break_time: null,
                real_break_time: null,
                planned_night_break_time: null,
                real_night_break_time: null,

                basic_salary: null,
                night_salary: null,
                overtime_salary: null,
                deduction_salary: null,
                night_deduction_salary: null,
                monthly_traffic_expense: null,
                daily_traffic_expense: null,
            },

            // These attributes can be directly changed
            directlyEditable: {
                planned_work_status_id: null,
                planned_rest_status_id: null,
                paid_rest_time_start: null,
                paid_rest_time_end: null,
                note: null,
                planned_early_arrive_start: null,
                planned_early_arrive_end: null,
                planned_late_time: null,
                planned_early_leave_time: null,
                planned_overtime_start: null,
                planned_overtime_end: null,
                last_modified_manager_name: null,
            },


            display_salary_table:false,
            display_monthly_fee: true,
            sendingRequest: false,
            // this is just an array of five booleans, to toggle the input of the schedule attributes
            changingSchedule: {
                schedule_start_work_time:          false,
                schedule_end_work_time:            false,
                schedule_break_time:          false,
                schedule_night_break_time:    false,
                schedule_working_hour:   false,
            },

            // constant that mark empty value
            NUM_EMPTY: -1,
            DATE_EMPTY: '1000-01-01 00:00:00',
            TIME_EMPTY: '-00:00:01',

            consts: {
                // These are the constants of RestStatus and WorkStatus models
                work: {
                    KEKKIN    : 1,
                    FURIKYUU  : 2,
                    FURIDE    : 3,
                    HOUDE     : 4,
                    KYUUDE    : 5,
                },
                rest: {
                    YUUKYU_1      : 1,
                    YUUKYU_2      : 2,
                    ZENKYUU_1     : 3,
                    ZENKYUU_2     : 4,
                    GOKYUU_1      : 5,
                    GOKYUU_2      : 6,
                    JIYUU         : 7,
                    HANKYUU_1     : 8,
                    HANKYUU_2     : 9,
                },

                // These are the constants of CalendarRestDay model
                calendar_rest_days: {
                    LAW_BASED_REST_DAY  : 1,
                    NORMAL_REST_DAY     : 2,
                    NOT_A_REST_DAY      : 0,
                }
            },

            // the TransferPicker variables
            showTranferPicker: false,
            transferPickerOptions: null,
            transferPickerDate: null,
        }
    },
    computed: {

        computedScheduleStartWorkTime: {
            get: function() {
                let value = this.getFromRootOrChanged('schedule_start_work_time');
                return (value === this.DATE_EMPTY) ? null : value;
            },
            set: function(newValue) {
                if (newValue !== '') {
                    let validValue = this.validateTimeFormat(newValue);
                    if (validValue !== null && validValue !== this.computedScheduleStartWorkTime) {
                        this.changed['schedule_start_work_time'] = validValue;
                    }
                } else {
                    this.changed['schedule_start_work_time'] = (this.computedScheduleStartWorkTime !== null) ? this.DATE_EMPTY : null;
                }
            },
        },
        computedScheduleEndWorkTime: {
            get: function() {
                let value = this.getFromRootOrChanged('schedule_end_work_time');
                return (value === this.DATE_EMPTY) ? null : value;
            },
            set: function(newValue) {
                if (newValue !== '') {
                    let validValue = this.validateTimeFormat(newValue);
                    if (validValue !== null) {

                        let momentEnd = this.getMomentInstance(validValue);
                        let momentStart = this.getMomentInstance(this.computedScheduleStartWorkTime);
                        momentEnd = (momentStart !== null) ? this.makeTheSecondOneBigger(momentStart, momentEnd) : momentEnd;

                        if (momentEnd.format('YYYY-MM-DD HH:mm:ss') !== this.computedScheduleEndWorkTime) {
                            this.changed['schedule_end_work_time'] = momentEnd.format('YYYY-MM-DD HH:mm:ss');
                        }
                    }
                } else {
                    this.changed['schedule_end_work_time'] = (this.computedScheduleEndWorkTime !== null) ? this.DATE_EMPTY : null;
                }
            }
        },
        computedScheduleBreakTime: {
            get: function() {
                return this.getFromRootOrChanged('schedule_break_time');
            },
            set: function(newValue) {
                let validValue = this.validateMinuteNumber(newValue);
                if (validValue !== null && validValue !== this.computedScheduleBreakTime) {
                    this.changed['schedule_break_time'] = validValue;
                }
            }
        },
        computedScheduleNightBreakTime: {
            get: function() {
                let value = this.getFromRootOrChanged('schedule_night_break_time');
                return (value === this.NUM_EMPTY) ? null : value;
            },
            set: function(newValue) {
                if (newValue !== '') {
                    let validValue = this.validateMinuteNumber(newValue);
                    if (validValue !== null && validValue !== this.computedScheduleNightBreakTime) {
                        this.changed['schedule_night_break_time'] = validValue;
                    }
                } else {
                    this.changed['schedule_night_break_time'] = (this.computedScheduleNightBreakTime !== null) ? this.NUM_EMPTY : null;
                }
            }
        },
        computedScheduleWorkingHour: {
            get: function() {
                return this.getFromRootOrChanged('schedule_working_hour');
            },
            set: function(newValue) {
                let validValue = this.validateTimeFormat(newValue);
                if (validValue !== null && moment(validValue).format('HH:mm:ss') !== this.computedScheduleWorkingHour) {
                    this.changed['schedule_working_hour'] = moment(validValue).format('HH:mm:ss');
                }
            }
        },

        computedPlannedWorkStatusId: {
            get: function() {
                return this.directlyEditable['planned_work_status_id'];
            },
            set: function(value) {
                this.directlyEditable['planned_work_status_id'] = value;
            },
        },
        computedPlannedRestStatusId: {
            get: function() {
                return this.directlyEditable['planned_rest_status_id'];
            },
            set: function(value) {
                this.directlyEditable['planned_rest_status_id'] = value;
                this.computedPaidRestTimeStart = '';
                this.computedPaidRestTimeEnd = '';
            },
        },

        computedPaidRestTimeStart: {
            get: function() {
                return this.isRestStatusUnitDayOrHour('hour') ? this.directlyEditable['paid_rest_time_start'] : null;
            },
            set: function(newValue) {
                if (newValue !== '') {

                    let momentPaidRestStart = this.getMomentInstance(this.validateTimeFormat(newValue));
                    if (momentPaidRestStart !== null) {
                        if (this.computedPaidRestTimeEnd !== null) {
                            let momentPaidRestEnd = this.getMomentInstance(this.computedPaidRestTimeEnd);
                            if (momentPaidRestStart.isAfter(momentPaidRestEnd)) {
                                momentPaidRestStart = this.subtractDayButStillHaveToBiggerThanTheUpperLimit(momentPaidRestStart);
                            }
                        }

                        momentPaidRestStart = this.isWithinScheduleStartAndEnd(momentPaidRestStart);
                        this.directlyEditable['paid_rest_time_start'] = (momentPaidRestStart !== null) ? momentPaidRestStart.format('YYYY-MM-DD HH:mm:ss') : null;
                    }

                } else {
                    this.directlyEditable['paid_rest_time_start'] = null;
                }
            }
        },
        computedPaidRestTimeEnd: {
            get: function() {
                return this.isRestStatusUnitDayOrHour('hour') ? this.directlyEditable['paid_rest_time_end'] : null;
            },
            set: function(newValue) {
                if (newValue !== '') {

                    let momentPaidRestEnd = this.getMomentInstance(this.validateTimeFormat(newValue));
                    if (momentPaidRestEnd !== null) {
                        if (this.computedPaidRestTimeStart !== null) {
                            let momentPaidRestStart = this.getMomentInstance(this.computedPaidRestTimeStart);
                            if (momentPaidRestStart.isAfter(momentPaidRestEnd)) {
                                momentPaidRestEnd = this.makeTheSecondOneBigger(momentPaidRestStart, momentPaidRestEnd);
                            }
                        }

                        momentPaidRestEnd = this.isWithinScheduleStartAndEnd(momentPaidRestEnd);
                        this.directlyEditable['paid_rest_time_end'] = (momentPaidRestEnd !== null) ? momentPaidRestEnd.format('YYYY-MM-DD HH:mm:ss') : null;
                    }

                } else {
                    this.directlyEditable['paid_rest_time_end'] = null;
                }
            }
        },


        computedPlannedWorkLocationId: {
            get: function() {
                if (this.changed['planned_work_location_id'] !== null) {
                    return this.changed['planned_work_location_id'];
                } else {
                    return this.root['planned_work_location_id'];
                }
            },
            set: function(value) {
                this.changed['planned_work_location_id'] = value;

                // reset the work status and rest status after change work location
                this.computedPlannedWorkStatusId = null;
                this.computedPlannedRestStatusId = null;
            },
        },
        computedPlannedWorkAddressId: function() {
            return this.root['planned_work_address_id'];
        },
        computedRealWorkLocationId: function() {
            return this.root['real_work_location_id'];
        },
        computedRealWorkAddressId: function() {
            return this.root['real_work_address_id'];
        },


        computedPlannedStartWorkTime: function() {
            if (this.isCurrentWorkStatus(this.consts.work.HOUDE, this.consts.work.KYUUDE) === true) {
                return this.computedPlannedOvertimeStart;
            } else if (this.totalPlannedEarlyArriveTime > 0 ) {
                return ((this.computedPlannedWorkSpanStart !== null) && (this.computedPlannedWorkSpanEnd !== null)) ? this.computedPlannedEarlyArriveStart : null;
            } else {
                return this.computedPlannedWorkSpanStart;
            }
        },
        computedTimestampedStartWorkTime: function() {
            let momentInstance = this.getMomentInstance(this.root['timestamped_start_work_time']);
            return (momentInstance !== null) ? momentInstance.seconds(0).format('YYYY-MM-DD HH:mm:ss') : null;
        },
        computedRealStartWorkTime: function() {
            if (this.computedTimestampedStartWorkTime !== null && !this.isCurrentWorkStatus(this.consts.work.KEKKIN, this.consts.work.FURIKYUU) && !this.takeAWholeDayOff) {
                let momentTimestamped = this.getMomentInstance(this.computedTimestampedStartWorkTime);

                if (this.computedPaidRestTimeStart !== null && this.computedPaidRestTimeEnd !== null) {
                    let momentPaidRestStart = this.getMomentInstance(this.computedPaidRestTimeStart);
                    let momentPaidRestEnd = this.getMomentInstance(this.computedPaidRestTimeEnd);

                    if (momentPaidRestStart.isSameOrBefore(momentTimestamped) && momentTimestamped.isSameOrBefore(momentPaidRestEnd)) {
                        return momentPaidRestEnd.format('YYYY-MM-DD HH:mm:ss');
                    }
                }
                if (this.computedPlannedStartWorkTime !== null) {
                    let momentPlanned = this.getMomentInstance(this.computedPlannedStartWorkTime);
                    return moment.max(momentPlanned, momentTimestamped).format('YYYY-MM-DD HH:mm:ss');

                } else if (this.computedScheduleWorkingHour !== null) {

                    if (this.totalPlannedEarlyArriveTime > 0) {
                        let momentEarlyArriveStart = this.getMomentInstance(this.computedPlannedEarlyArriveStart);
                        return moment.max(momentEarlyArriveStart, momentTimestamped).format('YYYY-MM-DD HH:mm:ss');
                    }
                    return momentTimestamped.format('YYYY-MM-DD HH:mm:ss');
                } else {
                    return null;
                }
            } else {
                return null;
            }
        },
        computedPlannedEndWorkTime: function() {
            if (this.isCurrentWorkStatus(this.consts.work.HOUDE, this.consts.work.KYUUDE) === true) {
                return this.computedPlannedOvertimeEnd;
            } else if (this.totalPlannedOvertime > 0 ) {
                return ((this.computedPlannedWorkSpanStart !== null) && (this.computedPlannedWorkSpanEnd !== null)) ? this.computedPlannedOvertimeEnd : null;
            } else {
                return this.computedPlannedWorkSpanEnd;
            }
        },
        computedTimestampedEndWorkTime: function() {
            let momentInstance = this.getMomentInstance(this.root['timestamped_end_work_time']);
            return (momentInstance !== null) ? momentInstance.seconds(0).format('YYYY-MM-DD HH:mm:ss') : null;
        },
        computedRealEndWorkTime: function() {
            if (this.computedTimestampedEndWorkTime !== null && !this.isCurrentWorkStatus(this.consts.work.KEKKIN, this.consts.work.FURIKYUU) && !this.takeAWholeDayOff) {
                let momentTimestamped = this.getMomentInstance(this.computedTimestampedEndWorkTime);

                if (this.computedPaidRestTimeStart !== null && this.computedPaidRestTimeEnd !== null) {
                    let momentPaidRestStart = this.getMomentInstance(this.computedPaidRestTimeStart);
                    let momentPaidRestEnd = this.getMomentInstance(this.computedPaidRestTimeEnd);

                    if (momentPaidRestStart.isSameOrBefore(momentTimestamped) && momentTimestamped.isSameOrBefore(momentPaidRestEnd)) {
                        return momentPaidRestStart.format('YYYY-MM-DD HH:mm:ss');
                    }
                }

                if (this.computedPlannedEndWorkTime !== null) {
                    let momentPlanned = this.getMomentInstance(this.computedPlannedEndWorkTime);
                    return moment.min(momentPlanned, momentTimestamped).format('YYYY-MM-DD HH:mm:ss');

                } else if (this.computedScheduleWorkingHour !== null) {

                    if (this.totalPlannedOvertime > 0) {
                        let momentOvertimeEnd = this.getMomentInstance(this.computedPlannedOvertimeEnd);
                        return moment.min(momentOvertimeEnd, momentTimestamped).format('YYYY-MM-DD HH:mm:ss');
                    } else if (this.estimatedEndWorkTime !== null) {
                        let momentEstimatedEndWorkTime = this.getMomentInstance(this.estimatedEndWorkTime);
                        return moment.min(momentTimestamped, momentEstimatedEndWorkTime).format('YYYY-MM-DD HH:mm:ss');
                    }

                    return momentTimestamped.format('YYYY-MM-DD HH:mm:ss');

                } else {
                    return null;
                }
            } else {
                return null;
            }
        },


        computedPlannedWorkingHour: function() {
            if (this.takeAWholeDayOff !== true && this.remainingWorkSpanTimeInMinutes > 0) {
                let momentPlannedWorkSpan = this.getMomentInstance(this.computedPlannedWorkSpan);
                let totalWorkSpanInMinutes = momentPlannedWorkSpan.hours() * 60 + momentPlannedWorkSpan.minutes();
                totalWorkSpanInMinutes += this.totalPlannedEarlyArriveTime + this.totalPlannedOvertime;

                return this.minutesToString(totalWorkSpanInMinutes);
            }
            return null;
        },
        computedRealWorkingHour: function() {
            if (this.computedRealStartWorkTime !== null && this.computedRealEndWorkTime !== null && this.computedRealWorkSpan !== null) {
                let momentRealWorkSpan = this.getMomentInstance(this.computedRealWorkSpan);
                let totalRealWorkSpanInMinutes = momentRealWorkSpan.hours() * 60 + momentRealWorkSpan.minutes();
                totalRealWorkSpanInMinutes += this.totalRealEarlyArriveTime + this.totalRealOvertime;

                return this.minutesToString(totalRealWorkSpanInMinutes);
            }
            return null;
        },


        computedNote: {
            get: function() {
                return this.directlyEditable['note'];
            },
            set: function(value) {
                this.directlyEditable['note'] = value;
            }
        },


        computedPlannedEarlyArriveStart: {
            get: function() {
                return (this.takeAWholeDayOff !== true) ? this.directlyEditable['planned_early_arrive_start'] : null;
            },
            set: function(value) {
                if (value !== '') {

                    let momentEarlyArriveStart = this.isBeforeScheduleStartWorkTime(this.getMomentInstance(this.validateTimeFormat(value)));
                    if (momentEarlyArriveStart !== null) {

                        let momentEarlyArriveEnd = this.computedPlannedEarlyArriveEnd !== null ? this.getMomentInstance(this.computedPlannedEarlyArriveEnd) : null;
                        let momentOvertimeStart = this.computedPlannedOvertimeStart !== null ? this.getMomentInstance(this.computedPlannedOvertimeStart) : null;
                        let momentOvertimeEnd = this.computedPlannedOvertimeEnd !== null ? this.getMomentInstance(this.computedPlannedOvertimeEnd) : null;

                        if (momentEarlyArriveEnd !== null && !momentEarlyArriveStart.isSameOrBefore(momentEarlyArriveEnd)) {
                            return null;
                        }

                        if (momentOvertimeStart !== null && !momentEarlyArriveStart.isSameOrBefore(momentOvertimeStart)) {
                            return null;;
                        }

                        if (momentOvertimeEnd !== null && !momentEarlyArriveStart.isSameOrBefore(momentOvertimeEnd)) {
                            return null;
                        }

                        this.directlyEditable['planned_early_arrive_start'] = momentEarlyArriveStart.format('YYYY-MM-DD HH:mm:ss');
                    }

                } else {
                    this.directlyEditable['planned_early_arrive_start'] = null;
                }

            },
        },
        computedRealEarlyArriveStart: function() {
            if (this.computedRealStartWorkTime !== null && this.computedRealEndWorkTime !== null && this.computedPlannedEarlyArriveStart !== null && this.computedPlannedEarlyArriveEnd !== null) {
                let momentPlannedEarlyArriveStart = this.getMomentInstance(this.computedPlannedEarlyArriveStart);
                let momentPlannedEarlyArriveEnd = this.getMomentInstance(this.computedPlannedEarlyArriveEnd);
                let momentReal = this.getMomentInstance(this.computedRealStartWorkTime);

                if (momentReal.isBefore(momentPlannedEarlyArriveStart)) {
                    return momentPlannedEarlyArriveStart.format('YYYY-MM-DD HH:mm:ss');
                } else if (momentPlannedEarlyArriveStart.isSameOrBefore(momentReal) && momentReal.isBefore(momentPlannedEarlyArriveEnd)) {
                    return momentReal.format('YYYY-MM-DD HH:mm:ss');
                }
            }

            return null;
        },
        computedPlannedEarlyArriveEnd: {
            get: function() {
                return (this.takeAWholeDayOff !== true) ? this.directlyEditable['planned_early_arrive_end'] : null;
            },
            set: function(value) {
                if (value !== '') {
                    let momentEarlyArriveEnd = this.isBeforeScheduleStartWorkTime(this.getMomentInstance(this.validateTimeFormat(value)));
                    if (momentEarlyArriveEnd !== null) {

                        let momentEarlyArriveStart = this.computedPlannedEarlyArriveStart !== null ? this.getMomentInstance(this.computedPlannedEarlyArriveStart) : null;
                        let momentOvertimeStart = this.computedPlannedOvertimeStart !== null ? this.getMomentInstance(this.computedPlannedOvertimeStart) : null;
                        let momentOvertimeEnd = this.computedPlannedOvertimeEnd !== null ? this.getMomentInstance(this.computedPlannedOvertimeEnd) : null;

                        if (momentEarlyArriveStart !== null && !momentEarlyArriveStart.isSameOrBefore(momentEarlyArriveEnd)) {
                            momentEarlyArriveEnd = this.isBeforeScheduleStartWorkTime(this.makeTheSecondOneBigger(momentEarlyArriveStart, momentEarlyArriveEnd));
                            if (!momentEarlyArriveStart.isSameOrBefore(momentEarlyArriveEnd)) {
                                return null;
                            }
                        }

                        if (momentOvertimeStart !== null && !momentEarlyArriveEnd.isSameOrBefore(momentOvertimeStart)) {
                            return null;
                        }

                        if (momentOvertimeEnd !== null && !momentEarlyArriveEnd.isSameOrBefore(momentOvertimeEnd)) {
                            return null;
                        }

                        this.directlyEditable['planned_early_arrive_end'] = momentEarlyArriveEnd.format('YYYY-MM-DD HH:mm:ss');
                    }
                } else {
                    this.directlyEditable['planned_early_arrive_end'] = null;
                }
            }
        },
        computedRealEarlyArriveEnd: function() {
            if (this.computedRealStartWorkTime !== null && this.computedRealEndWorkTime !== null && this.computedPlannedEarlyArriveStart !== null && this.computedPlannedEarlyArriveEnd !== null) {
                let momentPlannedEarlyArriveEnd = this.getMomentInstance(this.computedPlannedEarlyArriveEnd);
                let momentReal = this.getMomentInstance(this.computedRealStartWorkTime);

                if (momentReal.isBefore(momentPlannedEarlyArriveEnd)) {
                    return momentPlannedEarlyArriveEnd.format('YYYY-MM-DD HH:mm:ss');
                }
            }

            return null;
        },

        // These two late time attributes will be disable in the case of "schedule have only working hour and break time"
        computedPlannedLateTime: {
            get: function() {
                return ((this.takeAWholeDayOff === true) ||
                        ((this.computedScheduleStartWorkTime === null) && (this.computedScheduleEndWorkTime === null) && (this.computedScheduleWorkingHour !== null))) ? null : this.directlyEditable['planned_late_time'];
            },
            set: function(newValue) {
                if (newValue !== '') {
                    let validValue = this.validateMinuteNumber(newValue);
                    if (validValue !== null) {
                        this.directlyEditable['planned_late_time'] = validValue;
                    }
                } else {
                    this.directlyEditable['planned_late_time'] = null;
                }
            },
        },
        computedRealLateTime: function() {
            if (this.computedScheduleStartWorkTime !== null && this.computedRealStartWorkTime !== null) {
                let momentSchedule = this.getMomentInstance(this.computedScheduleStartWorkTime);
                let momentReal = this.getMomentInstance(this.computedRealStartWorkTime);

                if (momentReal.isAfter(momentSchedule)) {

                    let lateTime = momentReal.diff(momentSchedule, 'minutes');

                    if (this.computedPaidRestTimeStart !== null && this.computedPaidRestTimeEnd !== null) {
                        let momentPaidRestStart = this.getMomentInstance(this.computedPaidRestTimeStart);
                        let momentPaidRestEnd = this.getMomentInstance(this.computedPaidRestTimeEnd);

                        if (momentPaidRestEnd.isSameOrBefore(momentReal)) {
                            lateTime = lateTime - this.totalTakenPaidRestTime;
                        } else if (momentPaidRestStart.isBefore(momentReal)) {
                            let diff = momentReal.diff(momentPaidRestStart, 'minutes');
                            lateTime = lateTime - diff;
                        }
                    } else if (this.isCurrentRestStatus(this.consts.rest.ZENKYUU_1, this.consts.rest.ZENKYUU_2) === true) {
                        lateTime = lateTime - this.totalTakenPaidRestTime - this.computedScheduleBreakTime;
                    }
                    return lateTime;
                }
            }
            return null;
        },


        computedPlannedWorkSpan: function() {

            if (this.takeAWholeDayOff !== true && this.totalAtWorkTimeInMinutes > 0) {
                let totalWorkSpanInMinutes = this.totalAtWorkTimeInMinutes - this.computedPlannedBreakTime;

                // In these case, the totalAtWorkTime is still the sum of break time and working hour, but the break time is automatically set to 0, that's why we have to subtract the break time once more before calculating
                if (this.isCurrentRestStatus(this.consts.rest.ZENKYUU_1, this.consts.rest.ZENKYUU_2, this.consts.rest.GOKYUU_1, this.consts.rest.GOKYUU_2)) {
                    totalWorkSpanInMinutes -= this.computedScheduleBreakTime;
                }

                totalWorkSpanInMinutes -= this.totalTakenPaidRestTime;

                if (this.computedPlannedLateTime !== null) {
                    totalWorkSpanInMinutes -= this.computedPlannedLateTime;
                }

                if (this.computedPlannedEarlyLeaveTime !== null) {
                    totalWorkSpanInMinutes -= this.computedPlannedEarlyLeaveTime;
                }

                return (totalWorkSpanInMinutes > 0) ? this.minutesToString(totalWorkSpanInMinutes) : '00:00:00';
            } else {
                return null;
            }

        },
        computedRealWorkSpan: function() {
            if (this.computedRealStartWorkTime !== null && this.computedRealEndWorkTime !== null && this.computedPlannedWorkSpan !== null) {
                let momentRealStart = this.getMomentInstance(this.computedRealStartWorkTime);
                let momentRealEnd = this.getMomentInstance(this.computedRealEndWorkTime);
                let totalWorkingHoursInMinutes = momentRealEnd.diff(momentRealStart, 'minutes') - this.computedRealBreakTime;
                totalWorkingHoursInMinutes -= (this.totalRealEarlyArriveTime + this.totalRealOvertime);

                if (this.computedPaidRestTimeStart !== null && this.computedPaidRestTimeEnd !== null) {
                    let momentPaidRestStart = this.getMomentInstance(this.computedPaidRestTimeStart);
                    let momentPaidRestEnd = this.getMomentInstance(this.computedPaidRestTimeEnd);

                    if (momentRealStart.isSameOrBefore(momentPaidRestStart) && momentPaidRestEnd.isSameOrBefore(momentRealEnd)) {
                        totalWorkingHoursInMinutes -= this.totalTakenPaidRestTime;
                    }
                }

                let momentPlannedWorkSpan = this.getMomentInstance(this.computedPlannedWorkSpan);
                let momentRealWorkSpan = this.getMomentInstance(this.minutesToString(totalWorkingHoursInMinutes));
                return moment.min(momentRealWorkSpan, momentPlannedWorkSpan).format('HH:mm:ss');
            } else {
                return null;
            }
        },
        computedPlannedWorkSpanStart: function() {

            if (this.takeAWholeDayOff !== true && this.remainingWorkSpanTimeInMinutes > 0) {
                let momentScheduleStart = this.getMomentInstance(this.computedScheduleStartWorkTime);
                let momentScheduleEnd = this.getMomentInstance(this.computedScheduleEndWorkTime);

                if ((momentScheduleStart !== null) && (momentScheduleEnd !== null)) {

                    let offset = 0;
                    if (this.isCurrentRestStatus(this.consts.rest.ZENKYUU_1, this.consts.rest.ZENKYUU_2) === true) {
                        offset = this.offsetTimeToAddOrSubWhenTakeHalfDayOff();

                    } else if ((this.isRestStatusUnitDayOrHour('hour') == true) && (this.computedPaidRestTimeStart != null) && (this.computedPaidRestTimeEnd != null)) {
                        let momentRestStart = this.getMomentInstance(this.computedPaidRestTimeStart);
                        let momentRestEnd = this.getMomentInstance(this.computedPaidRestTimeEnd);

                        if (momentRestStart.isSame(momentScheduleStart)) {
                            offset = momentRestEnd.diff(momentRestStart, 'minutes');

                        } else {
                            let diffFromScheduleStart = momentRestStart.diff(momentScheduleStart, 'minutes');
                            if (diffFromScheduleStart <= this.computedPlannedLateTime) {
                                offset = momentRestEnd.diff(momentRestStart, 'minutes');
                            }

                        }
                    }

                    if (offset > 0) {
                        momentScheduleStart.add(offset, 'minutes');
                    }

                    if (this.computedPlannedLateTime !== null) {
                        momentScheduleStart.add(this.computedPlannedLateTime, 'minutes');
                    }

                    return momentScheduleStart.format('YYYY-MM-DD HH:mm:ss');
                }
            }
            return null;
        },
        computedRealWorkSpanStart: function() {
            if (this.computedRealStartWorkTime !== null && this.computedPlannedWorkSpanStart !== null) {
                let momentRealStart = this.getMomentInstance(this.computedRealStartWorkTime);
                let momentPlannedStart = this.getMomentInstance(this.computedPlannedWorkSpanStart);

                return moment.max(momentRealStart, momentPlannedStart).format('YYYY-MM-DD HH:mm:ss');
            } else {
                return null;
            }
        },
        computedPlannedWorkSpanEnd: function() {

            if (this.takeAWholeDayOff !== true && this.remainingWorkSpanTimeInMinutes > 0) {
                let momentScheduleStart = this.getMomentInstance(this.computedScheduleStartWorkTime);
                let momentScheduleEnd = this.getMomentInstance(this.computedScheduleEndWorkTime);

                if ((momentScheduleEnd !== null) && (momentScheduleStart !== null)) {

                    let offset = 0;
                    if (this.isCurrentRestStatus(this.consts.rest.GOKYUU_1, this.consts.rest.GOKYUU_2)) {
                        offset = this.offsetTimeToAddOrSubWhenTakeHalfDayOff();

                    } else if ((this.isRestStatusUnitDayOrHour('hour') == true) && (this.computedPaidRestTimeStart != null) && (this.computedPaidRestTimeEnd != null)) {
                        let momentRestStart = this.getMomentInstance(this.computedPaidRestTimeStart);
                        let momentRestEnd = this.getMomentInstance(this.computedPaidRestTimeEnd);

                        if (momentRestEnd.isSame(momentScheduleEnd)) {
                            offset = momentRestEnd.diff(momentRestStart, 'minutes');

                        } else {
                            let diffFromScheduleEnd = momentScheduleEnd.diff(momentRestEnd, 'minutes');
                            if (diffFromScheduleEnd <= this.computedPlannedEarlyLeaveTime) {
                                offset = momentRestEnd.diff(momentRestStart, 'minutes');
                            }
                        }
                    }

                    if (offset > 0) {
                        momentScheduleEnd.subtract(offset, 'minutes');
                    }

                    if (this.computedPlannedEarlyLeaveTime !== null) {
                        momentScheduleEnd.subtract(this.computedPlannedEarlyLeaveTime, 'minutes');
                    }

                    return momentScheduleEnd.format('YYYY-MM-DD HH:mm:ss');
                }
            }
            return null;
        },
        computedRealWorkSpanEnd: function() {
            if (this.computedRealEndWorkTime !== null && this.computedPlannedWorkSpanEnd !== null) {
                let momentRealEnd = this.getMomentInstance(this.computedRealEndWorkTime);
                let momentPlannedEnd = this.getMomentInstance(this.computedPlannedWorkSpanEnd);

                return moment.min(momentRealEnd, momentPlannedEnd).format('YYYY-MM-DD HH:mm:ss');
            } else {
                return null;
            }
        },



        computedPlannedBreakTime: {
            get: function() {
                if (this.takeAWholeDayOff !== true && !this.isCurrentRestStatus(this.consts.rest.ZENKYUU_1, this.consts.rest.ZENKYUU_2, this.consts.rest.GOKYUU_1, this.consts.rest.GOKYUU_2)) {

                    if (this.changed['planned_break_time'] !== null) {
                        return this.changed['planned_break_time'];
                    } else {
                        if (this.root['planned_break_time'] !== this.root['schedule_break_time']) {
                            return this.root['planned_break_time'];
                        } else {
                            return this.computedScheduleBreakTime;
                        }
                    }

                } else {
                    return null;
                }
            },
            set: function(value) {
                let validValue = this.validateMinuteNumber(value);
                if (validValue !== null && validValue !== this.getFromRootOrChanged('planned_break_time')) {
                    this.changed['planned_break_time'] = validValue;
                }
            }
        },
        computedRealBreakTime: {
            get: function() {
                if (this.isCurrentWorkStatus(this.consts.work.KEKKIN, this.consts.work.FURIKYUU)) {
                    return null;
                } else if (this.changed['real_break_time'] !== null) {
                    return this.changed['real_break_time'];
                } else {
                    if (this.root['real_break_time'] === this.root['planned_break_time'] && this.root['real_break_time'] !== null) {
                        return this.computedPlannedBreakTime;
                    } else {
                        return this.root['real_break_time'];
                    }
                }
            },
            set: function(value) {
                let validValue = this.validateMinuteNumber(value);
                if (validValue !== null && validValue !== this.getFromRootOrChanged('real_break_time')) {
                    this.changed['real_break_time'] = validValue;
                }
            }
        },
        computedPlannedNightBreakTime: {
            get: function() {
                if (this.takeAWholeDayOff !== true && !this.isCurrentRestStatus(this.consts.rest.ZENKYUU_1, this.consts.rest.ZENKYUU_2, this.consts.rest.GOKYUU_1, this.consts.rest.GOKYUU_2)) {

                    if (this.changed['planned_night_break_time'] !== null) {
                        return (this.changed['planned_night_break_time'] === this.NUM_EMPTY) ? null : this.changed['planned_night_break_time'];
                    } else {
                        if (this.root['planned_night_break_time'] !== this.root['schedule_night_break_time']) {
                            return (this.changed['planned_night_break_time'] === this.NUM_EMPTY) ? null : this.changed['planned_night_break_time'];
                        } else {
                            return this.computedScheduleNightBreakTime;
                        }
                    }

                } else {
                    return null;
                }
            },
            set: function(value) {
                if (value !== '') {
                    let validValue = this.validateMinuteNumber(value);
                    if (validValue !== null && validValue !== this.getFromRootOrChanged('planned_night_break_time')) {
                        this.changed['planned_night_break_time'] = validValue;
                    }
                } else {
                    this.changed['planned_night_break_time'] = (this.computedPlannedNightBreakTime !== null) ? this.NUM_EMPTY : null;
                }
            }
        },
        computedRealNightBreakTime: {
            get: function() {
                if (this.changed['real_night_break_time'] !== null) {
                    return (this.changed['real_night_break_time'] === this.NUM_EMPTY) ? null : this.changed['real_night_break_time'];
                } else {
                    if (this.root['real_night_break_time'] === this.root['planned_night_break_time'] && this.root['real_night_break_time'] !== null) {
                        return this.computedPlannedNightBreakTime;
                    } else {
                        return (this.changed['real_night_break_time'] === this.NUM_EMPTY) ? null : this.root['real_night_break_time'];
                    }
                }
            },
            set: function(value) {
                if (value !== '') {
                    let validValue = this.validateMinuteNumber(value);
                    if (validValue !== null && validValue !== this.getFromRootOrChanged('real_night_break_time')) {
                        this.changed['real_night_break_time'] = validValue;
                    }
                } else {
                    this.changed['real_night_break_time'] = (this.computedRealNightBreakTime !== null) ? this.NUM_EMPTY : null;
                }
            }
        },

        computedRealGoOutTime: function() {
            return this.root['real_go_out_time'];
        },

        computedPlannedEarlyLeaveTime: {
            get: function() {
                return (this.takeAWholeDayOff !== true) ? this.directlyEditable['planned_early_leave_time'] : null;
            },
            set: function(newValue) {
                if (newValue !== '') {
                    let validValue = this.validateMinuteNumber(newValue);
                    if (validValue !== null) {
                        this.directlyEditable['planned_early_leave_time'] = validValue;
                    }
                } else {
                    this.directlyEditable['planned_early_leave_time'] = null;
                }
            }
        },
        computedRealEarlyLeaveTime: function() {
            if (this.computedScheduleEndWorkTime !== null && this.computedRealEndWorkTime !== null) {
                let momentSchedule = this.getMomentInstance(this.computedScheduleEndWorkTime);
                let momentReal = this.getMomentInstance(this.computedRealEndWorkTime);

                if (momentReal.isBefore(momentSchedule)) {
                    let earlyLeaveTime = momentSchedule.diff(momentReal, 'minutes');

                    if (this.computedPaidRestTimeStart !== null && this.computedPaidRestTimeEnd !== null) {
                        let momentPaidRestStart = this.getMomentInstance(this.computedPaidRestTimeStart);
                        let momentPaidRestEnd = this.getMomentInstance(this.computedPaidRestTimeEnd);

                        if (momentReal.isSameOrBefore(momentPaidRestStart)) {
                            earlyLeaveTime = earlyLeaveTime - this.totalTakenPaidRestTime;
                        } else if (momentReal.isSameOrBefore(momentPaidRestEnd)) {
                            let diff = momentPaidRestEnd.diff(momentReal, 'minutes');
                            earlyLeaveTime = earlyLeaveTime - diff;
                        }

                    } else if (this.isCurrentRestStatus(this.consts.rest.GOKYUU_1, this.consts.rest.GOKYUU_2) === true) {
                        earlyLeaveTime = earlyLeaveTime - this.totalTakenPaidRestTime - this.computedScheduleBreakTime;
                    }
                    return earlyLeaveTime;
                }
            } else if (this.computedRealEndWorkTime !== null) {
                let plannedEarlyLeave = (this.computedPlannedEarlyLeaveTime !== null) ? this.computedPlannedEarlyLeaveTime : 0;
                let momentPlannedWorkSpan = this.getMomentInstance(this.computedPlannedWorkSpan);
                let momentRealWorkSpan = this.getMomentInstance(this.computedRealWorkSpan);

                if (momentPlannedWorkSpan === null && momentRealWorkSpan === null) {
                    // This the case when: work_status is houde or kyude
                    let momentRealEnd = this.getMomentInstance(this.computedRealEndWorkTime);
                    let momentPlannedEnd = this.getMomentInstance(this.computedPlannedEndWorkTime);
                    return momentRealEnd.isSame(momentPlannedEnd) ? null : momentPlannedEnd.diff(momentRealEnd, 'minutes');

                } else if (momentPlannedWorkSpan.isSame(momentRealWorkSpan)) {
                    return plannedEarlyLeave;
                } else {
                    let diff = momentPlannedWorkSpan.diff(momentRealWorkSpan, 'minutes');

                    return plannedEarlyLeave + diff;
                }
            }

            return null;
        },


        computedPlannedOvertimeStart: {
            get: function() {
                return (this.takeAWholeDayOff !== true) ? this.directlyEditable['planned_overtime_start'] : null;
            },
            set: function(value) {
                if (value !== '') {
                    let momentOvertimeStart = this.isAfterScheduleEndWorkTime(this.getMomentInstance(this.validateTimeFormat(value)));
                    if (momentOvertimeStart !== null) {
                        if (this.upperLimitForOvertime !== null && momentOvertimeStart.isBefore(this.getMomentInstance(this.upperLimitForOvertime))) {
                            momentOvertimeStart.add(1, 'days');
                        }
                        this.directlyEditable['planned_overtime_start'] = momentOvertimeStart.format('YYYY-MM-DD HH:mm:ss');
                    }
                } else {
                    this.directlyEditable['planned_overtime_start'] = null;
                }
            }
        },
        computedRealOvertimeStart: function() {
            if (this.computedRealStartWorkTime !== null && this.computedRealEndWorkTime !== null && this.computedPlannedOvertimeStart !== null && this.computedPlannedOvertimeEnd !== null) {
                let momentPlannedOvertimeStart = this.getMomentInstance(this.computedPlannedOvertimeStart);
                let momentPlannedOvertimeEnd = this.getMomentInstance(this.computedPlannedOvertimeEnd);
                let momentReal = this.getMomentInstance(this.computedRealEndWorkTime);

                if (momentPlannedOvertimeStart.isBefore(momentReal)) {
                    return momentPlannedOvertimeStart.format('YYYY-MM-DD HH:mm:ss');
                }
            }

            return null;
        },
        computedPlannedOvertimeEnd: {
            get: function() {
                return (this.takeAWholeDayOff !== true) ? this.directlyEditable['planned_overtime_end'] : null;
            },
            set: function(value) {
                if (value !== '') {

                    let momentOvertimeEnd = this.getMomentInstance(this.validateTimeFormat(value));
                    if (momentOvertimeEnd !== null) {

                        if (this.computedPlannedOvertimeStart !== null) {
                            let momentOvertimeStart = this.getMomentInstance(this.computedPlannedOvertimeStart);
                            momentOvertimeEnd = this.makeTheSecondOneBigger(momentOvertimeStart, momentOvertimeEnd);
                        } else {
                            momentOvertimeEnd = this.isAfterScheduleEndWorkTime(momentOvertimeEnd);
                        }
                        this.directlyEditable['planned_overtime_end'] = momentOvertimeEnd.format('YYYY-MM-DD HH:mm:ss');
                    }

                } else {
                    this.directlyEditable['planned_overtime_end'] = null;
                }
            }
        },
        computedRealOvertimeEnd: function() {
            if (this.computedRealStartWorkTime !== null && this.computedRealEndWorkTime !== null && this.computedPlannedOvertimeStart !== null && this.computedPlannedOvertimeEnd !== null) {
                let momentPlannedOvertimeStart = this.getMomentInstance(this.computedPlannedOvertimeStart);
                let momentPlannedOvertimeEnd = this.getMomentInstance(this.computedPlannedOvertimeEnd);
                let momentReal = this.getMomentInstance(this.computedRealEndWorkTime);

                if (momentReal.isSameOrBefore(momentPlannedOvertimeStart)) {
                    return null;
                } else if (momentPlannedOvertimeStart.isBefore(momentReal) && momentReal.isSameOrBefore(momentPlannedOvertimeEnd)) {
                    return momentReal.format('YYYY-MM-DD HH:mm:ss');
                } else {
                    return momentPlannedOvertimeEnd.format('YYYY-MM-DD HH:mm:ss');
                }
            }

            return null;
        },


        /////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////
        currentPlannedWorkAddressName: function() {
            if (this.computedPlannedWorkAddressId !== null) {
                let workAddress = _.find(this.workAddressesList, (place) => {
                    return place.work_location_id === this.computedPlannedWorkLocationId && place.work_address_id === this.computedPlannedWorkAddressId;
                });
                return workAddress !== undefined ? workAddress.name : null;
            } else {
                return null;
            }
        },
        currentRealWorkAddressName: function() {
            if (this.computedRealWorkAddressId !== null) {
                let workAddress = _.find(this.workAddressesList, (place) => {
                    return place.work_location_id === this.computedRealWorkLocationId && place.work_address_id === this.computedRealWorkAddressId;
                });
                return workAddress !== undefined ? workAddress.name : null;
            } else {
                return null;
            }
        },
        currentRealWorkLocationName: function() {
            if (this.computedRealWorkLocationId !== null) {
                let workLocation = _.find(this.workLocationsList, (place) => { return place.id == this.computedRealWorkLocationId; });
                return workLocation !== undefined ? workLocation.name : null;
            } else {
                return null;
            }
        },
        currentWorkStatusesList: function() {
            let currentWorkLocation = _.find(this.workLocationsList, (workLocation) => { return workLocation.id == this.computedPlannedWorkLocationId; });
            return (currentWorkLocation !== undefined) ? currentWorkLocation.work_statuses : [];
        },
        currentRestStatusesList: function() {
            let currentWorkLocation = _.find(this.workLocationsList, (workLocation) => { return workLocation.id == this.computedPlannedWorkLocationId; });
            return (currentWorkLocation !== undefined) ? currentWorkLocation.rest_statuses : [];
        },
        takeAWholeDayOff: function() {
            let momentScheduleWorkingHour = this.getMomentInstance(this.computedScheduleWorkingHour);
            if (momentScheduleWorkingHour !== null) {
                let scheduleWorkingHourInMinutes = momentScheduleWorkingHour.hours() * 60 + momentScheduleWorkingHour.minutes();
                if (this.isRestStatusUnitDayOrHour('day') === true ||
                    (this.totalTakenPaidRestTime !== 0 && this.totalTakenPaidRestTime === scheduleWorkingHourInMinutes)) {
                    return true;
                }
            }
            return false;
        },
        totalTakenPaidRestTime: function() {
            let momentPaidRestEnd = this.getMomentInstance(this.computedPaidRestTimeEnd);
            let momentPaidRestStart = this.getMomentInstance(this.computedPaidRestTimeStart);

            if (momentPaidRestEnd !== null && momentPaidRestStart !== null && this.isRestStatusUnitDayOrHour('hour') === true) {
                let totalTakenPaidRestTimeInMinutes = momentPaidRestEnd.diff(momentPaidRestStart, 'minutes');
                let momentScheduleWorkingHour = this.getMomentInstance(this.computedScheduleWorkingHour);
                let scheduleWorkingHourInMinutes = momentScheduleWorkingHour.hours() * 60 + momentScheduleWorkingHour.minutes();
                return totalTakenPaidRestTimeInMinutes > scheduleWorkingHourInMinutes ? scheduleWorkingHourInMinutes : totalTakenPaidRestTimeInMinutes;

            } else if (this.isCurrentRestStatus(this.consts.rest.ZENKYUU_1, this.consts.rest.ZENKYUU_2, this.consts.rest.GOKYUU_1, this.consts.rest.GOKYUU_2)) {
                let workSpan = this.getMomentInstance(this.computedScheduleWorkingHour);
                return workSpan !== null ? _.ceil((workSpan.hours() * 60 + workSpan.minutes())/2) : null;

            } else {
                return 0;
            }
        },
        remainingWorkSpanTimeInMinutes: function() {
            let totalWorkSpanTime = this.getMomentInstance(this.computedScheduleWorkingHour);

            if (totalWorkSpanTime) {
                let totalWorkSpanTimeInMinutes = totalWorkSpanTime.hours() * 60 + totalWorkSpanTime.minutes();
                let lateTime = this.computedPlannedLateTime !== null ? this.computedPlannedLateTime : 0;
                let earlyLeave = this.computedPlannedEarlyLeaveTime !== null ? this.computedPlannedEarlyLeaveTime : 0;

                let remainingWorkSpanTimeInMinutes = totalWorkSpanTimeInMinutes - this.totalTakenPaidRestTime - lateTime - earlyLeave;

                return remainingWorkSpanTimeInMinutes >= 0 ? remainingWorkSpanTimeInMinutes : 0;
            } else {
                return 0;
            }
        },
        totalPlannedEarlyArriveTime: function() {
            let momentEarlyArriveStart = this.getMomentInstance(this.computedPlannedEarlyArriveStart);
            let momentEarlyArriveEnd = this.getMomentInstance(this.computedPlannedEarlyArriveEnd);
            if (momentEarlyArriveStart !== null && momentEarlyArriveEnd !== null) {
                return momentEarlyArriveEnd.diff(momentEarlyArriveStart, 'minutes');
            } else {
                return 0;
            }
        },
        totalRealEarlyArriveTime: function() {
            let momentEarlyArriveStart = this.getMomentInstance(this.computedRealEarlyArriveStart);
            let momentEarlyArriveEnd = this.getMomentInstance(this.computedRealEarlyArriveEnd);
            if (momentEarlyArriveStart !== null && momentEarlyArriveEnd !== null) {
                return momentEarlyArriveEnd.diff(momentEarlyArriveStart, 'minutes');
            } else {
                return 0;
            }
        },
        totalPlannedOvertime: function() {
            let momentOvertimeStart = this.getMomentInstance(this.computedPlannedOvertimeStart);
            let momentOvertimeEnd = this.getMomentInstance(this.computedPlannedOvertimeEnd);
            if (momentOvertimeStart !== null && momentOvertimeEnd !== null) {
                return momentOvertimeEnd.diff(momentOvertimeStart, 'minutes');
            } else {
                return 0;
            }
        },
        totalRealOvertime: function() {
            let momentOvertimeStart = this.getMomentInstance(this.computedRealOvertimeStart);
            let momentOvertimeEnd = this.getMomentInstance(this.computedRealOvertimeEnd);
            if (momentOvertimeStart !== null && momentOvertimeEnd !== null) {
                return momentOvertimeEnd.diff(momentOvertimeStart, 'minutes');
            } else {
                return 0;
            }
        },
        totalAtWorkTimeInMinutes: function() {
            if (this.computedScheduleWorkingHour !== null) {
                let momentWorkingHour = this.getMomentInstance(this.computedScheduleWorkingHour);
                let breakTime = (this.computedScheduleBreakTime !== null) ? this.computedScheduleBreakTime : 0;
                let totalTime = momentWorkingHour.hours() * 60 + momentWorkingHour.minutes() + breakTime;
                return totalTime;
            } else {
                return 0;
            }
        },

        // These computed properties are for the case of 'only working hour and break time' schedule
        upperLimitForOvertime: function() {
            if (this.computedScheduleStartWorkTime === null && this.computedScheduleEndWorkTime === null && this.computedScheduleWorkingHour !== null) {

                let momentScheduleWorkingHour = this.getMomentInstance(this.computedScheduleWorkingHour);

                if (this.computedPlannedEarlyArriveStart !== null && this.computedPlannedEarlyArriveEnd !== null) {
                    let momentEarlyArriveEnd = this.getMomentInstance(this.computedPlannedEarlyArriveEnd);
                    momentEarlyArriveEnd.add(this.totalAtWorkTimeInMinutes, 'minutes');
                    return momentEarlyArriveEnd.format('YYYY-MM-DD HH:mm:ss');
                } else {
                    let momentUpperLimit = this.getMomentInstanceOfUpperLimit();
                    momentUpperLimit.add(this.totalAtWorkTimeInMinutes, 'minutes');
                    return momentUpperLimit.format('YYYY-MM-DD HH:mm:ss');
                }

            } else {
                return null;
            }
        },
        estimatedEndWorkTime: function() {
            if (this.computedScheduleStartWorkTime === null && this.computedScheduleEndWorkTime === null && this.computedScheduleWorkingHour !== null && this.computedTimestampedStartWorkTime !== null) {
                let plannedEarlyLeave = (this.computedPlannedEarlyLeaveTime !== null) ? this.computedPlannedEarlyLeaveTime : 0;
                let maximumWorkingHourInMinutes = this.totalAtWorkTimeInMinutes + this.totalPlannedOvertime + this.totalPlannedEarlyArriveTime - plannedEarlyLeave;

                if (this.isCurrentRestStatus(this.consts.rest.ZENKYUU_1, this.consts.rest.ZENKYUU_2, this.consts.rest.GOKYUU_1, this.consts.rest.GOKYUU_2)) {
                    let momentScheduleWorkingHour = this.getMomentInstance(this.computedScheduleWorkingHour);
                    let offset = _.ceil(momentScheduleWorkingHour.hours() * 60 + momentScheduleWorkingHour.minutes())/2;
                    maximumWorkingHourInMinutes -= offset;
                }

                let momentTimestampedStartWork = this.getMomentInstance(this.computedTimestampedStartWorkTime);
                momentTimestampedStartWork.add(maximumWorkingHourInMinutes, 'minutes');
                return momentTimestampedStartWork.format('YYYY-MM-DD HH:mm:ss');
            } else {
                return null;
            }
        },

        // These are just some utility computed properties
        workAddressMode : function() { return this.root['planned_work_address_id'] !== null ;},
        showDeleteButton : function() {
            return (this.workAddressMode !== true) && (this.computedTimestampedStartWorkTime === null) && (this.computedTimestampedEndWorkTime === null);
        },

        // This computed property's sole purpose is to be watched, so that we can update the schedule_working_hour correctly.
        scheduleInputSet: function() {
            return [this.computedScheduleStartWorkTime, this.computedScheduleEndWorkTime, this.computedScheduleBreakTime];
        },

        isScheduleModified: function() {
            return (this.changed['schedule_start_work_time'] !== null) ||
                    (this.changed['schedule_end_work_time'] !== null) ||
                    (this.changed['schedule_break_time'] !== null) ||
                    (this.changed['schedule_night_break_time'] !== null) ||
                    (this.changed['schedule_working_hour'] !== null) ||
                    (this.changed['planned_work_location_id'] !== null);

        },

        isSalariesModified: function() {
            return (this.changed['basic_salary'] !== null) ||
                    (this.changed['night_salary'] !== null) ||
                    (this.changed['overtime_salary'] !== null) ||
                    (this.changed['deduction_salary'] !== null) ||
                    (this.changed['night_deduction_salary'] !== null) ||
                    (this.changed['daily_traffic_expense'] !== null) ||
                    (this.changed['monthly_traffic_expense'] !== null);
                
        },

        evaluateToDisablePaidRestTime: function() {
            switch (this.computedPlannedRestStatusId) {
                case null:
                case '':
                case this.consts.rest.YUUKYU_1:
                case this.consts.rest.YUUKYU_2:
                case this.consts.rest.ZENKYUU_1:
                case this.consts.rest.ZENKYUU_2:
                case this.consts.rest.GOKYUU_1:
                case this.consts.rest.GOKYUU_2:
                    return true;
                    break;
                default:
                    return false;
            }
        },

        // From here on, it's the transfer and/or date picker's computed properties
        currentTransferPickerDate: {
            get: function() {
                return (this.transferPickerDate === null) ? _.map(_.split(this.today, '-'), (value) => {return _.toInteger(value)}) : this.transferPickerDate;
            },
            set: function(data) {
                this.transferPickerDate = data;
            }
        },

        // Alert the start_work_time, end_work_time or the go_out_time
        alertRealStartWorkTime: function() {
            if (this.alertSettingData !== null) {

                let planned_moment = this.getMomentInstance(this.computedPlannedStartWorkTime);
                let real_moment = this.getMomentInstance(this.computedTimestampedStartWorkTime);

                if (planned_moment !== null && real_moment !== null) {
                    let diff = Math.abs(planned_moment.diff(real_moment, 'minutes'));
                    return diff >= this.alertSettingData['start_time_diff_limit'];
                }
            }
            return false;
        },
        alertRealEndWorkTime: function() {
            if (this.alertSettingData !== null) {

                let planned_moment = this.getMomentInstance(this.computedPlannedEndWorkTime);
                let real_moment = this.getMomentInstance(this.computedTimestampedEndWorkTime);

                if (planned_moment !== null && real_moment !== null) {
                    let diff = Math.abs(real_moment.diff(planned_moment, 'minutes'));
                    return diff >= this.alertSettingData['end_time_diff_limit'];
                }
            }
            return false;
        },
        alertRealGoOutTime: function() {
            if (this.alertSettingData !== null) {
                return this.alertSettingData['alert_go_out_time'] === true && this.root['real_go_out_time'] > this.computedPlannedBreakTime;
            }
            return false;
        }


    },
    watch: {
        scheduleInputSet: function(newSet) {
            if ((newSet[0] !== null) && (newSet[0] !== '') && (newSet[1] !== null) && (newSet[1] !== '')) {
                let momentStartWork = this.getMomentInstance(newSet[0]);
                let momentEndWork = this.getMomentInstance(newSet[1]);
                let breakTime = (breakTime !== null) ? newSet[2] : 0;

                let newWorkingHourValue = this.minutesToString(momentEndWork.diff(momentStartWork, 'minutes') - breakTime);
                if (newWorkingHourValue !== this.computedScheduleWorkingHour)
                    this.computedScheduleWorkingHour = newWorkingHourValue;
            }
        },
        computedScheduleStartWorkTime: function () {
            this.computedScheduleEndWorkTime = (this.computedScheduleEndWorkTime !== null) ? moment(this.computedScheduleEndWorkTime).format('HH:mm:ss') : null;
        },
        computedPlannedOvertimeStart: function() {
            this.computedPlannedOvertimeEnd = (this.computedPlannedOvertimeEnd !== null) ? moment(this.computedPlannedOvertimeEnd).format('HH:mm:ss') : null;
        },
        computedPlannedEarlyArriveEnd: function() {
            if (this.computedScheduleStartWorkTime === null && this.computedScheduleEndWorkTime === null && this.computedScheduleWorkingHour) {
                this.computedPlannedOvertimeStart = (this.computedPlannedOvertimeStart !== null) ? moment(this.computedPlannedOvertimeStart).format('HH:mm:ss') : null;
            }
        },
        rootData: {
            handler: function() {
                this.initializeData();

                // Initialize data for the scheduleTransferPicker
                this.processScheduleTransferData();
            },
            deep: true,
        },

    },
    methods: {

        // Utility functions
        validateTimeFormat: function(string) {
            let time = moment(string, ['HHmm', 'H:mm', 'H:mm:ss'], true).utcOffset(this.timezone);

            if (time.isValid()) {

                // Set time's date to today or to the root's date of upper limit (if it exists)
                let dayBase = (this.root.date_of_the_upper_limit !== null) ? this.root.date_of_the_upper_limit : this.today;
                let dateData = _.split(dayBase, '-');

                time.year(dateData[0]);
                // Have to minus 1 because the month is zero-indexed.
                time.month(dateData[1]-1);
                time.date(dateData[2]);

                if (this.root.date_upper_limit !== null) {
                    let dateUpperLimit = moment(this.root.date_upper_limit, 'YYYY-MM-DD HH:mm:ss').utcOffset(this.timezone);
                    if (time.isBefore(dateUpperLimit))
                        time.add(1, 'days');
                }

                // Then return the full-fledge time string
                return time.format('YYYY-MM-DD HH:mm:ss');

            }
            return null;
        },
        validateMinuteNumber: function(number) {
            if (!isNaN(number) && number !== '') {
                if (0 <= number && number <= 1440)
                    return _.toSafeInteger(number);
            }
            return null;
        },

        // This is the counter-part of getCarbonInstance in the model.
        getMomentInstance: function(time_string) {
            if (time_string !== null && time_string !== '') {

                if (time_string.indexOf(' ') !== -1) {
                    return moment(time_string, 'YYYY-MM-DD HH:mm:ss').utcOffset(this.timezone);
                } else {

                    let day_string = (this.root.date_of_the_upper_limit !== null) ? this.root.date_of_the_upper_limit : this.today;
                    let instance = moment(day_string + ' ' + time_string, 'YYYY-MM-DD HH:mm:ss').utcOffset(this.timezone);

                    if (this.root.date_upper_limit !== null) {
                        let dateUpperLimit = moment(this.root.date_upper_limit, 'YYYY-MM-DD HH:mm:ss').utcOffset(this.timezone);
                        if (instance.isBefore(dateUpperLimit))
                            instance.add(1, 'days');
                    }

                    return instance;
                }

            } else {
                return null;
            }
        },
        minutesToString: function(minutes) {
            return _.padStart(_.floor(minutes/60), 2, '0') + ':' + _.padStart(_.floor(minutes%60), 2, '0') + ':00';
        },
        makeTheSecondOneBigger: function(momentStart, momentEnd) {
            if (momentStart !== null && momentEnd !== null) {
                while (momentEnd.isBefore(momentStart)) {
                    momentEnd.add(1, 'days');
                }
                return momentEnd;
            } else {
                return null;
            }
        },
        getMomentInstanceOfUpperLimit: function() {
            if (this.root.date_upper_limit !== null) {
                return this.getMomentInstance(this.root.date_upper_limit);
            } else {
                return moment(this.today + ' 00:00:00', 'YYYY-MM-DD HH:mm:ss').utcOffset(this.timezone);
            }
        },
        subtractDayButStillHaveToBiggerThanTheUpperLimit: function(moment) {
            if (moment !== null) {
                let momentUpperLimit = this.getMomentInstanceOfUpperLimit();
                moment.subtract(1, 'days');
                if (moment.isBefore(momentUpperLimit)) {
                    return null;
                } else {
                    return moment;
                }
            } else {
                return null;
            }
        },
        calculateDiffInMinutes: function(momentStart, momentEnd) {
            momentEnd = this.makeTheSecondOneBigger(momentStart, momentEnd);
            return momentEnd.diff(momentStart, 'minutes');
        },
        isCurrentWorkStatus: function(...args) {
            let result = false;
            _.forEach(args, (arg) => {
                if ((this.computedPlannedWorkStatusId === arg) &&
                    (_.find(this.currentWorkStatusesList, (status) => {
                        return this.computedPlannedWorkStatusId === status.id;
                    }) !== undefined)) {
                    result = true;
                    return false;
                }
            });
            return result;
        },
        isCurrentRestStatus: function(...args) {
            let result = false;
            _.forEach(args, (arg) => {
                if ((this.computedPlannedRestStatusId === arg) &&
                    (_.find(this.currentRestStatusesList, (status) => {
                        return this.computedPlannedRestStatusId === status.id;
                    }) !== undefined)) {
                    result = true;
                    return false;
                }
            });
            return result;
        },
        isRestStatusUnitDayOrHour: function (type) {
            let restStatus = _.find(this.currentRestStatusesList, (status) => {
                return this.computedPlannedRestStatusId === status.id;
            });
            if (type == 'day') {
                return restStatus ? restStatus.day_based === true : false;
            } else {
                return restStatus ? restStatus.day_based === false : false;
            }
        },
        offsetTimeToAddOrSubWhenTakeHalfDayOff: function() {
            let momentStart = this.computedScheduleStartWorkTime;
            let momentEnd = this.computedScheduleEndWorkTime;
            let momentWorkingHour = this.computedScheduleWorkingHour;

            if ((momentStart !== null) && (momentEnd !== null)) {
                momentStart = this.getMomentInstance(momentStart);
                momentEnd = this.getMomentInstance(momentEnd);

                return (momentEnd.diff(momentStart, 'minutes') + this.computedScheduleBreakTime)/2;
            } else if (momentWorkingHour !== null) {
                momentWorkingHour = this.getMomentInstance(momentWorkingHour);
                moment_zero = this.getMomentInstance('0:0:0');

                return momentWorkingHour.diff(moment_zero, 'minutes')/2;
            }
        },
        isBeforeScheduleStartWorkTime: function(moment) {
            if (moment !== null && this.computedScheduleStartWorkTime !== null && this.computedScheduleEndWorkTime !== null) {
                let momentStartWorkTime = this.getMomentInstance(this.computedScheduleStartWorkTime);
                let momentUpperLimit = this.getMomentInstanceOfUpperLimit();

                while (momentStartWorkTime.isBefore(moment)) {
                    moment.subtract(1, 'days');
                }
                if (momentUpperLimit.isSameOrBefore(moment) && moment.isSameOrBefore(momentStartWorkTime)) {
                    return moment;
                } else {
                    return null;
                }
            } else if (this.computedScheduleStartWorkTime === null && this.computedScheduleEndWorkTime === null) {
                return moment;
            } else {
                return null;
            }
        },
        isAfterScheduleEndWorkTime: function(moment) {
            if (moment !== null && this.computedScheduleStartWorkTime !== null && this.computedScheduleEndWorkTime !== null) {
                let momentEndWorkTime = this.getMomentInstance(this.computedScheduleEndWorkTime);

                while (moment.isBefore(momentEndWorkTime)) {
                    moment.add(1, 'days');
                }
                if (momentEndWorkTime.isSameOrBefore(moment)) {
                    return moment;
                } else {
                    return null;
                }

            } else if (this.computedScheduleStartWorkTime === null && this.computedScheduleEndWorkTime === null) {
                return moment;
            } else {
                return null;
            }
        },
        isWithinScheduleStartAndEnd: function(moment) {
            if (moment !== null && this.computedScheduleStartWorkTime !== null && this.computedScheduleEndWorkTime !== null) {
                let momentStartWorkTime = this.getMomentInstance(this.computedScheduleStartWorkTime);
                let momentEndWorkTime = this.getMomentInstance(this.computedScheduleEndWorkTime);

                if (moment.isBefore(momentStartWorkTime)) {
                    moment.add(1, 'days');
                }

                if (momentStartWorkTime.isSameOrBefore(moment) && moment.isSameOrBefore(momentEndWorkTime)) {
                    return moment;
                } else {
                    return null;
                }

            } else if (this.computedScheduleStartWorkTime === null && this.computedScheduleEndWorkTime === null) {
                return moment;
            } else {
                return null;
            }
        },

        // Evaluate whether or not to disable a WorkStatus
        evaluateToDisableWorkStatus: function(id) {
            switch (id) {
                case this.consts.work.FURIKYUU:
                case this.consts.work.FURIDE:
                    return true;
                    break;
                case this.consts.work.KYUUDE:
                case this.consts.work.HOUDE:
                    return this.computedScheduleWorkingHour !== null;
                    break;
                default:
                    return false;
            }
        },


        // The getter for all of the attributes of EmployeeWorkingInformation instance
        getFromRootOrChanged: function(field_name) {
            if (this.changed[field_name] !== null)
                return this.changed[field_name];
            else
                return this.root[field_name];
        },

        // These three function set are for the schedule-5-set above
        changeSchedule: function(field_name) {
            this.changingSchedule[field_name] = true;
            this.$nextTick(() => {
                $('#' + field_name).focus();
            });
        },
        applySchedule: function(event, computedProp, tagId) {
            this[computedProp] = event.target.value;
            this.doneChangeSchedule(tagId);
        },
        doneChangeSchedule: function(field_name) {
            this.changingSchedule[field_name] = false;
        },
        updateAndReload: function(computedProperty, event, reformatTime = true) {
            this[computedProperty] = event.target.value;
            let reloadedValue = reformatTime ? this.$options.filters.reformatTime(this[computedProperty]) : this[computedProperty];
            event.target.value = reloadedValue;
        },
        getSalaryAttributes: function(fieldName) {
            return this.getFromRootOrChanged(fieldName) === this.NUM_EMPTY ? null : this.getFromRootOrChanged(fieldName);
        },
        updateAndReloadSalaryFields: function(fieldName, event) {
            let value = event.target.value;
            if (value !== '') {
                if (value >= 0 && isNaN(value) === false && value != this.getSalaryAttributes(fieldName)) {
                    this.changed[fieldName] = _.toSafeInteger(value);
                }
            } else {
                if (this.getSalaryAttributes(fieldName) !== null) {
                    this.changed[fieldName] = this.NUM_EMPTY;
                }
            }
            $(event.target).val(this.getSalaryAttributes(fieldName));
        },


        // These methods are not belong to the business logic. They are for the logic of this Vue component.
        initializeData: function() {
            if (this.rootData.new !== true) {
                _.forEach(this.root, (item, key) => {
                    this.root[key] = this.rootData[key];
                });
                _.forEach(this.directlyEditable, (item, key) => {
                    this.directlyEditable[key] = this.rootData[key];
                });
                _.forEach(this.changed, (item, key) => {
                    this.changed[key] = null;
                });
            }
        },
        sendData: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                let data = this.prepareDataToSend(true);
                let url = (this.rootData.new === true) ? '/employee_working_information' : '/employee_working_information/' + this.rootData.id;

                axios.post($.companyCodeIncludedUrl(url), data).then(response => {

                    document.caeru_alert('success', response.data['success']);
                    this.$emit('working-info-saved', this.rootData.id, response.data['new_data'], (this.rootData.new === true), response.data['schedule_transfer_data'][0], response.data['alert_setting_data'][0]);
                    this.sendingRequest = false;
                    if (this.rootData.new !== true) {
                        this.$nextTick(() => {
                            this.initializeData();
                            this.processScheduleTransferData();
                        });
                    }

                }).catch(error => {

                    if (error.response) {
                        document.caeru_alert('error', '');
                    }
                    this.sendingRequest = false;

                });

            }
        },
        resetThisWorkingInfo: function() {
            _.forEach(this.changed, (item, key) => {
                this.changed[key] = null;
            });
            _.forEach(this.directlyEditable, (item, key) => {
                this.directlyEditable[key] = this.root[key];
            });
        },
        deleteThisWorkingInfo: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                var conf = confirm('本当に削除しますか？');
                if (!!conf) {
                    if (this.rootData.new !== true) {

                        let url = '/employee_working_information/' + this.rootData.id;

                        axios.delete($.companyCodeIncludedUrl(url)).then(response => {

                            document.caeru_alert('success', response.data['success']);
                            this.$emit('delete-working-info', this.rootData.id);
                            this.sendingRequest = false;

                        }).catch(error => {

                            if (error.response) {
                                document.caeru_alert('error', '');
                            }
                            this.sendingRequest = false;

                        });

                    } else {
                        this.$emit('delete-working-info', this.rootData.id);
                    }
                }
            }
        },
        toggleDisplaySalaryTable: function() {
            this.display_salary_table = !this.display_salary_table;
        },
        prepareDataToSend: function(newInfo = false) {
            let data = {
                // These three are neccessary for the update's logic not the actual data for the Model itself
                'employee_working_day_id': this.workingDayId,
                'schedule_modified': this.isScheduleModified,
                'salaries_modified': this.isSalariesModified,

                'planned_work_location_id': this.changed['planned_work_location_id'],

                'planned_work_status_id': this.directlyEditable['planned_work_status_id'],
                'planned_rest_status_id': this.directlyEditable['planned_rest_status_id'],
                'paid_rest_time_start': this.directlyEditable['paid_rest_time_start'],
                'paid_rest_time_end': this.directlyEditable['paid_rest_time_end'],
                'note': this.directlyEditable['note'],
                'planned_early_arrive_start': this.directlyEditable['planned_early_arrive_start'],
                'planned_early_arrive_end': this.directlyEditable['planned_early_arrive_end'],
                'planned_late_time': this.directlyEditable['planned_late_time'],

                'planned_break_time': this.changed['planned_break_time'],
                'planned_night_break_time': this.changed['planned_night_break_time'],
                'real_break_time': this.changed['real_break_time'],
                'real_night_break_time': this.changed['real_night_break_time'],

                'planned_early_leave_time': this.directlyEditable['planned_early_leave_time'],
                'planned_overtime_start': this.directlyEditable['planned_overtime_start'],
                'planned_overtime_end': this.directlyEditable['planned_overtime_end'],
            };

            if (this.isScheduleModified === true || newInfo === true) {
                data['schedule_start_work_time'] = (this.computedScheduleStartWorkTime !== null) ? this.computedScheduleStartWorkTime : this.DATE_EMPTY;
                data['schedule_end_work_time'] = (this.computedScheduleEndWorkTime !== null) ? this.computedScheduleEndWorkTime : this.DATE_EMPTY;
                data['schedule_break_time'] = (this.computedScheduleBreakTime !== null) ? this.computedScheduleBreakTime : this.NUM_EMPTY;
                data['schedule_night_break_time'] = (this.computedScheduleNightBreakTime !== null) ? this.computedScheduleNightBreakTime : this.NUM_EMPTY;
                data['schedule_working_hour'] = (this.computedScheduleWorkingHour !== null) ? this.computedScheduleWorkingHour : this.TIME_EMPTY;
                data['planned_work_location_id'] = this.computedPlannedWorkLocationId;
            }

            if (this.isSalariesModified === true || newInfo === true) {
                data['basic_salary'] = (this.getSalaryAttributes('basic_salary') !== null) ? this.getSalaryAttributes('basic_salary') : this.NUM_EMPTY;
                data['night_salary'] = (this.getSalaryAttributes('night_salary') !== null) ? this.getSalaryAttributes('night_salary') : this.NUM_EMPTY;
                data['overtime_salary'] = (this.getSalaryAttributes('overtime_salary') !== null) ? this.getSalaryAttributes('overtime_salary') : this.NUM_EMPTY;
                data['deduction_salary'] = (this.getSalaryAttributes('deduction_salary') !== null) ? this.getSalaryAttributes('deduction_salary') : this.NUM_EMPTY;
                data['night_deduction_salary'] = (this.getSalaryAttributes('night_deduction_salary') !== null) ? this.getSalaryAttributes('night_deduction_salary') : this.NUM_EMPTY;
                data['monthly_traffic_expense'] = (this.getSalaryAttributes('monthly_traffic_expense') !== null) ? this.getSalaryAttributes('monthly_traffic_expense') : this.NUM_EMPTY;
                data['daily_traffic_expense'] = (this.getSalaryAttributes('daily_traffic_expense') !== null) ? this.getSalaryAttributes('daily_traffic_expense') : this.NUM_EMPTY;
            }

            return data;
        },

        // TransferPicker methods
        toggleTransferPicker: function() {
            this.showTranferPicker = !this.showTranferPicker;

            // reposition when open the picker
            if (this.showTranferPicker === true) {
                this.$nextTick(function() {
                    this.repositionByHeight();
                });
            }
        },

        processScheduleTransferData: function() {
            if (this.scheduleTransferData !== null) {

                let nationalHolidays = [];
                let lawRestDay = [];
                let normalRestDay = [];
                let transferableDays = [];

                _.forEach(this.scheduleTransferData['rest_days'], (day) => {
                    if (day['type'] === this.consts.calendar_rest_days['LAW_BASED_REST_DAY']) {
                        lawRestDay.push( _.map(_.split(day['assigned_date'], '-'), (data) => { return _.toInteger(data)}) );
                    } else if (day['type'] === this.consts.calendar_rest_days['NORMAL_REST_DAY']) {
                        normalRestDay.push( _.map(_.split(day['assigned_date'], '-'), (data) => { return _.toInteger(data)}) );
                    }
                });

                nationalHolidays = _.map(this.scheduleTransferData['national_holidays'], (day) => {
                    return _.map(_.split(day, '-'), (data) => { return _.toInteger(data)});
                });
                transferableDays = _.map(this.scheduleTransferData['transferable_days'], (day) => {
                    return _.map(_.split(day, '-'), (data) => { return _.toInteger(data)});
                });
                this.transferPickerOptions = {
                    'year' : this.currentTransferPickerDate[0],
                    'month' : this.currentTransferPickerDate[1],
                    'nationalHolidays' : this.filterByYearAndMonth(nationalHolidays),
                    'lawRestDay' : this.filterByYearAndMonth(lawRestDay),
                    'normalRestDay' : this.filterByYearAndMonth(normalRestDay),
                    'transferableDays' : this.filterByYearAndMonth(transferableDays),
                    'flipColorDay' : this.scheduleTransferData['flip_color_day'],
                    'startColor' : (this.currentTransferPickerDate[1] % 2) === 0,
                    'pickerMode' : true,
                    'transferMode' : true,
                }

            }
        },

        filterByYearAndMonth: function(collection) {
            let filtered = _.filter(collection, (item) => {
                return (item[0] === this.currentTransferPickerDate[0]) && (item[1] === this.currentTransferPickerDate[1]);
            });
            return _.map(filtered, (item) => {return item[2];});
        },
        // propagate the event up one more level.
        scheduleTransfer: function(day) {
            this.$emit('schedule-transfer', day);
        },
        changeCurrentTransferTime: function(year, month) {
            this.currentTransferPickerDate = [year, month];
            this.processScheduleTransferData();
        },


        repositionByHeight: function() {
            let popUp = $('#' + this.rootData.id +'.schedule_transfer .caeru_calendar_date_picker_popup');
            let diffHeight = window.innerHeight - popUp.outerHeight();
            let diffWidth = window.innerWidth - popUp.outerWidth();
            let scrollOffset = $(window).scrollTop();
            popUp.offset({ top: (scrollOffset + diffHeight/2), left: diffWidth/2 });
        }
    },
    filters: {
        reformatTime: (value) => {
            let moment = window.moment(value, ['YYYY-MM-DD HH:mm:ss', 'HH:mm:ss'], true);
            return moment.isValid() ? moment.format('H:mm') : null;
        },
        ensureSpace: function(value) {
            if ((value === null) || (value === undefined)) {
                // it's a freaking japanese-space! (switch your typing to japanese then use space).
                return "　";
            } else {
                return value;
            }
        },
    },
    created: function() {
        this.initializeData();

        // Initialize data for the scheduleTransferPicker
        this.processScheduleTransferData();
    },
    mounted: function() {

        // This is for calculating the position of the pop up
        // About these re-position nonsense. You have to call it once here, then add the event listener here, and also
        // whenever you open the pop up, you have to recalculate the position.
        this.repositionByHeight();
        this.$nextTick(function() {
            window.addEventListener('resize', this.repositionByHeight);
        });
    },
    components: {
        calendar: Calendar,
    },
}
</script>