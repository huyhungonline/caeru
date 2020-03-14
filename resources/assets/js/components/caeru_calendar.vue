<template>
    <section class="caeru_calendar_wrapper">
        <div :class="{caeru_calendar_date_picker_popup: pickerMode, select_calendar: pickerMode}">
            <section class="year_month_select" v-if="pickerMode">
                <div class="selectbox left right_10 left_10">
                    <select class="s_size" :value="main.year" @input="changeYeah($event.target.value)">
                        <option v-for="year in yearRange" :value="year">{{ year }}年</option>
                    </select>
                </div>
                <div class="selectbox left">
                    <select class="s_size" :value="main.month" @input="changeMonth($event.target.value)">
                        <option v-for="month in monthRange" :value="month">{{ month }}月</option>
                    </select>
                </div>
                <section class="right right_10">
                    <div class="arrow_gray left right_10">
                        <a @click="previousMonth"><img src="../../../../public/images/ico_arrow_left_gray.svg"></a>
                    </div>
                    <div class="arrow_gray left">
                        <a @click="nextMonth"><img src="../../../../public/images/ico_arrow_right_gray.svg"></a>
                    </div>
                </section>
            </section>
            <section>
                <table>
                    <tr v-if="!pickerMode">
                        <th colspan="7">
                            <div class="calender_wrap">
                                <span>{{ main.month }}月</span>
                                <div class="side_input_block">
                                    <input v-if="editable" class="ss_size40" v-model="child.flexTotalTime" type="text">
                                    <span v-else>{{ child.flexTotalTime }}</span>
                                    <span v-if="editable || child.flexTotalTime !== null">時間</span>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <td class="sunday text_bold">日</td>
                        <td class="text_bold">月</td>
                        <td class="text_bold">火</td>
                        <td class="text_bold">水</td>
                        <td class="text_bold">木</td>
                        <td class="text_bold">金</td>
                        <td class="saturday text_bold">土</td>
                    </tr>
                    <tr v-for="(week, wid) in month_map" v-if="lastWeekFirstDayNotNull(wid)">
                        <template v-if="!pickerMode">
                            <td :class="dayProperty(day, did)" v-for="(day, did) in week" @click="toggleDay(day)">{{ !!day ? day : '' }}</td>
                        </template>
                        <template v-else-if="pickerMode && !transferMode">
                            <td :class="dayProperty(day, did)" v-for="(day, did) in week" @click="chooseDay(day)">{{ !!day ? day : '' }}</td>
                        </template>
                        <template v-else>
                            <td :class="dayProperty(day, did)" v-for="(day, did) in week">
                                <p v-if="isTransferable(day) && !isTransferDay(day)"><a @click="pickTransferDay(day)" class="change_possible candidate">{{ !!day ? day : '' }}</a></p>
                                <p v-else-if="isTransferDay(day)" class="change_possible selected">{{ !!day ? day : '' }}</p>
                                <template v-else>{{ !!day ? day : '' }}</template>
                            </td>
                        </template>
                    </tr>
                </table>
            </section>
            <section class="btn"  v-if="pickerMode">
                <p class="button right_30" v-if="transferMode"><a class="s_size m_height btn_greeen" @click="transferClicked">振り替え</a></p>
                <p class="button"><a class="transfer_cloase s_size m_height btn_gray" @click="cancelClicked">キャンセル</a></p>
            </section>
        </div>
    </section>
</template>
<script>
export default {
    data: function() {
        return {
            month_map: [
                [null, null, null, null, null, null, null],
                [null, null, null, null, null, null, null],
                [null, null, null, null, null, null, null],
                [null, null, null, null, null, null, null],
                [null, null, null, null, null, null, null],
                [null, null, null, null, null, null, null],
            ],
            main : {
                year: null,
                month: null,
            },
            child : {
                lawRestDay:     [],
                normalRestDay:  [],
                flexTotalTime : null,
            },

            // The rest days types, these numbers should be the same with those in the CalendarRestDay Model
            restDayTypes: {
                lawRestDay: 1,
                normalRestDay: 2,
                noRestDay: 0
            },

            currentTransferDay: null,
        }
    },
    props: {
        year: {
            type: Number,
            required: true
        },
        month: {
            type: Number,
            required: true,
        },
        pickerMode: {
            type: Boolean,
            required: true,
        },
        nationalHolidays: {
            type: Array,
            required: false,
        },
        lawRestDay: {
            type: Array,
            required: false,
        },
        normalRestDay: {
            type: Array,
            required: false,
        },
        flexTotalTime: {
            type: Number,
            required: false,
            default: null,
        },
        editable: {
            type: Boolean,
            required: false,
            default: false,
        },

        // True means white, otherwise gray
        startColor: {
            type: Boolean,
            required: false,
            default: true,
        },
        flipColorDay: {
            type: Number,
            required: false,
            default: 0,
        },

        // From here on, these options are for picker mode
        transferableDays: {
            type: Array,
            required: false,
        },
        transferMode: {
            type: Boolean,
            required: false,
            default: false,
        },
        // To limit the navigation range
        startDate: {
            type: String,
            required: false,
        },
        endDate: {
            type: String,
            required: false,
        },


    },
    computed: {
        flipColorRealDay: function() {
            if (this.flipColorDay === 0) {
                var last_day = new Date(this.main.year, this.main.month, 1);
                last_day.setDate(last_day.getDate()-1);
                return last_day.getDate();
            } else 
                return this.flipColorDay;
        },
        yearRange: function() {
            if (this.transferMode === true && this.startDate !== null && this.endDate !== null) {
                let start = moment(this.startDate);
                let end = moment(this.endDate);

                return _.range(start.year(), (end.year() + 1));
            } else {
                return _.range(this.main.year - 5, this.main.year + 10);
            }
        },
        monthRange: function() {
            if (this.transferMode === true && this.startDate !== null && this.endDate !== null) {
                let start = moment(this.startDate);
                let end = moment(this.endDate);

                if (this.main.year === start.year() && this.main.year === end.year()) {
                    return _.range((start.month() + 1), (end.month() + 2));

                } else if (this.main.year === start.year() && this.main.year !== end.year()) {
                    return _.range((start.month() + 1), 13);

                } else if (this.main.year === end.year()) {
                    return _.range(1, (end.month() + 2));

                } else if (start.year() < this.main.year && this.main.year < end.year()) {
                    return 12;

                } else {
                    return [];
                }

            } else {
                return 12;
            }
        },
        currentFullTransferDay: function() {
            return (this.currentTransferDay !== null) ? this.main.year + '-' + this.main.month + '-' + this.currentTransferDay : null;
        },
    },
    methods: {
        initMap: function() {
            if ((this.main.year !== null) && (this.main.month !== null)) {
                var date = new Date(this.main.year, this.main.month-1, 1);
                var week = 0;
                while(date.getMonth() == this.main.month-1) {
                    var dow = date.getDay(); // day of week
                    this.month_map[week][dow] = date.getDate();
                    week = (dow === 6) ? week+1 : week;
                    date.setDate(date.getDate()+1);
                }
            }
        },

        // Check if the row for the last week is neccessary
        lastWeekFirstDayNotNull: function(weekId) {
            return (weekId !== 5) || !!this.month_map[5][0];
        },

        // change the color of the day base on that day's properties
        dayProperty: function(day, did) {
            return {
                pointable:      day !== null, 
                saturday:       did === 6,
                sunday:         (did === 0) || _.includes(this.nationalHolidays, day),
                pink_holiday:   _.includes(this.child.lawRestDay, day),
                blue_holiday:   _.includes(this.child.normalRestDay, day),
                closing_date:   (day !== null) && ((!!this.startColor && day  > this.flipColorRealDay) || (!this.startColor && day  <= this.flipColorRealDay)),
            }
        },
        resetMonthMap: function() {
            this.month_map = [
                [null, null, null, null, null, null, null],
                [null, null, null, null, null, null, null],
                [null, null, null, null, null, null, null],
                [null, null, null, null, null, null, null],
                [null, null, null, null, null, null, null],
                [null, null, null, null, null, null, null],
            ];
        },

        // Return the state of that day:
        //  1 -> law rest day
        //  2 -> normal rest day
        //  0 -> normal day
        dayState: function(day) {
            if (_.includes(this.child.lawRestDay, day))
                return this.restDayTypes.lawRestDay;
            else if (_.includes(this.child.normalRestDay, day))
                return this.restDayTypes.normalRestDay;
            else
                return this.restDayTypes.noRestDay;
        },

        // Normal mode: click on a day to change the status of that rest day
        // Toggle the state of that day and send the data outside
        toggleDay: function(day) {
            if (day !== null && !!this.editable) {
                // toggle
                if (this.dayState(day) === this.restDayTypes.noRestDay)
                    this.child.lawRestDay.push(day);
                else if (this.dayState(day) === this.restDayTypes.lawRestDay) {
                    this.child.lawRestDay.splice(_.indexOf(this.child.lawRestDay, day), 1);
                    this.child.normalRestDay.push(day);
                }
                else
                    this.child.normalRestDay.splice(_.indexOf(this.child.normalRestDay, day), 1);

                // send the data outside
                var data = {
                    day: this.main.year + '-' + this.main.month + '-' + day,
                    status: this.dayState(day),
                }
                this.$emit('rest-day-changed', data);
            }
        },
        assignData: function() {
            this.main.year              = this.year;
            this.main.month             = this.month;
            this.child.lawRestDay       = this.lawRestDay;
            this.child.normalRestDay    = this.normalRestDay;
            this.child.flexTotalTime    = this.flexTotalTime;
            if (this.transferMode === true) {
                this.transferableDays
            }
        },


        // Normal Picker mode: click on a day to choose that day
        chooseDay: function(day) {
            this.$emit('day-chose', this.main.year + '-' + _.padStart(this.main.month, 2, '0') + '-' + _.padStart(day, 2, '0'));
        },
        nextMonth: function() {

            if (this.transferMode === true && this.startDate !== null && this.endDate !== null) {
                let end = moment(this.endDate);
                if (this.main.year === end.year() && this.main.month === end.month() + 1) {
                    return;
                }

            }
            if (this.main.month == 12) {
                this.main.month = 1;
                this.main.year += 1;
                this.$nextTick(()=> {
                    this.$forceUpdate();
                })
            } else {
                this.main.month += 1;
            }
            this.changeCurrentTimeNavigationBar();

        },
        previousMonth: function() {

            if (this.transferMode === true && this.startDate !== null && this.endDate !== null) {
                let start = moment(this.startDate);
                if (this.main.year === start.year() && this.main.month === start.month() + 1) {
                    return;
                }
            }
            if (this.main.month == 1) {
                this.main.month = 12;
                this.main.year -= 1;
                this.$nextTick(()=> {
                    this.$forceUpdate();
                })
            } else {
                this.main.month -= 1;
            }
            this.changeCurrentTimeNavigationBar();

        },
        changeYeah: function(value) {

            if (this.transferMode === true && this.startDate !== null && this.endDate !== null) {
                let start = moment(this.startDate);
                let end = moment(this.endDate);

                if (value == end.year() && this.main.month > end.month() + 1) {
                    this.main.year = end.year();
                    this.main.month = end.month() + 1;
                } else if (value == start.year() && this.main.month < start.month() + 1) {
                    this.main.year = start.year();
                    this.main.month = start.month() + 1;
                }
                this.changeCurrentTimeNavigationBar();

            } else {
                this.main.year = _.toInteger(value);
                this.changeCurrentTimeNavigationBar();
            }
            this.$nextTick(()=> {
                this.$forceUpdate();
            })

        },
        changeMonth: function(value) {

            if (this.transferMode === true && this.startDate !== null && this.endDate !== null) {
                let start = moment(this.startDate);
                let end = moment(this.endDate);

                if (this.main.year === end.year() && _.toInteger(value) > end.month() + 1) {
                    this.main.year = end.year();
                    this.main.month = end.month() + 1;
                } else if (this.main.year === start.year() && _.toInteger(value) < start.month() + 1) {
                    this.main.year = start.year();
                    this.main.month = start.month() + 1;
                }
                this.changeCurrentTimeNavigationBar();

            } else {
                this.main.month = _.toInteger(value);
                this.changeCurrentTimeNavigationBar();
            }
            this.$nextTick(()=> {
                this.$forceUpdate();
            })

        },


        // Transfer Picker mode:
        isTransferable: function(day) {
            return _.includes(this.transferableDays, day);
        },
        isTransferDay: function(day) {
            return (this.currentTransferDay !== null) && (day === this.currentTransferDay);
        },
        pickTransferDay: function(day) {
            this.currentTransferDay = day;
        },
        transferClicked: function() {
            if (this.currentTransferDay !== null) {
                this.$emit('schedule-transfer', this.currentFullTransferDay);
            }
        },
        changeCurrentTimeNavigationBar: function() {
            if (this.transferMode === true) {
                this.currentTransferDay = null;
                this.$emit('change-current-transfer-time', this.main.year, this.main.month);
            } else {
                this.$emit('change-current-time', this.main.year, this.main.month);
            }
        },
        cancelClicked: function() {
            this.currentTransferDay = null;
            this.$emit('date-picker-cancel');
        }

    }, 
    watch: {
        'child.flexTotalTime': function() {
            var data = {
                month: this.main.year + '-' + this.main.month,
                time:   this.child.flexTotalTime,
            }
            this.$emit('flex-total-time-changed', data);
        },
        main: {
            handler: function() {
                this.resetMonthMap();
                this.initMap();
            },
            deep: true,
        },
        year: function() { this.main.year      = this.year; },
        month: function() {this.main.month    = this.month; },
        lawRestDay: function() { this.child.lawRestDay          = this.lawRestDay; },
        normalRestDay: function() { this.child.normalRestDay    = this.normalRestDay; },
        flexTotalTime: function() { this.child.flexTotalTime    = this.flexTotalTime; },
    },
    created: function() {
        this.assignData();
    }
}
</script>
<style>
.caeru_calendar_wrapper table tr td.pointable{
    cursor: pointer;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.caeru_calendar_wrapper table tr td.pointable:hover {
    font-weight: bold;
}

.caeru_calendar_date_picker_popup {
    width: 400px;
    position: relative;
    top: 50%;
    left: 50%;
    padding: 20px 20px 0;
    border:1px solid #ccc;
    z-index: 2000;
    background: #fff;
    opacity: 1.0;
}

.caeru_calendar_date_picker_popup table {
    table-layout: fixed;
}
</style>