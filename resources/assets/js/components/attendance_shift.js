import Attendance from '../components/attendance_shift.vue';
var vp = new Vue({
    el: '#ex6',
    data: {
        lists: window.data_employees,
        work_address_id: window.work_address_id,
        working_day_id: window.working_day_id,
        work_infor_id: window.work_infor_id,
        employee_names: window.employees,
        employee_id: window.employee_id

    },
    components: {
        attendance: Attendance,
    },
    created: function () {

    },
    methods: {


    }

});