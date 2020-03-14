import Autocomplete from '../components/caeru_autocomplete';

var app = new Vue({
    el: 'tr.chiefs_panel',
    data: {
        chiefs : !!window.chiefs ? window.chiefs : [],
        employees : window.employees,
        resetId: 0,
    },
    methods: {
        chiefAdd: function(index) {

            if (index !== null) {
                var duplicated = _.findIndex(this.chiefs, (chief) => {
                    return chief === this.employees[index].id;
                });
                if (duplicated === -1 ) this.chiefs.push(this.employees[index].id);

                this.resetId = 0;
                this.$nextTick(() => {
                    this.resetId = null;
                });
            }

        },
        chiefRemove: function(index) {
            this.chiefs.splice(index, 1);
        },
        getChiefName: function(id) {
            return this.employees[_.findIndex(this.employees, (employee) => {
                return employee.id == id
            })].name;
        },
        submit: function() {
            $('form#form').submit();
        }
    },
    components: {
        autocomplete: Autocomplete,
    },
});


$(document).ready(function() {

    // Recreate the option items
    function changeOptionList(departments) {
        var html = "<option value=''></option>";
        $.each(departments, function(index, value) {
            html += "<option value='" + index + "'>" + value + "</option>";
        });
        return html;
    };

    var workLocationSelector = $('select[name=work_location_id]');
    var departmentSelector = $('select[name=department_id]');
    var workLocationHidden = $('input[type=hidden][name=work_location_id]');
    

    if (workLocationSelector.exists()) {
        var currentWorkLocation = window.department_list[workLocationSelector.val()];
        // Init
        departmentSelector.html(changeOptionList(currentWorkLocation));
        
        // Hook to change event
        workLocationSelector.change(function(){
            var currentList = window.department_list[$(this).val()];
            departmentSelector.html(changeOptionList(currentList));
        });
    } else {
        // Init
        departmentSelector.html(changeOptionList(window.department_list[workLocationHidden.val()]));
    }

    // If we have default value then,
    if (!!window.default_value) departmentSelector.val(window.default_value);
});