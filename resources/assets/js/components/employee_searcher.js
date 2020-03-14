import Hub from '../components/hub.js';
import Autocomplete from '../components/caeru_autocomplete';

const hub = Hub;

var searcher = new Vue({
    el: '.search_box_wrapper',
    data: {
        display: false,
        displayHistory: false,
        displayToggleButton: false,
        searchHistory:  window.search_history,
        workAddresses: window.work_addresses,
        employeeNames: window.employee_names,
        redirect: !!window.target ? window.target : null,
        departmentField: null,
        fields: [
            null,
            null,
            null,
            null,
            [],
            1,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        ],
        default: [
            null,
            null,
            null,
            null,
            [],
            1,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        ],
        sendingRequest: false,
    },
    methods: {
        toggle: function(){
            this.display = !this.display;
        },
        changeConditions: function() {
            this.display = true;
            this.displayHistory = false;
            this.displayToggleButton = true;
        },
        resetConditions: function() {
            this.fields = _.cloneDeep(this.default);
            this.departmentField.multipleSelect('uncheckAll');
            hub.$emit('reset-autocomplete');
        },
        submit: function(){
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                var data = {
                   'conditions': this.fields,
                };
                axios.post($.companyCodeIncludedUrl('/employee/search'), data).then(response => {
                    if (!!this.redirect) {
                        window.location.replace(this.redirect);
                    } else {
                        $('section.searcher').html(response.data);
                    }
                    this.sendingRequest = false;
                }).catch(error => {
                    if (error.response) {
                        console.log('error: ' + error.response);
                    }
                    this.sendingRequest = false;
                })

            }
        },

        // Autocomplete methods
        workAddressSelected: function(id) {
            this.$set(this.fields, 20, this.workAddresses[id].name);
        },
        workAddressChanged: function(value) {
            this.$set(this.fields, 20, value);
        },
        employeeNameSelected: function(id) {
            this.$set(this.fields, 1, this.employeeNames[id].name);
        },
        employeeNameChanged: function(value) {
            this.$set(this.fields, 1, value);
        },
    },
    components: {
        autocomplete: Autocomplete,
    },
    mounted: function() {
        var select_box = $("select.ms");

        // Multiple select box
        this.departmentField = select_box.multipleSelect({
            width: 250,
            selectAll: true,
            minimumCountSelected: 4,
        });
        select_box.change(() => {
            // console.log($(select_box).val());
            this.$set(this.fields, 4, $(select_box).val());
        });
    },
    created: function() {
        // If there's already been a search history, overwrite the default data with it.
        if (!!this.searchHistory) {
            if (!_.isEqual(this.default, this.searchHistory.conditions)) {
                this.fields = this.searchHistory.conditions;
                this.displayHistory = true;
            }
        }
    },
});