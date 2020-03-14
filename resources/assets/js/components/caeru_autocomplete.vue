<template>
    <div class="caeru_autocomplete_wrapper" :class="{'open_list':openSuggestion}">
        <input :class="customClass" type="text" v-model="value"
            @input = 'updateValue'
            @blur = 'blur'
            @keydown.enter.prevent = 'enter'
            @keydown.down.prevent = 'down'
            @keydown.up.prevent = 'up'
            @keydown.esc.prevent = 'escape'
        >
        <div class="caeru_autocomplete_inner">
            <ul>
                <li v-for="(suggestion, index) in matches"
                    :class="{'active': isActive(index)}"
                    @click="select"
                    @mouseover="hover(index)"
                    @mouseleave="mouseLeave"
                >
                    <div class="caeru_autocomplete_row">
                        <span :class="{'right':descriptionDisplay.flip}">{{ suggestion[filteredFieldName] }}</span>
                        <span v-if="descriptionDisplay.display"
                            :class="{'left':descriptionDisplay.flip}"
                        >{{ suggestion.description }}</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
// Import the event hub to listen to the reset-autocomplete event
import Hub from './hub.js';
const hub = Hub;

export default {

    props: {

        // Data to be filtered on. This array contains, at least,  an 'id' field and a 'name' field.
        // There's maybe 'discription' or some other fields as well.
        suggestions: {
            type: Array,
            required: true
        },

        customClass: {
            type: String,
            required: false,
        },

        // This is not the actual descriptions, it's the option to display the discription( display or not, flip side or not)
        // The discriptions are provided alongside with the suggestions
        description: {
            type: Object,
            required: false,
        },

        // These initial properties is for providing a initial value of the input,
        // sometimes, you need to provide an it, sometimes, you need to provide a value
        // But don't provide both of them. If that happends, the initial value will be ignored
        initialId: {
            type: Number,
            required: false,
            default: null,
        },
        initialValue: {
            type: String,
            required: false,
            default: null,
        },

        // linked and current id usually go together, when 'linked' the autocomplete's input box will be updated according to the current id
        linked: {
            type: Boolean,
            required: false,
            default: false,
        },
        currentId: {
            type: Number,
            required: false,
            default: null,
        },

        // In case, you want to filter on another field other than the 'name' field (i.e. 'presentation_id'). Of couse, you have to provide data that contain that field.
        filteredFieldName: {
            type: String,
            required: false,
            default: 'name',
        },

        // Allow approximate result. A fancy name, but it basically means: the value does not necessarily need to be an exact option at any given time.
        // When approximate result is not allowed. That means: at any given time, the value in the input box must be an axact option from the suggestions
        allowApprox: {
            type: Boolean,
            required: false,
            default: false,
        },


        allowNull: {
            type: Boolean,
            required: false,
            default: true,
        },
    },
    data: function() {
        return {
            selecting: null,
            value: '',
            open: false,
            current: 0,
            hovering: false,
        };
    },
    computed: {
        // Filtering the suggestion based on the input
        matches: function() {
            return this.suggestions.filter((obj) => {
                return obj[this.filteredFieldName].indexOf(this.value) !== -1;
            });
        },

        openSuggestion: function() {
            // return true;
            return this.value !== '' &&
                this.matches.length !== 0 &&
                this.open === true;
        },

        descriptionDisplay: function() {
            return (!!this.description) ? this.description : { display: false, flip: false};
        },

    },
    watch: {
        currentId: {
            handler: function(newId) {
                if (!!this.linked) {
                    if (newId !== null) {
                        this.value = this.suggestions[newId][this.filteredFieldName];
                        this.selecting = newId;
                    } else {
                        this.value = '';
                        this.selecting = null;
                    }

                }
            },
        }
    },
    methods: {

        // When start typing
        updateValue (value) {
            if (!!this.allowApprox) {
                this.$emit('changed', this.value);
            }

            if (this.open === false) {
                this.open = true
                this.current = 0
            }
        },

        //when the option is clicked
        select: function() {
            this.$emit('selected', this.suggestions.indexOf(this.matches[this.current]));
            this.selecting = this.suggestions.indexOf(this.matches[this.current]);
            this.value = this.matches[this.current][this.filteredFieldName];
            this.hovering = false;
            this.open = false;
        },

        // When hover mouse over option
        hover: function(index) {
            this.hovering = true;
            this.current = index;
        },

        // When mouse leave
        mouseLeave: function (){
            this.hovering = false;
        },

        // When the input is blurred
        blur: function() {
            if (this.hovering == false) {
                this.cancel();
            }
        },

        // When enter key pressed on the input
        enter: function() {
            // When the suggestion list is not opened, if the user press 'enter',
            // the component will send out an 'enter-pressed' event. Otherwise, if the value is not null,
            // select the option.
            if (this.open === true && this.value !== ''  && this.matches.length !== 0) {
                this.select();
            } else {
                this.$emit('enter-pressed');
            }
        },

        // When up arrow pressed while suggestions are open
        up: function() {
            if (this.current > 0) {
              this.current--;
            }
        },

        // When down arrow pressed while suggestions are open
        down: function() {
            if (this.current < this.matches.length - 1) {
              this.current++;
            }
        },

        // When press escape button while suggestion are open
        escape: function() {
            this.cancel();
        },

        // Cancel the selection, If the mode allowApprox is not on the the value will either return to the previous value or null
        cancel: function() {
            if (!!this.allowApprox) this.close();
            else {
                if ((this.allowNull === true) && (this.value === '')) {
                    this.selecting = null;
                    this.$emit('selected', null);
                } else {
                    this.value = (this.selecting !== null) ? this.suggestions[this.selecting][this.filteredFieldName] : '';
                    this.$emit('selected', this.selecting);
                    this.close();
                }
            }
        },

        // Reset the value and everything when catch reset-autocomplete event
        reset: function() {
            this.value = null;
            this.selecting = null;
        },

        // Close the suggestions
        close: function() {
            if (this.open === true) this.open = false;
        },

        // For highlighting element
        isActive: function(index) {
            return index === this.current;
        },

    },
    created: function() {
        if (this.initialId !== null) {
            this.selecting = this.initialId;
            this.value = this.suggestions[this.initialId][this.filteredFieldName];

        // This initial value option usually goes with allowApprox option
        } else if (this.initialValue !== null) {
            this.value = this.initialValue;
        }

        // Register event handler for the reset-autocomplete event
        hub.$on('reset-autocomplete', this.reset);


    },
    mounted: function() {
        // Can't find a perfectly-consistent-css-way to do this
        $(this.$el.children[1]).width($(this.$el.children[0]).outerWidth());
    },
    beforeDestroy: function() {
        // Get rid of the event handler for the reset-autocomplete event
        hub.$off('reset-autocomplete', this.reset);
    },

}
</script>
<style>
.caeru_autocomplete_wrapper {
    display: inline-block;
}

.caeru_autocomplete_wrapper ul {
    width: 100%;
}

.caeru_autocomplete_wrapper.open_list  .caeru_autocomplete_inner{
    display: block;
}

.caeru_autocomplete_inner {
    display: none;
    position: absolute;
    z-index: 9999;
    margin: 0;
    border-right: 1px solid #ccc;
    border-left: 1px solid #ccc;
    border-top: 1px solid #ccc;background: #fff;
    box-shadow: 2px 5px 7px -1px #bdbdbd;
    -moz-box-shadow: 2px 5px 7px -1px #bdbdbd;
    -webkit-box-shadow: 2px 5px 7px -1px #bdbdbd;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 6px;
}

.caeru_autocomplete_wrapper li {
    margin: 0!important;
    width: 100%;
    padding:4px 6px;
    border-bottom:1px dashed #ccc;
    display: inline-block;
}

.caeru_autocomplete_wrapper li .caeru_autocomplete_row span{
    font-size: 86%;
}

.caeru_autocomplete_wrapper li:hover {
    cursor: pointer;
}

.caeru_autocomplete_wrapper li.active {
    background: #007bff ;
}
    
.caeru_autocomplete_wrapper li.active span {
    color: white;
}

.caeru_autocomplete_wrapper li:last-child{
    border-style: none;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 6px;
}

.caeru_autocomplete_row .left{
    text-align:left;
    float: left;
}

.caeru_autocomplete_row .right{
    text-align:right;
    float: right;
}

</style>