<template>
    <section class="pager">
        <template v-if="sumLine">
            <p v-if="total === 0">0件表示</p>
            <p v-else>{{ total }}件中{{ from }}〜{{ to }}件表示</p>
        </template>
        <ul :class="customClass" v-if="total !== 0">
            <li class="left" v-for="page in paginatedPages">
                <a class="active" v-if="page==currentPage">{{ page }}</a>
                <a @click="changePage(page)" class="approval_paginate" v-else-if="isValid(page)">{{ page }}</a>
                <span v-else>{{ page }}</span>
            </li>
        </ul>
    </section>
</template>
<script>
export default {
    props: {
        total: {
            type: Number,
            required: true,
        },
        currentPage: {
            type: Number,
            required: true,
        },
        perPage: {
            type: Number,
            required: true,
        },
        sumLine: {
            type: Boolean,
            required: false,
            default: true,
        },
        customClass: {
            type: String,
            required: false,
            default: '',
        },
    },
    computed: {
        from: function() {
            return ((this.currentPage-1) * this.perPage) + 1;
        },
        to: function() {
            var to = this.currentPage * this.perPage;
            return (to <= this.total) ? to : this.total;
        },
        paginatedPages: function() {
            var pageNum = _.floor(this.total / this.perPage);
            pageNum = (this.total % this.perPage) !== 0 ? pageNum +1 : pageNum;
            var current = this.currentPage;
            if ((pageNum > 10) && (current <= 6))
                return [1,2,3,4,5,6,7,8,'...',pageNum-1,pageNum];
            else if ((pageNum >10) && (current > pageNum - 6))
                return [1,2,'...',pageNum-7,pageNum-6,pageNum-5,pageNum-4,pageNum-3,pageNum-2,pageNum-1,pageNum];
            else if (pageNum > 12)
                return [1,2,'...',current-3,current-2,current-1,current,current+1,current+2,current+3,'...',pageNum-1,pageNum];
            else
                return Array.from(new Array(pageNum),(val,index)=>index+1);
        },
    },
    methods: {
        changePage: function(newPage) {
            this.$emit('changed', newPage);
        },
        isValid: function(page) {
            return _.isInteger(page);
        }
    },
}
</script>