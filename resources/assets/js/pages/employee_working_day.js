import Hub from '../components/hub.js';
import WorkingInfo from '../components/employee_working_info_component';
import Autocomplete from '../components/caeru_autocomplete';
import ErrorDisplay from '../components/caeru_error_display';
import Calendar from '../components/caeru_calendar.vue';

const hub = Hub;

var employee_working_day = new Vue({
    el: 'main#attendance_detail',
    data: {
        workingDayInstanceId: window.working_day_id,
        currentDate: window.current_date,
        currentEmployee: window.current_employee,
        workingInfos: window.working_infos,
        scheduleTransferData : window.schedule_transfer_data,
        alertSettingData : window.alert_setting_data,
        workLocations: window.work_locations,
        timezone: window.timezone,
        canChange: window.can_change_data,

        // Timestamp section
        timestamps: window.working_timestamps,
        timestampTypes: window.timestamp_types,
        timestampPlaces: window.timestamp_places,
        newTimestamp: {
            enable: false,
            processed_date_value: null,
            processed_time_value: null,
            timestamped_type: null,
            work_location_id: null,
            work_address_id: null,
        },
        timestampFormErrors: {
            enable: null,
            processed_date_value: null,
            processed_time_value: null,
            timestamped_type: null,
            work_location_id: null,
            work_address_id: null,
        },
        showTimestampForm: false,

        // General purpose
        daysOfWeek: ['日', '月', '火', '水', '木', '金', '土'],
        sendingRequest: false,
        newWorkingInfoCount: 0,
        showDatePicker: false,
        datePickerOptions: null,
        datePickerTimeNavigationData: null,
        datePickerData: {
            flipColorDay: window.flip_color_day,
            restDays: window.rest_days,
            nationalHolidays: window.national_holidays,
        },

        calendar_rest_day_consts: {
            LAW_BASED_REST_DAY  : 1,
            NORMAL_REST_DAY     : 2,
            NOT_A_REST_DAY      : 0,
        },
    },
    computed: {
        currentDateInArray: {
            get: function() {
                return (this.datePickerTimeNavigationData === null) ? _.map(_.split(this.currentDate, '-'), (value) => {return _.toInteger(value)}) : this.datePickerTimeNavigationData;
            },
            set: function(data) {
                this.datePickerTimeNavigationData = data;
            },
        },
    },
    methods: {
        // validate the time format type
        validateTimeFormat: function(string) {
            if (string.indexOf(':') === -1) {
                let temp = moment(string, 'Hmm', true);
                if (temp.isValid())
                    return temp.format('H:mm');
            } else {
                let temp = moment(string, 'H:mm', true);
                if (temp.isValid())
                    return temp.format('H:mm');
            }
            return false;
        },

        // Get the string of the date in japanese
        formatDate: function(date_string) {
            if (!!date_string) {
                let date = new Date(date_string);
                return date.getFullYear() + '年' + (date.getMonth()+1) + '月' + date.getDate() + '日(' + this.daysOfWeek[date.getDay()] + ')';
            }
        },

        // These function are for the date navigation panel
        nextDay: function() {
            let currentDate = new Date(this.currentDate);
            currentDate.setDate(currentDate.getDate()+1);
            return this.setDateToLink(currentDate);
        },
        previousDay: function() {
            let currentDate = new Date(this.currentDate);
            currentDate.setDate(currentDate.getDate()-1);
            return this.setDateToLink(currentDate);
        },
        setDateToLink: function(date_object) {
            let link_parts = _.split(window.location.pathname, '/');
            return _.join(_.initial(link_parts), '/') + '/' + date_object.getFullYear() + '-' + _.padStart((date_object.getMonth()+1), 2, '0') + '-' + _.padStart(date_object.getDate(), 2, '0');
        },

        // create an empty WorkingInformation
        createNewWorkingInfo: function() {
            let newUniqueId = 'new_info_' + this.newWorkingInfoCount;
            let newData = { id : newUniqueId, new: true };
            this.workingInfos.push(newData);
            this.newWorkingInfoCount++;
        },

        // Remove WorkingInformation
        removeWorkingInfo: function(id) {
            this.workingInfos.splice(_.findIndex(this.workingInfos, (info) => {return info.id === id}), 1)
        },

        // After a WorkingInformation was saved successfully
        workingInfoSaved: function(oldId, data, newOrNot, newScheduleTransferData, newAlertData) {
            // Re-assign all the neccessary data

            let info = _.find(this.workingInfos, (instance) => {return instance.id === oldId});
            if (newOrNot === true) info.new = false;
            _.forEach(data, (value, key) => {
                info[key] = value;
            });

            let transferInfo = _.find(this.scheduleTransferData, (instance) => {return instance['working_info_id'] === data.id});
            if (transferInfo !== undefined) {
                _.forEach(newScheduleTransferData, (value, key) => {
                    transferInfo[key] = value;
                })
            } else {
                this.scheduleTransferData.push(newScheduleTransferData);
            }

            let alertData = _.find(this.alertSettingData, (instance) => {return instance['working_info_id'] === data.id});
            if (alertData !== undefined) {
                _.forEach(newAlertData, (value, key) => {
                    alertData[key] = value;
                })
            } else {
                this.alertSettingData.push(newAlertData);
            }
        },

        // Extract the schedule_transfer_data by working_info_id
        extractScheduleTransferData: function(workingInfoId) {

            let extractedData = _.find(this.scheduleTransferData, (data) => {
                return data['working_info_id'] === workingInfoId;
            });
            return (extractedData !== undefined) ? extractedData : null;
        },
        // Extract the alert setting data by working_info_id
        extractAlertSettingData: function(workingInfoId) {

            let extractedData = _.find(this.alertSettingData, (data) => {
                return data['working_info_id'] === workingInfoId;
            });
            return (extractedData !== undefined) ? extractedData : null;
        },

        // Transfer working information
        scheduleTransfer: function(day) {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                let url = '/schedule_transfer';
                let data = {
                    employee_id:    this.currentEmployee.id,
                    from_date:      this.currentDate,
                    to_date:        day,
                }

                axios.post($.companyCodeIncludedUrl(url), data).then(response => {

                    this.sendingRequest = false;
                    window.location.reload();

                }).catch(error => {
                    if (error.response) {
                        document.caeru_alert('error', '');
                        this.sendingRequest = false;
                    }
                });
            }
        },

        // show the newTimestamp form
        createTimestamp: function() {
            this.showTimestampForm = true;
        },

        // Get the next day date_string
        tomorrow: function() {
            return moment(this.currentDate, 'YYYY-MM-DD').add(1, 'days').format('YYYY-MM-DD');
        },

        // Validate the input and set value
        insertDateToNewTimestamp: function() {
            let new_value = $('#newTimestamp').val();
            let valid_value = this.validateTimeFormat(new_value);
            if (valid_value !== false && valid_value !== this.newTimestamp.processed_time_value)
                this.newTimestamp.processed_time_value = valid_value;
        },

        placeSelected: function(index) {
            if (index !== null) {
                this.newTimestamp.work_location_id = this.timestampPlaces[index]['work_location_id'];
                this.newTimestamp.work_address_id = this.timestampPlaces[index]['work_address_id'];
            }
        },

        // Show the validation error for the newTimestamp form
        showError: function(returnedErrors) {
            if (returnedErrors != null) {
                for (var key in returnedErrors) {
                    this.timestampFormErrors[key] = returnedErrors[key][0];
                }
            } else {
                for (var key in this.timestampFormErrors) {
                    this.timestampFormErrors[key] = null;
                }
            }
        },

        // send the request to create a new WorkingTimestamp
        sendNewTimestamp: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                let url = '/working_timestamp/' + this.workingDayInstanceId;

                axios.post($.companyCodeIncludedUrl(url), this.newTimestamp).then(response => {

                    document.caeru_alert('success', response.data['success']);
                    this.showError(null);
                    this.timestamps = response.data['timestamps'];
                    this.showTimestampForm = false;
                    this.resetTheNewTimestamp();
                    this.sendingRequest = false;
                    this.refreshWorkingInformations();

                }).catch(error => {
                    if (error.response) {

                        document.caeru_alert('error', '');
                        this.showError(null);
                        this.showError(error.response.data);
                        this.sendingRequest = false;
                    }
                })
            }
        },

        // toggle the status of a WorkingTimestamp
        toggleStatusTimestamp: function(key) {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                let url = '/working_timestamp/' + this.timestamps[key]['id']
                let data = {
                    'enable': this.timestamps[key]['enable'],
                }

                axios.patch($.companyCodeIncludedUrl(url), data).then(response => {

                    document.caeru_alert('success', response.data['success']);
                    this.sendingRequest = false;
                    this.refreshWorkingInformations();

                }).catch(error => {
                    if (error.response) {

                        document.caeru_alert('error', '');
                        this.timestamps[key]['id'] = !this.timestamps[key]['id'];
                        this.sendingRequest = false;
                    }
                })
            }
        },

        // After the user change something in the working timestamp section, refresh the working info section
        refreshWorkingInformations: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                let url = '/employee_working_day_by_id/' + this.workingDayInstanceId;

                axios.get($.companyCodeIncludedUrl(url)).then(response => {

                    this.workingInfos = response.data['working_infos'];
                    this.scheduleTransferData = response.data['schedule_transfer_data'];
                    this.sendingRequest = false;
                }).catch(error => {
                    if (error.response) {

                        document.caeru_alert('error', '');
                        this.sendingRequest = false;
                    }
                })
            }
        },

        resetTheNewTimestamp: function() {
            this.newTimestamp = {
                enable: false,
                processed_date_value: null,
                processed_time_value: null,
                timestamped_type: null,
                work_location_id: null,
                work_address_id: null,
            };
        },

        // DatePicker
        toggleDatePicker: function() {
            this.showDatePicker = !this.showDatePicker;
            if (this.showDatePicker === true) {
                this.$nextTick(function() {
                    this.repositionByHeight();
                });
            }
        },
        datePickerChangeTime: function(year, month) {
            this.datePickerTimeNavigationData = [year, month];
            this.processDatePickerOptions();
        },
        goToThisDay: function(date) {
            let urlParts = _.split(window.location.href, '/');
            urlParts.pop();
            urlParts.push(date);
            window.location.href = _.join(urlParts, '/');
        },
        filterByYearAndMonth: function(collection) {
            let filtered = _.filter(collection, (item) => {
                return (item[0] === this.currentDateInArray[0]) && (item[1] === this.currentDateInArray[1]);
            });
            return _.map(filtered, (item) => {return item[2];});
        },
        processDatePickerOptions: function() {
            let nationalHolidays = [];
            let lawRestDay = [];
            let normalRestDay = [];

            _.forEach(this.datePickerData['restDays'], (day) => {
                if (day['type'] === this.calendar_rest_day_consts['LAW_BASED_REST_DAY']) {
                    lawRestDay.push( _.map(_.split(day['assigned_date'], '-'), (data) => { return _.toInteger(data)}) );
                } else if (day['type'] === this.calendar_rest_day_consts['NORMAL_REST_DAY']) {
                    normalRestDay.push( _.map(_.split(day['assigned_date'], '-'), (data) => { return _.toInteger(data)}) );
                }
            });

            nationalHolidays = _.map(this.datePickerData['nationalHolidays'], (day) => {
                return _.map(_.split(day, '-'), (data) => { return _.toInteger(data)});
            });

            this.datePickerOptions = {
                'year' : this.currentDateInArray[0],
                'month' : this.currentDateInArray[1],
                'nationalHolidays' : this.filterByYearAndMonth(nationalHolidays),
                'lawRestDay' : this.filterByYearAndMonth(lawRestDay),
                'normalRestDay' : this.filterByYearAndMonth(normalRestDay),
                'flipColorDay' : this.datePickerData['flipColorDay'],
                'startColor' : (this.currentDateInArray[1] % 2) === 0,
                'pickerMode' : true,
            }
        },
        repositionByHeight: function() {
            let popUp = $('.normal_date_picker .caeru_calendar_date_picker_popup');
            let diffHeight = window.innerHeight - popUp.outerHeight();
            let diffWidth = window.innerWidth - popUp.outerWidth();
            let scrollOffset = $(window).scrollTop();
            popUp.offset({ top: (scrollOffset + diffHeight/2), left: diffWidth/2 });
        }
    },
    created: function() {
        this.processDatePickerOptions();
    },
    mounted: function() {
        this.repositionByHeight();
        this.$nextTick(function() {
            window.addEventListener('resize', this.repositionByHeight);
        });
    },
    components: {
        'working-info' : WorkingInfo,
        'autocomplete' : Autocomplete,
        'error-display' : ErrorDisplay,
        'calendar': Calendar,
    }
})