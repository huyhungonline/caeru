import Hub from '../components/hub.js';
import Autocomplete from '../components/caeru_autocomplete';

const hub = Hub;

// Search when change Month and Year
 var now = new Date();
 var currentYear = now.getFullYear();
 var currentMonth = now.getMonth() + 1;
var searcher = new Vue({
    el: '.totalization-search',
    data: () => ({
        yearSelected:currentYear,
        monthSelected:currentMonth,
        totalizations: window.totalizations,
        display:true,
        display: true,
        employeeId: "",
        employeeName: "",
        cklist:[],
        displayHistory:false,
        totalizationsHistory:window.totalizationsHistory,
    }),
    methods:{
    	yearChanged: function(){
    		this.submit();
    	},
    	monthChanged: function(){
    		this.submit();
    	},
    	nextMonth: function(){
    		this.monthSelected++;
    		if(this.monthSelected >12){
    			this.yearSelected++;
    			this.monthSelected=1;
    		}
    		this.submit();
    	},
    	preMonth: function(){
    		this.monthSelected--;
    		if(this.monthSelected<1){
    			this.yearSelected--;
    			this.monthSelected = 12;
    		}
    		this.submit();
    	},
    	init: function(){
            this.beginDay = (new Date(this.totalizations.beginDate.date)).getDate();
            this.endDay = (new Date(this.totalizations.endDate.date)).getDate();

            this.beginMonth = this.monthSelected==1?12:this.monthSelected-1;
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
                'cklist': this.cklist,
                'employeeId': this.employeeId,
                'employeeName': this.employeeName,
                 'refreshSession': refreshSession || false,
                '_token': $("[name='csrf-token']").attr('content')
            };
            this.init();
            axios.post($.companyCodeIncludedUrl('/totalization'), data).then(response => {
                totalizationView.totalizations = that.totalizations = response.data.totalizationsJson;
                that.totalizationsHistory = response.data.totalizationsHistory;
            }); 
    	}
    },
    created: function(){
        this.init();
        this.employeeId = this.totalizationsHistory.employeeId || '';
        this.employeeName = this.totalizationsHistory.employeeName || '';
        this.cklist = this.totalizationsHistory.cklist || [];
        this.yearSelected = this.totalizationsHistory.yearHistory || currentYear;
        this.monthSelected = this.totalizationsHistory.monthHistory || currentMonth;

        this.displayHistory = window.totalizationsHistory;
        this.display = false;
    }
 });

var totalizationView = new Vue({
    el: '.approval_table',
    data: {
        totalizations: window.totalizations,
    },
    methods: {
    },
});
