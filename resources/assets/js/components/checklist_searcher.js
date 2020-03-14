import Hub from '../components/hub.js';
import Autocomplete from '../components/caeru_autocomplete';

const hub = Hub;
// Search when change Month and Year
var now = new Date();
var currentYear = now.getFullYear();
var currentMonth = now.getMonth() + 1;
var searcherDay = new Vue({
    el: '.checklist-search',
    data: () => ({
        yearSelected:currentYear,
        monthSelected:currentMonth,
        checklists: window.checklists,
        display: true,
        displayHistory: false,
        employeeId: "",
        employeeName: "",
        checkedEr:[],
        totaldakoku:0,
        totalhyou:0,
        checklistsHistory: window.checklistsHistory,
    }),
    methods: {
        yearChanged: function(){
            this.submit();
        },
        monthChanged: function(){
            this.submit();
        },
        nextMonth: function() {
            this.monthSelected++;
            if(this.monthSelected > 12) {
                this.yearSelected++;
                this.monthSelected = 1;
            }
            this.submit();
        },
        preMonth: function() {
            this.monthSelected--;
            if(this.monthSelected < 1) {
                this.yearSelected--;
                this.monthSelected = 12;
            }
            this.submit();
        },
        init: function(){
            this.beginDay = (new Date(this.checklists.beginDate.date)).getDate();
            this.endDay = (new Date(this.checklists.endDate.date)).getDate();
         
            this.beginMonth = this.monthSelected==1?12:this.monthSelected - 1;
            this.endMonth = this.monthSelected;

            this.beginYear = this.monthSelected==1?this.yearSelected-1:this.yearSelected;
            this.endYear = this.yearSelected;

        },
        toggle: function(){
            this.display = !this.display;
        },
        changConditions: function(){
            this.display = true;
            this.displayHistory = false;
        },
        resetConditions: function(){
            Object.assign(this.$data, this.$options.data.call(this));
            this.submit(true);
        },
        submit: function(refreshSession){
            var that = this;
            var data = {
                'year': this.yearSelected,
                'month': this.monthSelected,
                'errlist': this.checkedEr,
                'employeeId': this.employeeId,
                'employeeName': this.employeeName,
                'refreshSession': refreshSession || false,
                '_token': $("[name='csrf-token']").attr('content')
            };
            this.init();
            axios.post($.companyCodeIncludedUrl('/checklist/search'), data).then(response => {
             //window.location.reload();
             checklistView.checklists = that.checklists = response.data.checklistsJson;
             that.checklistsHistory = response.data.checklistsHistory;
            
            that.totaldakoku = that.checklists.totaldakoku || 0;
            that.totalhyou = that.checklists.totalhyou || 0;
            }); 
        },
    },
    created: function() {
        this.init();
        this.employeeId = this.checklistsHistory.employeeId || '';
        this.employeeName = this.checklistsHistory.employeeName ||'';
        this.checkedEr = this.checklistsHistory.errlist || [];
        this.yearSelected = this.checklistsHistory.yearHistory || currentYear;
        this.monthSelected = this.checklistsHistory.monthHistory || currentMonth;

        this.totaldakoku = this.checklists.totaldakoku || 0;
        this.totalhyou = this.checklists.totalhyou || 0;
        this.displayHistory = window.checklistsDisplayHistory;
        this.display = false;
    }
});

var checklistView = new Vue({
    el: '.check_table',
    data: {
        checklists: window.checklists,
    },
    filters : {
        formatDate : function(dispalydate){
            var displayday;
            var dateOfWeek = (new Date(dispalydate)).getDay();
            displayday = dispalydate.replace(/-/g,'/');
            if(dateOfWeek==0) {
                displayday = displayday + "(日)";
            }
            if(dateOfWeek==1) {
                displayday = displayday + "(月)";
            }
            if(dateOfWeek==2) {
                displayday = displayday + "(火)";
            }
            if(dateOfWeek==3) {
                displayday = displayday + "(水)";
            }
            if(dateOfWeek==4) {
                displayday = displayday + "(木)";
            }
            if(dateOfWeek==5) {
                displayday = displayday + "(金)";
            }
            if(dateOfWeek==6) {
                displayday = displayday + "(日)";
            }
            return displayday;
        }
    }
});

