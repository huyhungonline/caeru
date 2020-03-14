import Attendance from '../components/attendance_place.vue';
Vue.prototype.$eventHub = new Vue();
var a =[];
var place = new Vue({
    el: '#attendance_detail',
    data: {
        working_day_infors: window.data_working_days_infors,
        day_infor: window.day_infor,
        time: 0,
        monthSelected: 11,
        yearSelected: 2017
    },
        components: {
        attendance_place: Attendance,
    },
    created: function () {
        this.$eventHub.$on('submit',function (working_confirm,address_information_id,employee_working_information_id,id_table) {
            var person = {
                "confirm" : working_confirm,
                "address_information_id":address_information_id,
                "employee_working_information_id":employee_working_information_id,
                "id_table":id_table
            };
           a.push(Object.values(person));

        });

    },
    methods: {
        nextMonth: function() {
            this.monthSelected++;
            if(this.monthSelected > 12) {
                this.yearSelected++;
                this.monthSelected = 1;
            }
            this.emit();
        },
        preMonth: function() {
            this.monthSelected--;
            if(this.monthSelected < 1) {
                this.yearSelected--;
                this.monthSelected = 12;
            }
            this.emit();
            },
        emit: function () {

            axios.get('/itz/get_attendance_place_infor',{params: {  month: this.monthSelected,year: this.yearSelected}}).then(response => {
                this.working_day_infors = response.data;

            }).catch(error => {

                document.caeru_alert('error', '');


            })
        },
        click: function () {
            axios.get('/itz/save_attendance_place_infor',{params: { data:a}}).then(response => {
                document.caeru_alert('success', response.data['success']);
            }).catch(error => {

                document.caeru_alert('error', '');


            })

        },
        cancel: function () {

            axios.get('/itz/cancel_attendance_place_infor',{params: {  time: 0}}).then(response => {

                this.working_day_infors = response.data;


            }).catch(error => {

                document.caeru_alert('error', '');


            })
        }

    }

});