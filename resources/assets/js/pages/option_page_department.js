var example1 = new Vue({
    el: 'section#option_item_content',
    data: {
        list_department_status: list_department_status,
        name_option: '',
        confirm:false
    },
    methods: {
        addRows: function (event) {
            var object = {};
            object.name = this.name_option;
            if (this.name_option == '') return;
            if (!this.checkInValid(this.list_department_status, this.name_option)) this.list_department_status.push(object);
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
            event.preventDefault();
            var url = '/updateDepartments';
            var data = {
                list_department_status: this.list_department_status,
                confirm: this.confirm
            };
            axios.post($.companyCodeIncludedUrl(url), data).then(response => {
                this.sendingRequest = false;
               if (!!response.data.confirm) {
                    this.confirm = response.data.confirm;
                    var r = confirm("Employee is using this department, please confirm remove it!");
                    if (r == true) {
                        this.onFormSubmit(event);
                    }
                    else {
                        this.list_department_status = response.data.list_department_status;
                        this.confirm = false;
                    }
                }else {
                    document.caeru_alert('success', response.data['success']);
                    this.list_department_status = !!response.data.list_department_status ? response.data.list_department_status : this.list_department_status;
                    this.confirm = false;
                }
            }).catch(error => {
                document.caeru_alert('error', '');
                var errors = error.response.data;
                this.sendingRequest = false;
                $.each( errors, function( i, l ){
                    alert(l);
                    return false;
                });
            })
        },
    }
});