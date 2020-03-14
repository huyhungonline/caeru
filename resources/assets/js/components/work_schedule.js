// The component
// Vue.component('work-schedule', {
import Hub from './hub.js';
import Autocomplete from './caeru_autocomplete';
import ErrorDisplay from './caeru_error_display';

const hub = Hub;

export default {
    template: `
        <form action="" @submit.prevent="">
            <table>
                <template v-if="editable">
                    <tr>
                        <td>
                            <section class="bottom_10 side_input_block second">
                                <div class="side_input_block right_10"><span class="red">※</span>勤務日は</div>
                                <div class="side_input_block">
                                    <error-display :message="errors.prioritize_company_calendar" extra-class="radioes">
                                        <label class="radio_text right_30"><input type="radio" v-model.number="data.prioritize_company_calendar" value="1">企業カレンダーに従う</label>
                                        <label class="radio_text"><input type="radio" v-model.number="data.prioritize_company_calendar" value="0">オリジナル</label>
                                    </error-display>
                                </div>
                            </section>
                        </td>
                        <td rowspan="2">
                            <section class="unit">
                                <p class="bottom_10"><span class="button"><button class="ss_size s_height btn_greeen" @click="submit">保存</button></span></p>
                                <p class="bottom_10"><span class="button"><a class="ss_size s_height btn_greeen" @click="copy">コピー</a></span></p>
                                <p><span class="button"><a class="ss_size s_height btn_black" @click="remove">削除</a></span></p>
                            </section>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <section class="second">
                                <section class="left unit right_30">
                                    <p>勤務日</p>
                                    <div class="check_box_wrap side_input_block">
                                        <error-display :message="errors.start_date">
                                            <input class="s_size right_10"  v-model="data.start_date" type="text"><span class="right_10 ">〜</span>
                                        </error-display>
                                        <error-display :message="errors.end_date">
                                            <input class="s_size"  v-model="data.end_date" type="text">
                                        </error-display>
                                    </div>
                                </section>
                                <!--
                                    <div class="selectbox left right_10">
                                        <div>&nbsp;</div>
                                        <error-display :message="errors.frequency_type">
                                            <select class="s_size" v-model="data.frequency_type">
                                                <option v-for="(type, index) in frequencyTypes" :value="index">{{ type }}</option>
                                            </select>
                                        </error-display>
                                    </div>
                                -->
                                 <section class="side_input_block unit right_10">
                                    <p>月</p>
                                    <div class="check_onle_wrap">
                                        <error-display :message="errors.working_days_of_week[0]" extra-class="checkboxes">
                                            <label class="checkbox_box">
                                                <input type="checkbox" :true-value="1" :false-value="0" v-model="data.working_days_of_week[0]">
                                            </label>
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p>火</p>
                                    <div class="check_onle_wrap">
                                        <error-display :message="errors['working_days_of_week.1']" extra-class="checkboxes">
                                            <label class="checkbox_box">
                                                <input type="checkbox" :true-value="1" :false-value="0" v-model="data.working_days_of_week[1]">
                                            </label>
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p>水</p>
                                    <div class="check_onle_wrap">
                                        <error-display :message="errors['working_days_of_week.2']" extra-class="checkboxes">
                                            <label class="checkbox_box">
                                                <input type="checkbox" :true-value="1" :false-value="0" v-model="data.working_days_of_week[2]">
                                            </label>
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p>木</p>
                                    <div class="check_onle_wrap">
                                        <error-display :message="errors['working_days_of_week.3']" extra-class="checkboxes">
                                            <label class="checkbox_box">
                                                <input type="checkbox" :true-value="1" :false-value="0" v-model="data.working_days_of_week[3]">
                                            </label>
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p>金</p>
                                    <div class="check_onle_wrap">
                                        <error-display :message="errors['working_days_of_week.4']" extra-class="checkboxes">
                                            <label class="checkbox_box">
                                                <input type="checkbox" :true-value="1" :false-value="0" v-model="data.working_days_of_week[4]">
                                            </label>
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p>土</p>
                                    <div class="check_onle_wrap">
                                        <error-display :message="errors['working_days_of_week.5']" extra-class="checkboxes">
                                            <label class="checkbox_box">
                                                <input type="checkbox" :true-value="1" :false-value="0" v-model="data.working_days_of_week[5]">
                                            </label>
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p>日</p>
                                    <div class="check_onle_wrap">
                                        <error-display :message="errors['working_days_of_week.6']" extra-class="checkboxes">
                                            <label class="checkbox_box">
                                                <input type="checkbox" :true-value="1" :false-value="0" v-model="data.working_days_of_week[6]">
                                            </label>
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_30">
                                    <p>祝日は休む</p>
                                    <div class="check_onle_wrap">
                                        <error-display :message="errors.rest_on_holiday" extra-class="checkboxes">
                                            <label class="checkbox_box">
                                                <input type="checkbox" :true-value="1" :false-value="0" v-model="data.rest_on_holiday">
                                            </label>
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p><span class="red" v-if="displayEmployee || !!data.work_address_id">※</span>出勤</p>
                                    <div class="side_input_block">
                                        <error-display :message="errors.start_work_time">
                                            <input class="ss_size"  :value="data.start_work_time | dropTheSecond" @input="data.start_work_time = $event.target.value" type="text">
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p><span class="red" v-if="displayEmployee || !!data.work_address_id">※</span>退勤</p>
                                    <div class="side_input_block">
                                        <error-display :message="errors.end_work_time">
                                            <input class="ss_size"  :value="data.end_work_time | dropTheSecond" @input="data.end_work_time = $event.target.value" type="text">
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p><span class="red">※</span>休憩</p>
                                    <div class="side_input_block">
                                        <error-display :message="errors.break_time">
                                            <input class="ss_size"  v-model="data.break_time" type="text">
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p>(内)深休</p>
                                    <div class="side_input_block">
                                        <error-display :message="errors.night_break_time">
                                            <input class="ss_size"  v-model="data.night_break_time" type="text">
                                        </error-display>
                                    </div>
                                </section>
                                <section class="side_input_block unit right_10">
                                    <p><span class="red">※</span>所定</p>
                                    <div class="side_input_block">
                                        <error-display :message="errors.working_hour">
                                            <input class="ss_size"  :value="data.working_hour | dropTheSecond" @input="data.working_hour = $event.target.value" type="text">
                                        </error-display>
                                    </div>
                                </section>
                            </section>
                            <section class="second dash_line">
                                <section class="right_10 side_input_block" v-if="!displayEmployee">
                                    <div class="m_size">&nbsp;</div>
                                    <div class="selectbox side_input_block">
                                        <error-display :message="errors.work_location_id">
                                            <select class="m_size"  v-model="data.work_location_id">
                                                <option v-for="workLocation in workLocations" :value="workLocation.id">{{ workLocation.name }}</option>
                                            </select>
                                        </error-display>
                                    </div>
                                    <div class="bottom_10 m_size">&nbsp;</div>
                                </section>
                                <section class="right_10 side_input_block" v-if="displayAddress || displayEmployee">
                                    <div>&nbsp;</div>
                                    <div class="selectbox side_input_block right_10 top_position">
                                        <error-display :message="errors.candidating_type">
                                            <select class="s_size" v-model.number="data.candidating_type">
                                                <option value="" v-if="!displayEmployee"></option>
                                                <option value="0">固定</option>
                                                <option value="1">候補</option>
                                            </select>
                                        </error-display>
                                    </div>
                                    <div class="side_input_block">
                                        <p class="bottom_10" v-if="!displayEmployee">
                                            <span class="right_10 side_input_block">訪問先</span>
                                            <error-display :message="errors.work_address_id">
                                                <autocomplete :suggestions="autocompleteData" custom-class="m_size" :initial-id="autocompleteInitId"
                                                    @selected="autocompleteResult"
                                                    @enter-pressed="submit"
                                                ></autocomplete>
                                            </error-display>
                                        </p>
                                        <p class="bottom_10" v-if="displayEmployee">
                                            <span class="right_10 side_input_block">勤務者</span>
                                            <error-display :message="errors.employee_id">
                                                <autocomplete :suggestions="autocompleteData" custom-class="m_size" :initial-id="autocompleteInitId"
                                                    @selected="autocompleteResult"
                                                    @enter-pressed="submit"
                                                ></autocomplete>
                                            </error-display>
                                        </p>
                                        <p>
                                            <span class="right_10 side_input_block">必要人数</span>
                                                <error-display :message="errors.candidate_number">
                                                    <input class="right_4 ss_size40" v-if="data.candidating_type!=0" v-model.number="data.candidate_number" type="text">
                                                    <input class="right_4 ss_size40" v-else disabled v-model.number="data.candidate_number" type="text">
                                                </error-display>
                                            <span class="side_input_block">人</span>
                                        </p>
                                    </div>
                                </section>
                                    <div class="right_4 side_input_block">
                                        <div>&nbsp;</div>
                                        <div class="bottom_10">
                                            <span class="side_input_block right_10"></span>
                                            <div class="selectbox side_input_block enpty_box">
                                                <error-display :message="errors.normal_salary_type">
                                                    <select class="s_size"  v-model="data.normal_salary_type">
                                                        <option v-for="(item, index) in salaryTypes" :value="index">{{ item }}</option>
                                                    </select>
                                                </error-display>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="side_input_block right_10">祝日</span>
                                            <div class="selectbox side_input_block">
                                                <error-display :message="errors.holiday_salary_type">
                                                    <select class="s_size"  v-model="data.holiday_salary_type">
                                                        <option v-for="(item, index) in salaryTypes" :value="index">{{ item }}</option>
                                                    </select>
                                                </error-display>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="right_10 side_input_block">
                                        <div class="bottom_10">
                                            <div class="right_4 side_input_block unit">
                                                <p>基本給</p>
                                                <error-display :message="errors.normal_salary">
                                                    <input class="s_size side_input_block"  v-model="data.normal_salary" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                        <div>
                                            <div class="right_4 side_input_block unit">
                                                <error-display :message="errors.holiday_salary">
                                                    <input class="s_size side_input_block"  v-model="data.holiday_salary" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                    </div>
                                    <div class="right_10 left_10 side_input_block">
                                        <div class="bottom_10">
                                            <div class="right_4 side_input_block unit">
                                                <p>深夜</p>
                                                <error-display :message="errors.normal_night_salary">
                                                    <input class="ss_size side_input_block"  v-model="data.normal_night_salary" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                        <div>
                                            <div class="right_4 side_input_block unit">
                                                <error-display :message="errors.holiday_night_salary">
                                                    <input class="ss_size side_input_block"  v-model="data.holiday_night_salary" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                    </div>
                                    <div class="right_10 left_10 side_input_block">
                                        <div class="bottom_10">
                                            <div class="right_4 side_input_block unit">
                                                <p>残業</p>
                                                <error-display :message="errors.normal_overtime_salary">
                                                    <input class="ss_size side_input_block"  v-model="data.normal_overtime_salary" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                        <div>
                                            <div class="right_4 side_input_block">
                                                <error-display :message="errors.holiday_overtime_salary">
                                                    <input class="ss_size side_input_block"  v-model="data.holiday_overtime_salary" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                    </div>
                                    <div class="right_10 left_10 side_input_block">
                                        <div class="bottom_10">
                                            <div class="right_4 side_input_block unit">
                                                <p>控除</p>
                                                <error-display :message="errors.normal_deduction_salary">
                                                    <input class="ss_size side_input_block"  v-model="data.normal_deduction_salary" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                        <div>
                                            <div class="right_4 side_input_block">
                                                <error-display :message="errors.holiday_deduction_salary">
                                                    <input class="ss_size side_input_block"  v-model="data.holiday_deduction_salary" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                    </div>
                                    <div class="right_30 left_10 side_input_block">
                                        <div class="bottom_10">
                                            <div class="right_4 side_input_block unit">
                                                <p>深夜控除</p>
                                                <error-display :message="errors.normal_night_deduction_salary">
                                                    <input class="ss_size side_input_block"  v-model="data.normal_night_deduction_salary" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                        <div>
                                            <div class="right_4 side_input_block">
                                                <error-display :message="errors.holiday_night_deduction_salary">
                                                    <input class="ss_size side_input_block"  v-model="data.holiday_night_deduction_salary" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                    </div>
                                <section class="unit side_input_block">
                                    <div>
                                        <div class="bottom_10">
                                            <span class="side_input_block right_10 bottom_position">月額</span>
                                            <div class="right_4 side_input_block">
                                                <div>交通費</div>
                                                <error-display :message="errors.monthly_traffic_expense">
                                                    <input class="ss_size side_input_block"  v-model="data.monthly_traffic_expense" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                        <div>
                                            <span class="side_input_block right_10 bottom_position">日額</span>
                                            <div class="right_4 side_input_block">
                                                <error-display :message="errors.daily_traffic_expense">
                                                    <input class="ss_size side_input_block"  v-model="data.daily_traffic_expense" type="text">
                                                </error-display>
                                            </div>
                                            <span class="side_input_block bottom_position">円</span>
                                        </div>
                                    </div>
                                </section>
                            </section>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr>
                        <td>
                            <section class="second">
                                <p v-if="data.prioritize_company_calendar">勤務日は企業カレンダーに従う</p>
                                <p v-else>オリジナル</p>
                            </section>
                            <section class="second dash_line">
                                <p class="right_30 side_input_block"><span class="right_10">勤務日</span><span>{{ data.start_date }}〜{{ data.end_date }}</span></p>
                            </section>
                            <section class="second dash_line">
                                <p class="right_30 side_input_block"> <!-- <span class="right_10">{{ frequencyTypes[data.frequency_type] }}</span> --> <span>{{ working_days_of_week_string }}</span></p>
                            </section>
                            <section class="second dash_line">
                                <p class="right_30 side_input_block"><span class="right_10">出勤</span><span>{{ data.start_work_time | dropTheSecond }}</span></p>
                                <p class="right_30 side_input_block"><span class="right_10">退勤</span><span>{{ data.end_work_time | dropTheSecond }}</span></p>
                                <p class="right_30 side_input_block"><span class="right_10">休憩</span><span>{{ data.break_time }}</span></p>
                                <p class="right_30 side_input_block"><span class="right_10">(内)深夜</span><span>{{ data.night_break_time }}</span></p>
                                <p class="right_30 side_input_block"><span class="right_10">所定</span><span>{{ data.working_hour | dropTheSecond }}</span></p>
                            </section>
                            <section class="second dash_line">
                                <template v-if="!displayEmployee">
                                    <p class="right_30 side_input_block"><span class="right_10">勤務地</span><span>{{ workLocationName }}</span></p>
                                    <template v-if="displayAddress && (data.candidating_type !==null)">
                                        <p class="right_30 side_input_block">
                                            <span class="right_10" v-if="data.candidating_type">候補</span>
                                            <span class="right_10" v-else>固定</span>
                                            <span class="right_10" v-if="data.candidating_type">必要人数</span><span>{{ data.candidate_number }}</span>
                                        </p>
                                        <p class="right_30 side_input_block"><span class="right_10">訪問先</span><span>{{ autocompleteData[autocompleteInitId].name }}</span></p>
                                    </template>
                                </template>
                                <template v-else>
                                    <p class="right_30 side_input_block">
                                        <span class="right_10" v-if="data.candidating_type">候補</span>
                                        <span class="right_10" v-else>固定</span>
                                        <span class="right_10">必要人数</span><span>{{ data.candidate_number }}</span>
                                    </p>
                                    <p class="right_30 side_input_block"><span class="right_10">勤務者</span><span>{{ autocompleteData[autocompleteInitId].name }}</span></p>
                                </template>
                            </section>
                            <section class="second dashline">
                                <p class="right_30 side_input_block"><span class="right_10">{{ salaryTypes[data.normal_salary_type] }}</span><span class="right_10">基本給</span><span>{{ data.normal_salary }}</span>円</p>
                                <p class="right_30 side_input_block"><span class="right_10">深夜</span><span>{{ data.normal_night_salary }}</span>円</p>
                                <p class="right_30 side_input_block"><span class="right_10">残業</span><span>{{ data.normal_overtime_salary }}</span>円</p>
                                <p class="right_30 side_input_block"><span class="right_10">控除</span><span>{{ data.normal_deduction_salary }}</span>円</p>
                                <p class="right_30 side_input_block"><span class="right_10">深夜控除</span><span>{{ data.normal_night_deduction_salary }}</span>円</p>
                                <p class="right_10 left_30 side_input_block">祝日</p>
                                <p class="right_30 side_input_block"><span class="right_10">{{ salaryTypes[data.holiday_salary_type] }}</span><span class="right_10">基本給</span><span>{{ data.holiday_salary }}</span>円</p>
                                <p class="right_30 side_input_block"><span class="right_10">深夜</span><span>{{ data.holiday_night_salary }}</span>円</p>
                                <p class="right_30 side_input_block"><span class="right_10">残業</span><span>{{ data.holiday_overtime_salary }}</span>円</p>
                                <p class="right_30 side_input_block"><span class="right_10">控除</span><span>{{ data.holiday_deduction_salary }}</span>円</p>
                                <p class="right_30 side_input_block"><span class="right_10">深夜控除</span><span>{{ data.holiday_night_deduction_salary }}</span>円</p>
                            </section>
                            <section class="second dash_line">
                                <p class="right_30 side_input_block">
                                    <span class="right_10">交通費</span>
                                    <template v-if="!!data.monthly_traffic_expense">
                                        <span class="right_10">月額</span>
                                        <span>{{ data.monthly_traffic_expense }}円</span>
                                    </template>
                                    <template v-if="!!data.daily_traffic_expense">
                                        <span class="right_10">日額</span>
                                        <span>{{ data.daily_traffic_expense }}円</span>
                                    </template>
                                </p>
                            </section>
                        </td>
                    </tr>
                </template>
            </table>
        </form>
    `,
    props: ['presentationData', 'employeeId', 'workAddressId', 'workLocationId', 'modelData'],
    data: function() {
        return {
            data: {
                prioritize_company_calendar: 1,
                start_date: null,
                end_date: null,
                // frequency_type: null,
                working_days_of_week: [0, 0, 0, 0, 0, 0, 0],
                rest_on_holiday: 0,
                start_work_time: null,
                end_work_time: null,
                break_time: null,
                night_break_time: null,
                working_hour: null,
                work_location_id: null,
                normal_salary_type: null,
                normal_salary: null,
                normal_night_salary: null,
                normal_overtime_salary: null,
                normal_deduction_salary: null,
                normal_night_deduction_salary: null,
                holiday_salary_type: null,
                holiday_salary: null,
                holiday_night_salary: null,
                holiday_overtime_salary: null,
                holiday_deduction_salary: null,
                holiday_night_deduction_salary: null,
                monthly_traffic_expense: null,
                daily_traffic_expense: null,
                candidate_number: null,
                candidating_type: null,
                work_address_id: null,
                employee_id: null,
            },
            errors: {
                prioritize_company_calendar: null,
                start_date: null,
                end_date: null,
                // frequency_type: null,
                working_days_of_week: {},
                rest_on_holiday: null,
                start_work_time: null,
                end_work_time: null,
                break_time: null,
                night_break_time: null,
                working_hour: null,
                work_location_id: null,
                normal_salary_type: null,
                normal_salary: null,
                normal_night_salary: null,
                normal_overtime_salary: null,
                normal_deduction_salary: null,
                normal_night_deduction_salary: null,
                holiday_salary_type: null,
                holiday_salary: null,
                holiday_night_salary: null,
                holiday_overtime_salary: null,
                holiday_deduction_salary: null,
                holiday_night_deduction_salary: null,
                monthly_traffic_expense: null,
                daily_traffic_expense: null,
                candidate_number: null,
                candidating_type: null,
                work_address_id: null,
                employee_id: null,
            },
            days_of_week: ['月', '火', '水', '木', '金', '土', '日'],
            wait: 0,
            sendingRequest: false,
        }
    },
    computed: {
        // The types use for the select fields and what not
        // frequencyTypes: function() {
        //     return this.presentationData.frequency_types;
        // },
        workLocations: function() {
            return this.presentationData.work_locations;
        },
        salaryTypes: function() {
            return this.presentationData.salary_types;
        },

        // The boolean for various usage of this component
        displayAddress: function() {
            return this.presentationData.display_address;
        },
        displayEmployee: function() {
            return this.presentationData.display_employee;
        },
        editable: function() {
            return !!this.presentationData.editable;
        },

        // The data for the autocomplete component
        autocompleteData: function() {
            return (!!this.presentationData.autocomplete_data && this.presentationData.autocomplete_data.constructor === Array) ? this.presentationData.autocomplete_data : [];
        },
        autocompleteInitId: function() {
            var search_id = null;
            search_id = (!!this.displayEmployee) ? this.data.employee_id : this.data.work_address_id;
            if (!!search_id) {
                return _.findIndex(this.autocompleteData, object => {
                    return object.id == search_id
                })
            }
        },

        // These variable are use for the interface when the editable = false
        working_days_of_week_string: function() {
            var array = [];
            for (var i in this.data.working_days_of_week) {
                if (this.data.working_days_of_week[i] == 1) {
                    array.push(this.days_of_week[i]);
                }
            };
            if (!!this.data.rest_on_holiday) array.push('祝日は休む');
            return array.join('・');
        },
        workLocationName: function() {
            for (var i in this.workLocations) {
                if (this.workLocations[i].id == this.data.work_location_id)
                    return this.workLocations[i].name;
            }
            return '';
        }
    },
    filters: {
        dropTheSecond: function(time_string) {
            let time = _.split(time_string, ':');

            return (!!time[1]) ? (time[0] + ':' + time[1]) : time_string;
        },
    },
    methods: {
        initialize: function() {
            if (this.modelData.new == "new") {
                // this.data.frequency_type = Object.keys(this.presentationData.frequency_types)[0];
            } else {
                for (var key in this.data) {
                    this.data[key] = this.modelData[key];
                };
                if (this.modelData.new != "copy") {
                    this.data.id = this.modelData.id;
                }
            }
        },
        submit: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                // initialize data
                var url = '/schedule';
                this.data["employee_id"] = (!!this.employeeId) ? this.employeeId : this.data["employee_id"];
                this.data["work_address_id"] = (!!this.workAddressId) ? this.workAddressId : this.data["work_address_id"];
                this.data["work_location_id"] = (!!this.workLocationId) ? this.workLocationId : this.data["work_location_id"];

                // If there is an id in the data, then this will be an update request, else it will be a create new request
                if (this.data.id != undefined) {
                    url = url + '/' + this.data.id;
                }

                axios.post($.companyCodeIncludedUrl(url), this.data).then(response => {
                    document.caeru_alert('success', response.data['success']);
                    this.data.id = response.data['id'];
                    this.showError(null);
                    hub.$emit('saved', this.modelData.id);
                    this.sendingRequest = false;
                }).catch(error => {
                    if (error.response) {
                        document.caeru_alert('error', '');
                        this.showError(null);
                        this.showError(error.response.data);
                    }
                    this.sendingRequest = false;
                })

            }
        },
        remove: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                var vue = this;
                if (this.data.id != undefined) {
                    var conf = confirm('本当に削除しますか？');
                    if (!!conf) {
                        axios.delete($.companyCodeIncludedUrl('/schedule/') + this.data.id).then(function(response) {
                            document.caeru_alert('success', response.data['success']);
                            vue.$emit("remove");
                            this.sendingRequest = false;
                        }).catch(function(error) {
                            if (error.response) {
                                document.caeru_alert('error', '');
                            }
                            this.sendingRequest = false;
                        });
                    }
                } else {
                    vue.$emit("remove");
                }

            }
        },
        copy: function() {
            this.$emit("copy", this.data, this.modelData.id);
        },
        showError: function(returnedErrors) {
            if (returnedErrors != null) {
                for (var key in returnedErrors) {
                    this.errors[key] = returnedErrors[key][0];
                }
            } else {
                for (var key in this.errors) {
                    this.errors[key] = (key == "working_days_of_week") ? {} : null;
                }
            }
        },

        // get the result from the autocomplete component
        autocompleteResult: function(id) {
            if (!!this.displayEmployee) {
                this.data.employee_id = (id !== null) ? this.autocompleteData[id].id : null;
            } else {
                this.data.work_address_id = (id !== null) ? this.autocompleteData[id].id : null;
                this.data.work_location_id = (id !== null) ? this.autocompleteData[id].work_location_id : this.data.work_location_id;
            }
        },

        // Calculate the working_hour
        calculateWorkTime: function() {
            var startTime = this.getValidTime(this.data.start_work_time);
            var endTime = this.getValidTime(this.data.end_work_time);

            if (!!startTime && !!endTime) {
                // If the endTime is smaller then the startTime then it's from the next day
                if (endTime.isBefore(startTime)) {
                    endTime.add(1, 'days');
                }

                var breakTime = _.toSafeInteger(this.data.break_time);
                var workingMinutes = endTime.diff(startTime, 'minutes') - breakTime;
                if (workingMinutes > 0) {
                    this.data.working_hour = _.floor(workingMinutes/60) + ':' + _.padStart(workingMinutes%60, 2, '0');
                }
            }
        },

        // Get a valid Date() object from a time string. In the case of invalid string, return false.
        getValidTime: function(string) {

            let time = moment(string, ['Hm', 'H:m', 'H:m:s'], true);

            if (time.isValid())
                return time;

            return false;
        },
    },
    watch: {
        data: {
            handler: function(newData) {
                //We have to ignore the first tick of the watcher, because this tick is due to the first change when the component is rendered
                if (this.wait == 0) {
                    this.wait ++;
                } else {
                    hub.$emit('changed', this.modelData.id);
                }
            },
            deep: true
        },

        // When the start_work_time and end_work_time are provided, anychange to these fields will provoke the function
        // to re-calculate the working_hour.
        'data.start_work_time': function(value) {
            if (!!value && !!this.data.end_work_time) this.calculateWorkTime();
        },
        'data.end_work_time': function(value) {
            if (!!value  && !!this.data.start_work_time) this.calculateWorkTime();
        },
        'data.break_time': function() {
            if (!!this.data.start_work_time && !!this.data.end_work_time) this.calculateWorkTime();
        },
    },
    // updated: function() {
    //     console.log("change here: " + this.modelData.id);
    // },
    // mounted: function() {
    //     this.initialize();
    //     console.log("stablilize this: " + this.modelData.id);
    // },
    created: function() {
        // Initialize the data
        this.initialize();

        // Register event handler
        hub.$on('submit-schedule', this.submit);
    },
    beforeDestroy: function() {
        // Get rid of the event handler
        hub.$off('submit-schedule', this.submit);
    },
    components: {
        'error-display': ErrorDisplay,
        'autocomplete': Autocomplete,
    },
};
