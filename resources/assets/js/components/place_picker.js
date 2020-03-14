// The component
Vue.component('place-picker', {
    template: `
        <transition name="fade">
            <div class="place-picker-wrapper" v-show="display">
                <div class="modal-content place-picker" :class="map_wrapper_size_class">
                    <h2>GPS位置情報</h2>
                    <section :class="map_size_class">
                        <div class="map-container"></div>
                        <div class="infowindow-content">
                            <img src="" width="16" height="16" class="place-icon">
                            <span class="place-name title"></span>
                        </div>
                    </section>
                    <section class="second" v-if="!read_only">
                        <div class="right_30 left">
                            <span class="right_10 w_46">緯度</span><input @keydown.enter.prevent class="m_size latitude-input" v-model.number="center.latitude" type="text">
                        </div>
                        <div class="left">
                            <span class="right_10 w_46">経度</span><input @keydown.enter.prevent class="m_size longitude-input" v-model.number="center.longitude" type="text">
                        </div>
                    </section>
                    <section class="second bottom_10" v-if="!read_only">
                        <div class="search_box">
                            <span class="right_10 w_46">地域名</span> <input　@keydown.enter.prevent="geocodingAddress" class="map-search-input l_size right_10" v-model="address" type="text">
                            <p class="button"><a class="s_size s_height btn_greeen" @click="geocodingAddress">検索</a></p>
                        </div>
                    </section>
                    <section class="btn">
                        <p class="button"><a class="mm_size l_height btn_gray l_font" @click="$emit('close')">閉じる</a></p>
                    </section>
                </div>
                <div class="modal-overlay" @click="$emit('close')"></div>
            </div>
        </transition>
    `,
    props: {
        display: {
            type: Boolean,
            required: true,
        },
        lat: {
            required: true,
        },
        lng: {
            required: true,
        },
        read_only: {
            type: Boolean,
            required: true,
        },
        addr: {
            type: String,
            required: false,
            default: null,
        },
    },
    data: function() {
        return {
            mapOptions: {
                zoom: 16,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: {lat: 34.400587, lng: 132.469515},
            },
            map: null,
            marker: null,
            address: null,
            map_size_class: null,
            map_wrapper_size_class: null,

            // Needed for the geocoding
            apiKey : 'AIzaSyCTcCQSwQzudWCAhaAL2HAqtIp7V71_psA',
            sendingRequest: false,
        }
    },
    computed: {
        center: function() {
            return {
                latitude: parseFloat(this.lat) ? parseFloat(this.lat) : null,
                longitude: parseFloat(this.lng) ? parseFloat(this.lng) : null,
            }
        },
    },
    methods: {
        initMap: function() {
            this.map = map = new google.maps.Map($(this.$el).find('.map-container')[0], this.mapOptions);


            this.marker = marker = new google.maps.Marker({
              map: map,
              anchorPoint: new google.maps.Point(0, -29),
              draggable:true,
            });

            // can not drag the marker in read only mode
            if (this.read_only == true){
                this.marker.draggable = false;
            }
            // Add event for the marker
            marker.addListener('dragend', () => {
                this.center.latitude = marker.getPosition().lat();
                this.center.longitude = marker.getPosition().lng();
                this.$forceUpdate();
            });

            // Comment out the autocomplete. Because this behaviour is not desired at the moment

            // var input = $(this.$el).find('.map-search-input')[0];

            // var autocomplete = new google.maps.places.Autocomplete(input);

            // Re-declare this component so that it can be use by the closures
            // var component = this;

            // Add event for the autocomplete
            // autocomplete.addListener('place_changed', function() {
            //     marker.setVisible(false);
            //     var place = autocomplete.getPlace();
            //     if (!place.geometry) {
            //         // User entered the name of a Place that was not suggested and
            //         // pressed the Enter key, or the Place Details request failed.
            //         window.alert("No details available for input: '" + place.name + "'");
            //         return;
            //     }

            //     // If the place has a geometry, then present it on a map.
            //     if (place.geometry.viewport) {
            //         map.fitBounds(place.geometry.viewport);
            //     } else {
            //         map.setCenter(place.geometry.location);
            //     }
            //     marker.setPosition(place.geometry.location);
            //     marker.setVisible(true);

            //     // Change the latitude and longitude AND broadcast it to the form outside
            //     component.center.latitude = place.geometry.location.lat();
            //     component.center.longitude = place.geometry.location.lng();
            //     component.address = place.formatted_address;
            //     component.$forceUpdate();
            // });
        },
        updatePosition: function() {
            var newPosition = {lat: this.center.latitude, lng: this.center.longitude};
            this.marker.setPosition(newPosition);
            this.marker.setVisible(true);
            this.map.setCenter(newPosition);
            this.$emit('change-position', this.center.latitude, this.center.longitude);
        },

        // Geocoding, based on the current address
        geocodingAddress: function() {
            if (!this.sendingRequest) {
                this.sendingRequest = true;

                if (this.address !== null) {
                    this.marker.setVisible(false);
                    let url = 'https://maps.googleapis.com/maps/api/geocode/json?';
                    url = url + 'address=' + this.address + '&key=' + this.apiKey;

                    axios.get(encodeURI(url)).then(response => {
                        var location = response.data.results[0].geometry.location;
                        this.center.latitude = location.lat;
                        this.center.longitude = location.lng;
                        this.$forceUpdate();
                        this.sendingRequest = false;
                    }).catch(error => {
                        if (error.response) {
                            console.log(error.response);
                        }
                        this.sendingRequest = false;
                    });
                }

            }
        }
    },
    watch: {
        // Everytime the user open this window, re-assign value of the address.
        display: function(newValue) {
            if (!!newValue) {
                this.address = this.addr;
            }
        },
        address: function(newValue) {
            this.$emit('change-address', newValue);
        }
    },
    mounted: function() {
        this.map_size_class = this.read_only ? "gmap_position" : "get_gmap_position";
        this.map_wrapper_size_class = this.read_only ? "gmap_wrapper" : "get_gmap_wrapper";
        this.initMap();
    },
    updated: function() {
        google.maps.event.trigger(this.map, "resize");
        if (this.center.latitude != null && this.center.longitude != null) {
            this.updatePosition();
        }
    }
});
