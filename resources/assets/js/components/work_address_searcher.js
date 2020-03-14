import Hub from '../components/hub.js';
import Autocomplete from '../components/caeru_autocomplete';

const hub = Hub;

var searcher = new Vue({
    el: '.search_box_wrapper',
    data: {
        display: !!window.default_hide ? false : true,
        fields: [
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
        ],
        default: [
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
        ],
        initPlaceName: '',
        placeNames: window.work_address_names,
        initPlaceAddress: '',
        placeAddresses: window.work_address_addresses,
        initEmployeeName: '',
        employeeNames: window.employee_names,
        displayHistory: false,
        searchHistory:  window.search_history,
        redirect: !!window.target ? window.target : null,
        sendingRequest: false,
    },
    methods: {
        changeConditions: function() {
            this.display = true;
            this.displayHistory = false;
        },
        placeNameSelected: function(id) {
            this.fields[1] = this.placeNames[id].name;
        },
        placeNameChanged: function(newName) {
            this.fields[1] = newName;
        },
        placeAddressSelected: function(id) {
            this.fields[5] = this.placeAddresses[id].name;
        },
        placeAddressChanged: function(newName) {
            this.fields[5] = newName;
        },
        employeeNameSelected: function(id) {
            this.fields[3] = this.employeeNames[id].name;
        },
        employeeNameChanged: function(newName) {
            this.fields[3] = newName;
        },
        resetConditions: function() {
            this.fields = _.cloneDeep(this.default);
            // _.assign(this.fields, this.default);
            hub.$emit('reset-autocomplete');
        },
        submit: function(){
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                var data = {
                   'conditions': this.fields,
                };
                axios.post($.companyCodeIncludedUrl('/work_address/search'), data).then(response => {
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
    },
    created: function() {

        // If there's already been a search history, overwrite the default data with it.
        if (!!this.searchHistory) {
            if (!_.isEqual(this.default, this.searchHistory.conditions)) {
                this.fields = this.searchHistory.conditions;
                this.displayHistory = true;
                this.display = false;
            }
        }
    },
    components: {
        autocomplete: Autocomplete,
    },
});