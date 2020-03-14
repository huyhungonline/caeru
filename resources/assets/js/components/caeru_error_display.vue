<template>
    <div class="input_wrapper" :class="{ error: toggle, radioes: extraClass=='radioes', checkboxes: extraClass=='checkboxes' }" >
        <slot>
        </slot>
        <div class='error_wrapper'>
            <span class="tool_error">{{ message }}</span>
        </div>
    </div>    
</template>
<script>
export default {
    props: ['message', 'extraClass'],
    computed: {
        toggle: function() {
            return this.message != null;
        },
    },
}
</script>