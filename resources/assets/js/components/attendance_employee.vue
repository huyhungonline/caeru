<template>

<tr>
    <td>
        <div class="check_onle_wrap">
            <label class="checkbox_box"><input name="" type="checkbox" value="0" :checked="working_confirm == 1 ? true : false" @click="working_confirm = (working_confirm + 1) % 2" v-model="working_confirm" ></label>
        </div>
    </td>
    <td>{{ data_employee.id }}</td>
    <td>{{ data_employee.name }}</td>
    <td></td>
    <td></td>
    <td><input class="ss_size" name="" type="text" :disabled="working_confirm == 1 ? false : true" value="" v-model="break_time"></td>
    <td><input class="ss_size" name="" type="text"  :disabled="working_confirm == 1 ? false : true" v-model="night_break_time"></td>
    <td id="">{{ time_start }}~{{ time_end }}</td>
    <td></td>
    <td class="td_height_space">
        <section class="unit">
            <p class="bottom_10"><span class="button"><a class="s_size s_height btn_greeen" @click="saveBreakTimeInfor()">保存</a></span></p>
            <p><span class="button"><a class="s_size s_height btn_gray" @click="reset()" >キャンセル</a></span></p>
        </section>
    </td>
    <td>
        <p class="button"><a class="ss_size s_height btn_gray" href="attendance_details_visit.html">変更</a></p>
    </td>
</tr>

</template>
<script>export default {
    props: {
        data_employee: {
            type: Object,
            reuired: false,
        },


    },
    data(){
        return {


            break_time: 0,
            night_break_time: 0,
            working_confirm: true,
            disabled: 0,
            checked: false,
            time_start: '',
            time_end: ''


        }
    },

    created: function () {

        this.break_time = this.data_employee.break_time;
        this.night_break_time = this.data_employee.night_break_time;
        this.working_confirm = this.data_employee.working_confirm;
        this.time_start = this.data_employee.time_start;
        this.time_end   = this.data_employee.time_end;
        let self = this;
        this.$parent.$on('saveAllData',function (id) {
            self.saveBreakTimeInfor();

        });
        this.$parent.$on('changeTimePlanned',function (start_time,end_time) {

            self.break_time = start_time;
            self.night_break_time = end_time;

        });
        this.$parent.$on('reset',function () {
              self.reset();
        });
    },
    methods: {


        saveBreakTimeInfor: function() {

            axios.get('/itz/save_break_time_infor',{params: {working_day_id:this.data_employee.working_day_id, address_information_id:this.data_employee.address_information_id,employee_id:this.data_employee.id,break_time:this.break_time,night_break_time: this.night_break_time, working_confirm: this.working_confirm}}).then(response => {

                document.caeru_alert('success', response.data['success']);

            }).catch(error => {

                document.caeru_alert('error', '');


            })
        },
        reset: function () {

            this.break_time = this.data_employee.break_time;
            this.night_break_time = this.data_employee.night_break_time;
            this.working_confirm = this.data_employee.working_confirm;
        }

    }
}
</script>