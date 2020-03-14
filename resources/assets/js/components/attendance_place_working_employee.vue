



<template>
        <section class="check_box_wrap right_30 side_input_block">

            <label class="checkbox_text"><input name="hugahuga" type="checkbox"  :checked="data_employees.working_confirm == 1 ? true : false" v-model="data_employees.working_confirm"  @click="save_employee_confirm_infor"  >{{ data_employees.name }} <a v-if="data_employees.gender == 1">(女)</a><a v-if="data_employees.gender == 2">(男)</a></label>
        </section>
</template>

<script>
export default {
    props: {
        data_employees: {
            type: Object,
            reuired: false,
        },
    },
    data(){
        return {

            working_confirm: 0

        }
    },
    computed: {


    },
    components: {

    },
    created: function () {
        let self = this;
        this.working_confirm = this.data_employees.working_confirm;

    },
    methods: {
       save_employee_infor: function () {
           axios.get('/itz/save_attendance_place_infor',{params: { employee_id: this.data_employees.id,work_infor_id: this.data_employees.address_information_id,employee_working_information_id: this.data_employees.employee_working_information_id,working_confirm: this.data_employees.working_confirm}}).then(response => {

              document.caeru_alert('success', response.data['success']);

           }).catch(error => {

             //  document.caeru_alert('error', response.data['id']);


           })
       },
        save_employee_confirm_infor: function () {
            this.data_employees.working_confirm = (this.data_employees.working_confirm + 1) % 2;
           // this.$eventHub.$emit('logged-in',);
            this.$eventHub.$emit('submit',this.data_employees.working_confirm,this.data_employees.address_information_id,this.data_employees.employee_working_information_id,this.data_employees.id_table);
        }
    }
}

</script>