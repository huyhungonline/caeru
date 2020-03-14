import Hub from '../components/hub.js';
import workSchedule from '../components/work_schedule.js';

const hub = Hub;

/**
    The work-schedule component

    To use this component, you will need, in the data:
        A presentation_data object. Like its name, this object contain the presentation data for this component. Find out more in the composer for employee.edit_work page
        A model_data array. It's the container of the instances of the PlannedSchedule model, with some extra properties.
        A variable to maintain the uniqueness of every new instance of schedule. In this cae, the newScheduleNumber variable it is.
    Then you will also need the three functions: addSchedule, removeSchedule, copySchedule.

    The other functions(i.e. submitMainForm, checkScheduleState, changeState) are for various purposes, like changes detection, data submission. You can modify them however you need.

    This component uses the centralize event hub to communicate with the main instance, so you will also need to import the hub, like above.

**/
Vue.component('work-schedule', workSchedule);

var app = new Vue({
    el: '#container',
    data: {
        presentation_data: window.presentation_data,
        model_data: window.model_data,
        newScheduleNumber : 0,
    },
    methods: {
        submitMainForm: function() {

            // This step is for checking if there is any changed schedule to warn the user before he/she press the '保存' button.
            // Now that the button has been clearly separated into the main form's '保存' button and the 'スケジュル保存' button of the schedules section,
            // we dont need to do this check-to-confirm any more.
            hub.$emit('submit-schedule');

            // if (true == this.checkScheduleState()) {
            //     var choice = window.confirm('There have been changes to the schedules. Do you want to save them ?');
            //     if (choice) {
            //         hub.$emit('submit-schedule');
            //     } else {
            //         $('form#main').submit();
            //     }
            // } else {
            //     $('form#main').submit();
            // }
        },
        addSchedule: function() {
            this.model_data.unshift({
                id : 'new' + this.newScheduleNumber,
                new : 'new',
                changed: true,
            });
            this.newScheduleNumber++;
        },
        removeSchedule: function(index) {
            this.model_data.splice(index, 1);
        },
        copySchedule: function(data, originalId) {
            var newData = jQuery.extend(true, {}, data)
            newData.id = 'new' + this.newScheduleNumber;
            newData.new = "copy";
            newData.changed = true;
            this.newScheduleNumber++;
            this.putNextTo(originalId, newData);
        },
        changeState: function(id, state) {
            for (var index in this.model_data) {
                if (this.model_data[index].id == id) {
                    this.model_data[index].changed = state;
                    return ;
                }
            }
        },
        checkScheduleState: function(){
            for (var index in this.model_data) {
                if (this.model_data[index].changed == true) {
                    return true;
                }
            }
            return false;
        },
        putNextTo: function(modelId, data) {
            var targetIndex = _.findIndex(this.model_data, (model) => {
                return model.id === modelId;
            })
            var head_part = _.slice(this.model_data, 0, targetIndex+1);
            var tail_part = _.slice(this.model_data, targetIndex+1);
            this.model_data = _.concat(head_part, data, tail_part);
        },
    },
    created: function() {
        hub.$on('changed', id => this.changeState(id, true));
        hub.$on('saved', id => this.changeState(id, false));
    },
    mounted: function() {
        for (var index in this.model_data) {
            this.model_data[index].changed = false;
        };
    }
})

$(document).ready(function() {
    // Initialize
    var holiday_bonus_type_extra_value = $("[name='holiday_bonus_type_extra']:checked");
    var holiday_bonus_type_extra = $("[name='holiday_bonus_type_extra']");
    var select_box = $("[name=holiday_bonus_type]");


    // Disable if paid_holiday_type = 1
    if(holiday_bonus_type_extra_value.val()==1) {
        select_box.prop("disabled", 'disable');
        select_box.addClass('disabled');
    }

    // toggle the select box base on the choice of holiday_bonus_type_extra
    holiday_bonus_type_extra.change(function() {
        if($(this).val()==1) {
            select_box.prop("disabled", 'disable');
            select_box.addClass('disabled');
            select_box.val('');
        } else {
            select_box.prop("disabled", false);
            select_box.removeClass('disabled');
        }
    });
});