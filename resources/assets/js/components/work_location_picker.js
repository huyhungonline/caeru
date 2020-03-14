 var app = new Vue({
    el: 'div.worklocation',
    data: {
        display: false,
        displayDisabled: false,
    },
    methods: {
        open: function() {
            this.display = true;
            this.$nextTick(function() {
                this.repositionByHeight();
            });
        },
        toggleDisable: function() {
            this.displayDisabled = !this.displayDisabled;
        },
        close: function() {
            this.display = false;
        },
        repositionByHeight: function() {
            if (!!this.display) {
                let popUp = $('.work_location_picker');
                let diffHeight = window.innerHeight - popUp.outerHeight();
                let diffWidth = window.innerWidth - popUp.outerWidth();
                let scrollOffset = $(window).scrollTop();
                popUp.offset({ top: (scrollOffset + diffHeight/2), left: diffWidth/2 });
            }
        }
    },
    mounted: function() {
        this.repositionByHeight();
        this.$nextTick(function() {
            window.addEventListener('resize', this.repositionByHeight);
        });
    },
})
