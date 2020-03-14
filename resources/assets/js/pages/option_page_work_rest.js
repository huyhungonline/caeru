var example1 = new Vue({
    el: 'section#option_item_content',
    data: {
        list_work_status_default: list_work_status_default,
        list_work_status_customize: list_work_status_customize,
        list_rest_status_default: list_rest_status_default,
        list_rest_status_customize: list_rest_status_customize,
        default_data: default_data,
        default_work_status: default_work_status,
        name_option: '',
        selected:'勤務形態',
        message_error: null,
        check_submit: false,
        sendingRequest: false,
    },
    computed: {
        checkError: function() {
            return this.message_error != null ? 'error' : '';
        }
    },
    methods: {
        addRows: function (event) {
            var object = {};
            object.name = this.name_option;
            this.message_error = null
            if (this.name_option == '') return;
            if (this.name_option.length != 2) {
                this.message_error = "The input must be 2 characters";
                this.$refs.search.focus();
                return;
            }
            if (this.selected === '勤務形態') {
                for(var item in default_work_status){
                    if (default_work_status[item] == this.name_option) {
                        if (!this.checkInValid(this.list_work_status_default, this.name_option)) this.list_work_status_default.push(object);
                        this.name_option = '';
                        return;
                    }
                }
                if (!this.checkInValid(this.list_work_status_customize, this.name_option)) this.list_work_status_customize.push(object);
            } else {
                for(var item in default_data){
                    if (default_data[item] == this.name_option) {
                        if (!this.checkInValid(this.list_rest_status_default, this.name_option)) this.list_rest_status_default.push(object);
                        this.name_option = '';
                        return;
                    }
                }
                if (!this.checkInValid(this.list_rest_status_customize, this.name_option)) {
                    object.paid_type = 1;
                    object.unit_type = 1;
                    this.list_rest_status_customize.push(object);
                }
            }
            this.name_option = '';
        },
        removeRows: function (listObject, key) {
            Vue.delete(listObject, key);
        },
        checkInValid: function (listObject, name) {
            for(var index in listObject){
                if (listObject[index].name == name) return true;
            }
            return false;
        },
        onFormSubmit: function (event) {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                event.preventDefault();
                if (this.check_submit) return;
                this.check_submit = true;
                var url = '/updateWorkAndRest';
                var data = {
                    list_work_status_default: this.list_work_status_default,
                    list_work_status_customize: this.list_work_status_customize,
                    list_rest_status_default: this.list_rest_status_default,
                    list_rest_status_customize: this.list_rest_status_customize,
                };
                axios.post($.companyCodeIncludedUrl(url), data).then(response => {
                    document.caeru_alert('success', response.data['success']);
                    this.list_work_status_default = !!response.data.list_work_status_default ? response.data.list_work_status_default : this.list_work_status_default;
                    this.list_work_status_customize = !!response.data.list_work_status_customize ? response.data.list_work_status_customize : this.list_work_status_customize;
                    this.list_rest_status_default = !!response.data.list_rest_status_default ? response.data.list_rest_status_default : this.list_rest_status_default;
                    this.list_rest_status_customize = !!response.data.list_rest_status_customize ? response.data.list_rest_status_customize : this.list_rest_status_customize;
                    this.check_submit = false;
                    this.sendingRequest = false;
                }).catch(error => {
                    document.caeru_alert('error', '');
                    var errors = error.response.data;
                    $.each( errors, function( i, l ){
                        alert(l);
                        return false;
                    });
                    this.sendingRequest = false;
                })

            }
        },
    }
});