// The root element
var app = new Vue({
    el: '#form',
    data: {
        placePickerDisplay : false,
        workLatitude: $("[name='latitude']").val(),
        workLongitude: $("[name='longitude']").val(),
        address: $("[name='address']").val(),
    },
    methods: {
        toggle: function() {
            this.placePickerDisplay = !this.placePickerDisplay;
        },
        changeGeocode: function(latitude, longitude) {
            this.workLatitude = latitude;
            this.workLongitude = longitude;
        },
        changeAddress: function(address) {
            this.address = address;
        },
    }
})