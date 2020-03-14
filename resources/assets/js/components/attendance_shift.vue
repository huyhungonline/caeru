<template>
    <div >
    <section class="approval_table">
        <div id="insert_infor_working">
           <table class="bottom_30">
            <tr>
                <th class="s_6"></th>
                <th class="s_6">出勤</th>
                <th class="s_6">退勤</th>
                <th class="s_6">必要人数</th>
                <th class="s_12">備考</th>
                <th class="s_10">変更ユーザ</th>
                <th class="s_8"></th>
            </tr>
            <tr>
                <td>予定</td>
                <td><input class="ss_size" name=""   type="text" value="" v-model="planned_start_work_time"></td>
                <td><input class="ss_size" name=""  type="text" value="" v-model="planned_end_work_time"></td>
                <td><input class="ss_size" name="" type="text" value="2" v-model="candidate_number"></td>
                <td><textarea name="remarks" class="mm_size" v-model="note"></textarea></td>
                <td></td>
                <td>
                    <section class="unit td_height_space">
                        <p class="bottom_10"><span class="button"><a class="s_size s_height btn_greeen"  v-on:click="save_planned_address_work_infor()">保存</a></span></p>
                        <p><span class="button"><a class="s_size s_height btn_gray" v-on:click="resetAddressInfor()">キャンセル</a></span></p>
                    </section>
                </td>
            </tr>
        </table>
        </div>
    </section>

    <section class="select_one2">
        <section class="right_position">
            <p class="button add_btn"><a class="m_size s_height btn_blue" href="#">従業員追加</a></p>
        </section>
    </section>
    <section class="approval_table">
        <table>
            <tr>
                <th class="s_5"></th>
                <th class="s_8">従業員ID</th>
                <th class="s_18">従業員名</th>
                <th class="s_8">勤務形態</th>
                <th class="s_8">休日形態</th>
                <th class="s_6">休憩</th>
                <th class="s_6">(内)深休</th>
                <th class="s_12">勤務時間</th>
                <th class="s_14">変更ユーザ</th>
                <th class="s_10"></th>
                <th></th>
            </tr>
            <tr>
                <td>
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" @click="disabled = (disabled + 1) % 2" value="hoge" v-model="working_confirm"></label>
                    </div>
                </td>
                <td>
                    <autocomplete :suggestions="employee_names" custom-class="s_size" :linked="true" :allow-null="false" v-model="employee_id"
                                  :initial-id="currentAutocompleteId"
                                  :current-id="currentAutocompleteId"
                                  filtered-field-name="presentation_id"
                                  @selected="currentEmployeeSelected">
                    </autocomplete>
                </td>
                <td>
                    <autocomplete :suggestions="employee_names" custom-class="m_size" :linked="true" :allow-null="false"
                                  :initial-id="currentAutocompleteId"
                                  :current-id="currentAutocompleteId"
                                  @selected="currentEmployeeSelected">
                    </autocomplete>
                </td>
                <td></td>
                <td></td>
                <td><input class="ss_size" name="" type="text" :disabled="disabled == 1 ? false : true"  v-model="break_time"></td>
                <td><input class="ss_size" name="" type="text" :disabled="disabled == 1 ? false : true"  v-model="night_break_time"></td>
                <td>〜</td>
                <td></td>
                <td class="td_height_space">
                    <section class="unit">
                        <p class="bottom_10"><span class="button"><a class="s_size s_height btn_greeen" v-on:click="insert_new_employee_for_working_address()" >保存</a></span></p>
                        <p><span class="button"><a class="s_size s_height btn_gray" href="#">キャンセル</a></span></p>
                    </section>
                </td>
                <td>
                    <p class="button"><a class="ss_size s_height btn_gray" href="attendance_details_visit.html">変更</a></p>
                </td>
            </tr>
            <template v-for="(item,index) in data">
                <attendance_employee :data_employee="item" :planned_start_work_time="planned_start_work_time" :planned_end_work_time="planned_end_work_time"></attendance_employee>
            </template>
        </table>
    </section>
    <section class="btn">
        <p class="button right_30 save_btn"><a class="m_size l_height btn_greeen l_font" @click="click()">保存</a></p>
        <p class="button right_30"><a class="m_size l_height btn_gray l_font" @click="initialState()">キャンセル</a></p>
        <p class="button"><a class="m_size l_height btn_gray l_font" href="attendance_work_info.html">一覧に戻る</a></p>
    </section>
    </div>
</template>

<script>
import Attendance_employee from '../components/attendance_employee.vue';
import Autocomplete from '../components/caeru_autocomplete.vue'
  export default {
      props: {
          data: {
              type: Object,
              reuired: false,
          },
          index: {
              type: Number,
              required: false,
          },
          work_address_id: {
              type: Number,
              required: false,
          },
          working_day_id: {
              type: Number,
              required: false,
          },
          work_infor_id: {
              type: Array,
              required: false,
          },
          employee_names: {
              type: Array,
              required: false,
          },
      },
      data(){
          return {

              break_time: '',
              night_break_time: '',
              employee_id: '',
              employee_name: '',
              working_confirm: 0,
              start_working_time: null,
              end_working_time: null,
              note: null,
              candidate_number: '',
              planned_start_work_time: '',
              planned_end_work_time: '',
              disabled: 0,
              hidden: 1,
              newEmployee: null,
              currentEmployee: null,

          }
      },
      computed: {
          // computed id of the new employee
          newAutocompleteId: function() {
              return (this.getNewAutocompleteId() !== -1) ? this.getNewAutocompleteId() : null;
          },


          // computed id of the current employee
          currentAutocompleteId: function() {
              return this.getAutocompleteId();
          },


      },
      components: {
          attendance_employee: Attendance_employee,
          autocomplete: Autocomplete
      },
      created: function () {


          axios.get('/itz/get_working_address_infor',{params: {work_infor_id: this.work_infor_id[this.index]}}).then(response => {

             this.candidate_number = response.data.candidate_number;
             this.planned_start_work_time =  response.data.planned_start_work_time;
             this.planned_end_work_time  = response.data.planned_end_work_time;
             this.note            = response.data.note;

          }).catch(error => {
              if (error.response) {
                  document.caeru_alert('error', '');
                  this.showError(null);
                  this.showError(error.response.data);
              }

          })
      },
      methods: {

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
              if (id != null) this.currentEmployee = this.employee_names[id].id;
              else this.currentEmployee = this.currentEmployee;
          },

          insert_new_employee_for_working_address: function () {

              axios.get('/itz/insert_employee_working_address', {
                  params: {
                      work_infor_id: this.work_infor_id[this.index],
                      employee_id: this.employee_id,
                      employee_name: this.employee_name,
                      working_confirm: this.working_confirm,
                      break_time: this.break_time,
                      nigth_break_time: this.nigth_break_time,
                      working_day_id: this.working_day_id
                  }
              }).then(response => {
                  document.caeru_alert('success', response.data['success']);
                  this.$emit('changeTimePlanned', this.planned_start_work_time, this.planned_end_work_time);
              }).catch(error => {

                  document.caeru_alert('error', '');


              })
          },
          save_planned_address_work_infor: function () {
              axios.get('/itz/save_working_address_infor', {
                  params: {
                      work_infor_id: this.work_infor_id[this.index],
                      planned_start_work_time: this.planned_start_work_time,
                      planned_end_work_time: this.planned_end_work_time,
                      candidate_number: this.candidate_number,
                      note: this.note
                  }
              }).then(response => {
                  document.caeru_alert('success', response.data['success']);
                  this.$emit('changeTimePlanned', this.planned_start_work_time, this.planned_end_work_time);
              }).catch(error => {

                  document.caeru_alert('error', '');


              })
          },
          click: function () {
              this.save_planned_address_work_infor();
              this.$emit('saveAllData', this.planned_start_work_time);
          },
          resetAddressInfor: function () {
              axios.get('/itz/get_working_address_infor',{params: {work_infor_id: this.work_infor_id[this.index]}}).then(response => {

                  this.candidate_number = response.data.candidate_number;
                  this.planned_start_work_time =  response.data.planned_start_work_time;
                  this.planned_end_work_time  = response.data.planned_end_work_time;
                  this.note            = response.data.note;

              }).catch(error => {
                  if (error.response) {
                      document.caeru_alert('error', '');
                      this.showError(null);
                      this.showError(error.response.data);
                  }

              })
          },
          initialState: function () {
              this.$emit('reset');
              axios.get('/itz/get_working_address_infor',{params: {work_infor_id: this.work_infor_id[this.index]}}).then(response => {

                  this.candidate_number = response.data.candidate_number;
                  this.planned_start_work_time =  response.data.planned_start_work_time;
                  this.planned_end_work_time  = response.data.planned_end_work_time;
                  this.note            = response.data.note;

              }).catch(error => {
                  if (error.response) {
                      document.caeru_alert('error', '');
                      this.showError(null);
                      this.showError(error.response.data);
                  }

              })
          }
      }
  }

</script>