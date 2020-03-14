<template>
    <table width="1000px">
<template  v-for="(item,index) in data">

            <tr>

            <td  v-if = " index == 0 " rowspan="30" width="185px"><span > {{ item.day }}({{  item.weekday}})</span></td>


            <td width="107px">{{ item.time_start}}</td>
            <td width="106px">{{ item.time_end}}</td>
            <td v-if ="item.number != item.color" width="80px" style="background-color: #e874a4;" >{{ item.number}}</td>
                <td v-else width="80px"  >{{ item.number}}</td>
            <td class="left_align" width="723px">
                <template v-for="(it,index) in item['data']">
                <attendance_place_working_employee  :data_employees = "it"></attendance_place_working_employee>
                </template>
            </td>
            <td><p class="button"><a class="ss_size s_height btn_gray" href="attendance_shift.html">訪問</a></p></td>
            </tr>

</template>
    </table>
</template>
<script>
import Attendance_place_working_employee from '../components/attendance_place_working_employee.vue'
export default {
    props: {
        data: {
            type: Array,
            reuired: false,
        },
        day_infor: {
            type: String,
            reuired: false,
        },

    },
    data(){
        return {

         date: '',
            n:5,
            color: 'blue'


        }
    },
    computed: {


    },
    components: {
        attendance_place_working_employee: Attendance_place_working_employee
    },
    created: function () {

    },
    methods: {


    }
}

</script>