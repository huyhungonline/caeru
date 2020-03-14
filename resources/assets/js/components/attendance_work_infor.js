import Attendance from '../components/attendance_work_infor.vue';
import Autocomplete from '../components/caeru_autocomplete.vue';
import Calendar from '../components/caeru_calendar.vue';
Vue.prototype.$eventHub = new Vue();
var m = 0;
var today = new Date();
var place = new Vue({
    el: '#attendance',
    data: {

        working_address_day_infors: window.data_working_days_infors,
        day_infor: window.day_infor,
        list_address: window.list_address,
        employee_names: window.employees,
        calender: window.calender,
        newEmployee: null,
        currentEmployee: null,
        color: 'red',
        yearSelected :today.getFullYear(),
        monthSelected: today.getMonth()+1,
        employee_id: null,
        employee_name: '',
        time_input: '1994-02-16',
        day : today.getDate(),
        fields: [
            today.getDate(),
            today.getMonth()+1,
            today.getFullYear(),
            0,
            0

        ],
        showDatePicker: false,
        datePickerOptions: null,
        datePickerTimeNavigationData: null,
        datePickerData: {
            flipColorDay: window.flip_color_day,
            restDays: window.rest_days,
            nationalHolidays: window.national_holidays,
        },
        calendar_rest_day_consts: {
            LAW_BASED_REST_DAY  : 1,
            NORMAL_REST_DAY     : 2,
            NOT_A_REST_DAY      : 0,
        },

    },
    components: {
        attendance_work_infor: Attendance,
        autocomplete: Autocomplete,
        calendar: Calendar,
    },
    watch: {
        monthSelected: function (val) {
            this.time_input = this.yearSelected + "-" + val + '-' + this.day;
        },
        day:  function (val) {
            this.time_input =  this.yearSelected + "-"+ this.monthSelected  + '-' + val  ;
        },
    },
    computed: {

        classChanger: function(index){

            var theClass = 'classB';

            return theClass;
        },
        classChanger1: function(index){

            var theClass = 'classA';

            return theClass;
        },
        // computed id of the new employee
        newAutocompleteId: function() {
            return (this.getNewAutocompleteId() !== -1) ? this.getNewAutocompleteId() : null;
        },


        // computed id of the current employee
        currentAutocompleteId: function() {

            return this.getAutocompleteId();
        },


    },
    created: function () {
        this.time_input = today.getFullYear()+'-'+this.monthSelected+'-'+today.getDate();
        this.processDatePickerOptions();


    },

    methods: {
        // DatePicker
        toggleDatePicker: function() {
            this.showDatePicker = !this.showDatePicker;
            if (this.showDatePicker === true) {
                this.$nextTick(function() {
                    this.repositionByHeight();
                });
            }
        },
        datePickerChangeTime: function(year, month) {
            this.datePickerTimeNavigationData = [year, month];
            this.processDatePickerOptions();
        },
        goToThisDay: function(date) {
            let urlParts = _.split(window.location.href, '/');
            urlParts.pop();
            urlParts.push(date);
            window.location.href = _.join(urlParts, '/');
        },
        filterByYearAndMonth: function(collection) {
            let filtered = _.filter(collection, (item) => {
                return (item[0] === this.yearSelected) && (item[1] === this.monthSelected);
            });
            return _.map(filtered, (item) => {return item[2];});
        },
        processDatePickerOptions: function() {
            let nationalHolidays = [];
            let lawRestDay = [];
            let normalRestDay = [];

            _.forEach(this.datePickerData['restDays'], (day) => {
                if (day['type'] === this.calendar_rest_day_consts['LAW_BASED_REST_DAY']) {
                    lawRestDay.push( _.map(_.split(day['assigned_date'], '-'), (data) => { return _.toInteger(data)}) );
                } else if (day['type'] === this.calendar_rest_day_consts['NORMAL_REST_DAY']) {
                    normalRestDay.push( _.map(_.split(day['assigned_date'], '-'), (data) => { return _.toInteger(data)}) );
                }
            });

            nationalHolidays = _.map(this.datePickerData['nationalHolidays'], (day) => {
                return _.map(_.split(day, '-'), (data) => { return _.toInteger(data)});
            });

            this.datePickerOptions = {
                'year' : this.yearSelected,
                'month' : this.monthSelected,
                'nationalHolidays' : this.filterByYearAndMonth(nationalHolidays),
                'lawRestDay' : this.filterByYearAndMonth(lawRestDay),
                'normalRestDay' : this.filterByYearAndMonth(normalRestDay),
                'flipColorDay' : this.datePickerData['flipColorDay'],
                'startColor' : (this.monthSelected % 2) === 0,
                'pickerMode' : true,
            }
        },
        repositionByHeight: function() {
            let popUp = $('.normal_date_picker .caeru_calendar_date_picker_popup');
            let diffHeight = window.innerHeight - popUp.outerHeight();
            let diffWidth = window.innerWidth - popUp.outerWidth();
            let scrollOffset = $(window).scrollTop();
            popUp.offset({ top: (scrollOffset + diffHeight/2), left: diffWidth/2 });
        },

///////////////////////
        // methods to get and update the Id of the current employee
        getAutocompleteId: function () {
            return _.findIndex(this.employee_names, obj => {
                return obj.id == this.currentEmployee;
            });
        },
        // methods to get and update the Id of the target employee
        getNewAutocompleteId: function () {
            return _.findIndex(this.employee_names, obj => {
                return obj.id == this.newEmployee;
            });
        },
        newEmployeeSelected(id) {
            if (id !== null) {

                this.newEmployee = this.employee_names[id].id;
            }
            else this.newEmployee = null;
        },
        currentEmployeeSelected(id) {
            this.employee_id = this.employee_names[id].id;
            this.$set(this.fields, 4, this.employee_names[id].id);
            if (id != null) this.currentEmployee = this.employee_names[id].id;
            else this.currentEmployee = this.currentEmployee;
        },
        daysInMonth(year, month) {
            return new Date(year, month, 0).getDate();
        },
        nextDay() {
            var x = this.daysInMonth(this.monthSelected, this.yearSelected);
            if (this.day + 1 > x) {
                this.day = 1;
                this.monthSelected = this.monthSelected + 1;
                this.$set(this.fields, 0, this.day);
                this.$set(this.fields, 1, this.monthSelected);
                if (this.monthSelected > 12) {
                    this.monthSelected = 1;
                    this.yearSelected = this.yearSelected + 1;
                    this.$set(this.fields, 1, this.monthSelected);
                    this.$set(this.fields, 2, this.yearSelected);
                }
                this.get_data_address_infor();
            } else {
                this.day = this.day + 1;
                this.$set(this.fields, 0, this.day);
                this.get_data_address_infor();
            }
        },
        preDay() {
            if (this.day - 1 <= 0) {
                this.day = this.daysInMonth(this.monthSelected - 1, this.yearSelected);
                this.$set(this.fields, 0, this.day);
                this.monthSelected = this.monthSelected - 1;
                this.$set(this.fields, 1, this.monthSelected);
                if (this.monthSelected <= 0) {
                    this.monthSelected = 12;
                    this.yearSelected = this.yearSelected - 1;
                    this.$set(this.fields, 1, this.monthSelected);
                    this.$set(this.fields, 2, this.yearSelected);
                }
                this.get_data_address_infor();
            } else {
                this.day = this.day - 1;
                this.$set(this.fields, 0, this.day);
                this.get_data_address_infor();
            }
        },
        nextWeek() {
            if (this.day + 7 > 31 && this.day != 24) {
                var x = this.daysInMonth(this.monthSelected, this.yearSelected);
                var y = x - this.day;
                var z = 7 - y;
                this.day = z;
                this.$set(this.fields, 0, this.day);
                this.monthSelected = this.monthSelected + 1;
                this.$set(this.fields, 1, this.monthSelected);
                if (this.monthSelected > 12) {
                    this.monthSelected = 1;
                    this.yearSelected = this.yearSelected + 1;
                    this.$set(this.fields, 1, this.monthSelected);
                    this.$set(this.fields, 2, this.yearSelected);
                }
                this.get_data_address_infor();
            } else {
                this.day = this.day + 7;
                this.$set(this.fields, 0, this.day);
                this.get_data_address_infor();
            }

        },
        preWeek() {
            if (this.day - 7 > 0) {

                this.day = this.day - 7;
                this.$set(this.fields, 0, this.day);
                this.get_data_address_infor();
                return
            }
            if (this.day - 7 <= 0) {

                var x = this.day - 7;

                var y = this.daysInMonth(this.monthSelected - 1, this.yearSelected);

                this.day = y + x;
                this.$set(this.fields, 0, this.day);
                this.monthSelected = this.monthSelected - 1;
                this.$set(this.fields, 1, this.monthSelected);
                if (this.monthSelected == 0) {
                    this.monthSelected = 12;
                    this.yearSelected = this.yearSelected - 1;
                    this.$set(this.fields, 1, this.monthSelected);
                }
                this.get_data_address_infor();
            }

        },
        nextMonth: function () {
            this.monthSelected++;
            if (this.monthSelected > 12) {
                this.yearSelected++;
                this.monthSelected = 1;
                this.$set(this.fields, 1, this.monthSelected);
            }
            this.$set(this.fields, 1, this.monthSelected);

            this.getCalender();
        },
        preMonth: function () {
            this.monthSelected--;
            if (this.monthSelected < 1) {
                this.yearSelected--;
                this.monthSelected = 12;
                this.$set(this.fields, 1, this.monthSelected);
            }
            this.$set(this.fields, 1, this.monthSelected);
            this.getCalender();
        },
        getCalender: function () {
            axios.get('/itz/attendance_address_infor_get_calender', {
                params: {
                    month: this.monthSelected,
                    year: this.yearSelected
                }
            }).then(response => {
                this.calender = response.data;

            }).catch(error => {

                document.caeru_alert('error', '');


            })
        },
        chooseDay: function (it, i, index) {

            if (index == 0 && it > 7) {
                m = 1;
                if (this.monthSelected >= this.monthSelected - 1) {
                    this.monthSelected = this.monthSelected - 1;
                    this.$set(this.fields, 1, this.monthSelected);
                }
                this.day = it;
                this.$set(this.fields, 0, this.day);
                return
            }
            if (index > 1 && it < 7) {

                m = 2;
                if (this.monthSelected <= this.monthSelected + 1) {
                    this.monthSelected = this.monthSelected + 1;
                    this.$set(this.fields, 1, this.monthSelected);
                }
                this.day = it;
                this.$set(this.fields, 0, this.day);
                return
            }
            if (m == 1) {
                this.monthSelected = this.monthSelected + 1;
                this.$set(this.fields, 1, this.monthSelected);
                m = 0;
            }
            if (m == 2) {
                this.monthSelected = this.monthSelected - 1;
                this.$set(this.fields, 1, this.monthSelected);
                m = 0;
            }
            this.day = it;


        },
        change_address_id: function (value) {

            this.fields[3] = value;
        },
        search: function () {

            var data = {
                'conditions': this.fields
            };
            axios.get('/itz/attendance_address_infor_day_API', {
                params: {
                    month: this.monthSelected,
                    year: this.yearSelected,
                    day: this.day
                }

            }).then(response => {
                this.day_infor = response.data;
                alert(this.fields[4]);

            }).catch(error => {

                //  document.caeru_alert('error', '');


            });
            axios.post('/itz/attendance_address_infor_API', data).then(response => {

                //  document.caeru_alert('success', response.data['id']);
                this.working_address_day_infors = response.data;


            }).catch(error => {

                document.caeru_alert('error', '');


            });
        },
        get_data_address_infor: function () {
            var data = {
                'conditions': this.fields
            };

            axios.get('/itz/attendance_address_infor_day_API', {
                params: {
                    month: this.monthSelected,
                    year: this.yearSelected,
                    day: this.day
                }
            }).then(response => {
                this.day_infor = response.data;


            }).catch(error => {

                //  document.caeru_alert('error', '');


            });
            axios.post('/itz/attendance_address_infor_API', data).then(response => {
                this.working_address_day_infors = response.data['data'];


            }).catch(error => {

                 document.caeru_alert('error', '');


            })
        },
        reset: function () {
            this.day = today.getDate();
            this.yearSelected = today.getFullYear();
            this.monthSelected = today.getMonth();
        },
        gotoWorkPlace : function (id) {

            let url = '/attendance_working_place';
            url = url + '/' + id;
            window.location.href = $.companyCodeIncludedUrl(url);
        },
        gotoAttendanceMember : function (id) {

            let url = '/attendance_working_member';
            url = url + '/' + id;
            window.location.href = $.companyCodeIncludedUrl(url);
        },
        reset: function () {

            this.monthSelected = today.getMonth()+1;
            this.yearSelected = today.getFullYear();
            this.day = today.getDate();
            this.$set(this.fields, 0,  today.getDate());
            this.$set(this.fields, 1, today.getMonth()+1);
            this.$set(this.fields, 2,  today.getFullYear());
            this.$set(this.fields, 3,   0);
            this.$set(this.fields, 4, 0);
        }


    }
});
