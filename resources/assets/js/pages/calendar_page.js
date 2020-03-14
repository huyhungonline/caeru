import Calendar from '../components/caeru_calendar';

var app = new Vue({
    el: 'section#calendar_container',
    data: {
        restDays :  window.rest_days,
        workTimes : window.work_times,
        flipDay :   window.accounting_day,
        currentYear: (new Date()).getFullYear(),
        workLocation: (!!window.work_location_id) ? window.work_location_id : null,
        touchData: {
            restDays: {},
            totalWorkTimes: {},
        },
        optionsArray:   [null, null, null, null, null, null, null, null, null, null, null, null],
        key_api: 'AIzaSyDeZb8TyMsZ-5kfvg38uQQURZBx4bjwX4c',
        calendar_id: 'japanese__ja@holiday.calendar.google.com',
        sendingRequest: false,
    },
    methods: {
        initializeOptionsArray: function() {
            this.optionsArray = _.map(this.optionsArray, (value, index) => {
                return {
                    year: this.currentYear,
                    pickerMode: false,
                    nationalHolidays: [],
                    lawRestDay: [],
                    normalRestDay: [],
                    flexTotalTime: null,
                    startColor: (index % 2) === 0,
                    flipColorDay: this.flipDay,
                };
            });
        },
        submit: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                // First, we have to find the differences between the current data and the original
                // Then, we're gonna send the differences to the server, ONLY the differences.
                var restDaysDiff = this.getRestDaysDiff();
                var workTimesDiff = this.getWorkTimesDiff();

                var data = {
                    'changed_rest_days'     : restDaysDiff,
                    'changed_work_times'    : workTimesDiff,
                }
                // Check if this is a single work location. If so, add the work location id to the load.
                if (!!this.workLocation)
                    data['id'] = this.workLocation;

                // Then we send them.
                axios.post($.companyCodeIncludedUrl('/calendar'), data).then(response => {

                    // If this is the first time this company's calendar was saved, then the page need to be refreshed
                    if (response.data['refresh'] === true)
                        location.reload();
                    else
                        document.caeru_alert('success', response.data['success']);

                    this.sendingRequest = false;

                }).catch(error => {
                    if (error.response) {
                        document.caeru_alert('error', '');
                        console.log(error.response);
                    }
                    this.sendingRequest = false;
                });

            }
        },


        // Get the differences
        getRestDaysDiff: function() {
            var diff = [];
            _.forEach(this.touchData.restDays, (value, index) => {
                if (_.has(this.restDays, index)) {
                    if (this.restDays[index].type !== value)
                        diff.push({day: index, type: value });
                } else {
                    if (value !== 0)
                        diff.push({day: index, type: value});
                }
            })
            return diff;
        },
        getWorkTimesDiff: function() {
            var diff = [];
            _.forEach(this.touchData.totalWorkTimes, (value, index) => {
                if (_.has(this.workTimes, index)) {
                    if (this.workTimes[index].time !== value)
                        diff.push({month: index, time: value});
                } else {
                    if (value !== 0)
                        diff.push({month: index, time: value});
                }
            })
            return diff;
        },


        // Listen to change event from the components
        restDayChanged: function(data) {
            this.touchData.restDays[data.day] = data.status;
        },
        flexTotalTimeChanged: function(data) {
            this.touchData.totalWorkTimes[data.month] = _.toNumber(data.time);
        },

        // Distribute the data to the twelve option objects of twelve months
        distributeRestDays: function(allData) {
            _.forEach(allData, (data, time) => {
                var month   =   _.split(time, '-')[1];
                var day     =   _.split(time, '-')[2];
                if (data.type == 1)
                    this.optionsArray[month-1].lawRestDay.push(_.toSafeInteger(day));
                else if (data.type == 2)
                    this.optionsArray[month-1].normalRestDay.push(_.toSafeInteger(day));
            });
        },
        distributeWorkTimes: function(allData) {
            _.forEach(allData, (data) => {
                var month   =   data.month;
                this.optionsArray[month-1].flexTotalTime = data.time;
            });
        },
        getAndDistributeNationHolidaysFromGoogleAPI: function(){
            // The link to use google api
            var url = 'https://www.googleapis.com/calendar/v3/calendars/'+ this.calendar_id +
            '/events?key=' + this.key_api+ '&timeMin='+ this.currentYear + '-01-01T00%3A00%3A00.000Z' + '&timeMax=' +
            this.currentYear + '-12-31T00%3A00%3A00.000Z' + '&maxResults=100&orderBy=startTime&singleEvents=true';

            axios.get(url).then(response => {
                _.forEach(response.data.items, (data) => {
                    var month = _.split(data.start.date, '-')[1];
                    var day = _.split(data.start.date, '-')[2];
                    this.optionsArray[month-1].nationalHolidays.push(_.toSafeInteger(day));
                })
            }).catch(error => {
                console.log(error);
            });
        },


        // Load data of another year
        getDataOfNextYear: function() {
            this.currentYear += 1;
            this.getDataOfAnotherYear(this.currentYear);
        },
        getDataOfPreviousYear: function() {
            this.currentYear -= 1;
            this.getDataOfAnotherYear(this.currentYear);
        },
        getDataOfAnotherYear: function(year) {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                axios.get($.companyCodeIncludedUrl('/calendar/') + year).then(response => {
                    this.initializeOptionsArray();
                    this.distributeRestDays(response.data.calendar_rest_days);
                    this.distributeWorkTimes(response.data.total_work_times);
                    this.getAndDistributeNationHolidaysFromGoogleAPI();
                    this.sendingRequest = false;
                }).catch(error => {
                    console.log(error.response);
                    this.sendingRequest = false;
                })

            }
        },

    },
    created: function() {
        this.initializeOptionsArray();
        this.distributeRestDays(window.rest_days);
        this.distributeWorkTimes(window.work_times);
        this.getAndDistributeNationHolidaysFromGoogleAPI();
    },
    components: {
        calendar: Calendar,
    },
})