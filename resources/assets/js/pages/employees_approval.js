import Autocomplete from '../components/caeru_autocomplete';
import Paginator from '../components/caeru_paginator';

var approval = new Vue({
    el: 'main#basic',
    data: {
        currentEmployee:    window.current_employee,
        subordinates:       window.subordinates,
        employees:          window.employees,
        currentPage: 1,
        perPage: 20,
        fields: [
            null,
            null,
            1,
            null,
            []
        ],
        default: [
            null,
            null,
            1,
            null,
            []
        ],
        displayPopup: false,
        newEmployee: null,
        sendingRequest: false,
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


        paginatedData: function() {
            return _.chunk(this.subordinates, this.perPage)[this.currentPage - 1];
        },
    },
    methods: {
        submit: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                // The first thing to do is discard all the old employee in the list who is not a subordinate
                _.remove(this.subordinates, (employee) => {
                    return employee.attached == false;
                });

                var data = {
                    "conditions"        :   this.fields
                };
                axios.post($.companyCodeIncludedUrl("/employee_approval_search"), data).then(result => {
                    var differ = _.pullAllBy(result.data, this.subordinates, 'id');
                    this.subordinates = _.concat(this.subordinates, differ);
                    this.sendingRequest = false;
                }).catch(error => {
                    console.log(error.response);
                    this.sendingRequest = false;
                });

            }
        },
        reload: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                var url = '/employee_approval_reload/' + this.currentEmployee;
                axios.get($.companyCodeIncludedUrl(url)).then(result => {
                    this.fields = this.default;
                    this.subordinates = result.data;
                    this.sendingRequest = false;
                }).catch(error => {
                    console.log(error.response);
                    this.sendingRequest = false;
                });

            }
        },
        resetConditions: function() {
            this.fields = _.cloneDeep(this.default);
        },


        // methods to get and update the Id of the current employee
        getAutocompleteId: function() {
            return _.findIndex(this.employees, obj => {
                return obj.id == this.currentEmployee;
            });
        },
        currentEmployeeSelected(id) {
            if (id !== null) this.currentEmployee = this.employees[id].id;
            else this.currentEmployee = this.currentEmployee;
        },


        // methods to get and update the Id of the target employee
        getNewAutocompleteId: function() {
            return _.findIndex(this.employees, obj => {
                return obj.id == this.newEmployee;
            });
        },
        newEmployeeSelected(id) {
            if (id !== null) this.newEmployee = this.employees[id].id;
            else this.newEmployee = null;
        },


        changePage: function(newPage) {
            this.currentPage = newPage;
        },


        // chief-subordinate related stuffs
        updateRelationship: function(key) {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                var data = {
                    'current': this.currentEmployee,
                    'target' : this.paginatedData[key].id,
                    'status' : this.paginatedData[key].attached,
                };
                axios.post($.companyCodeIncludedUrl('/employee_approval_update'), data).then(response => {
                    document.caeru_alert('success', response.data['success']);
                    this.sendingRequest = false;
                }).catch(error => {
                    document.caeru_alert('error', '');
                    console.log(error.response);
                    this.paginatedData[key].attached = !this.paginatedData[key].attached;
                    this.sendingRequest = false;
                })

            }

        },
        moveRelationship: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                var data = {
                    'current'   : this.currentEmployee,
                    'new'       : this.newEmployee,
                };
                axios.post($.companyCodeIncludedUrl('/employee_approval_move'), data).then(response => {
                    document.caeru_alert('success', response.data['success']);
                    this.currentEmployee = this.newEmployee;
                    this.newEmployee = null;
                    this.closePopup();
                    this.sendingRequest = false;
                }).catch(error => {
                    document.caeru_alert('error', '');
                    console.log(error.response);
                    this.sendingRequest = false;
                })

            }

        },


        // pop up related
        closePopup: function() {
            this.displayPopup = false;
        },
        showPopup: function() {
            this.displayPopup = true;
            this.$nextTick(function() {
                this.repositionByHeight();
            });
        },
        repositionByHeight: function() {
            let popUp = $('.approval_pop_up');
            let diffHeight = window.innerHeight - popUp.outerHeight();
            let diffWidth = window.innerWidth - popUp.outerWidth();
            let scrollOffset = $(window).scrollTop();
            popUp.offset({ top: (scrollOffset + diffHeight/2), left: diffWidth/2 });
        }
    },
    watch: {
        currentEmployee: {
            handler: function() {
                this.reload();
            }
        },
    },
    mounted: function() {
        var select_box = $("select.ms");

        // Multiple select box
        select_box.multipleSelect({
            width: 250,
            selectAll: true,
            minimumCountSelected: 4,
        });
        select_box.change(() => {
            // console.log($(select_box).val());
            this.$set(this.fields, 4, $(select_box).val());
        });

        // This is for calculating the position of the pop up
        this.repositionByHeight();
        this.$nextTick(function() {
            window.addEventListener('resize', this.repositionByHeight);
        });
    },
    components: {
        autocomplete: Autocomplete,
        paginator: Paginator,
    },
});