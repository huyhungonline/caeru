<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">


<title>Intro to vue.js - Just Laravel</title>

<!-- Fonts -->
<link rel="stylesheet"
	href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<!-- Styles -->
<style>
html, body {
	background-color: #fff;
	color: #636b6f;
	font-family: 'Raleway', sans-serif;
	font-weight: 100;
	height: auto;
	margin: 0;
}

.full-height {
	min-height: 100vh;
}

.flex-center {
	align-items: center;
	display: flex;
	justify-content: center;
}

.position-ref {
	position: relative;
}

.top-right {
	position: absolute;
	right: 10px;
	top: 18px;
}

.content {
	text-align: center;
}

.title {
	font-size: 84px;
}

.m-b-md {
	margin-bottom: 30px;
}
body{
	width:800px;
	margin:0 auto;
	padding:10px;
}
</style>
	<script src="{{ asset('/js/bootstrap.js') }}" type="text/javascript"></script>

	<script src="https://unpkg.com/vue"></script>
</head>
<body>

<div id="ex9">
	<input type="radio" id="one" v-on:click="changeData()" value="Yêu" v-model="picked">
	<label for="one">Yêu</label>
	<br>
	<input type="radio" id="two" value="Không yêu" v-model="picked">
	<label for="two">Không yêu</label>
	<br>
	<span>Bạn chọn: @{{ picked }}</span><br>
	<input type="checkbox" id="jack" value="Jack"  required v-model="newItem.name1">
	<label for="jack">Jack</label>
	<input type="checkbox" id="john" value="John"  required v-model="newItem.name2">
	<label for="john">John</label>
	<input type="checkbox" id="mike" value="Mike"  required v-model="newItem.name3">
	<label for="mike">Mike</label>
	<br>
	<span>Checked names: @{{ checkedNames }}</span><br>
	<button v-on:click="createItem()">Add </button>
	<div class="table table-borderless" id="table">
		<table class="table table-borderless" id="table">
			<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Age</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tr>
				<td>@{{ i.id }}</td>
				<td>@{{ i.name }}</td>
				<td>@{{ i.age }}</td>
				<td @click.prevent="deleteItem(item)" class="btn btn-danger"><span
							class="glyphicon glyphicon-trash"></span></td>
			</tr>
		</table>
	</div>
</div>

<div id="app"></div>

    <div id="abc">
        <p>@{{ count }}</p>
        <child v-on:counter="countPlus"></child>
        <child v-on:counter="countPlus"></child>
	</div>
<div id="ap">
	<child></child>
</div>





{{--<script src="{{ asset('/js/main.js') }}" type="text/javascript"></script>--}}

<div class="container" style="padding-top:50px;">
	<autocomplete :suggestions="cities" :selection.sync="value"></autocomplete>
</div>
</div>



{{--<div id="ex6">--}}
	{{--<input type="text" placeholder="what are you looking for?" v-model="query" v-on:keyup="autoComplete" class="form-control">--}}
	{{--<div class="panel-footer" v-if="results.length">--}}
		{{--<ul class="list-group">--}}
			{{--<li class="list-group-item" v-for="result in results">--}}
				{{--@{{ result.name }}--}}
			{{--</li>--}}
		{{--</ul>--}}
	{{--</div>--}}
	{{--<button v-on:click="numberPlus()">Test </button>--}}
{{--</div>--}}
<div id="ex6">
	<input type="text" placeholder="what are you looking for?" v-model="query" v-on:keyup="autoComplete" class="form-control"
		   @keydown.enter = 'enter'
		   @keydown.down = 'down'
		   @keydown.up = 'up'
		   @input = 'change'
	>
	<div class="panel-footer" v-if="results.length">
		<ul class="list-group">
			<li class="list-group-item" v-for="result in results"

				@click="suggestionClick($index)"
			>
				@{{ result.name }}

			</li>
		</ul>
	</div>
	<button v-on:click="numberPlus()">Test </button>
</div>
<script>
    var vp = new Vue({
        el: '#ex6',
        data(){
            return {
                query: '',
                datas: window.data_test,
                results: [],
				test: ''
            }
        },
        methods: {
            autoComplete(){


                this.results = [];


                    axios.get('/itz/search',{params: {query: this.query}}).then(response => {
                         alert(this.datas);
                        this.results = response.data;

                    });

            },
            numberPlus: function ()
            {
                axios.get('/itz/gettime').then(response => {

                    this.test = response.data;
                   alert(this.test);

                });
            },
            enter() {
                alert("aaaaaa");
            },

            up() {
                alert("aaaaaa");
            },

            down() {
                alert("aaaaaa");
            },
            suggestionClick(index) {
                alert(index);
            },


        }

    });
</script>

<script src="dist/build.js"></script>

<script type="text/javascript">
    Vue.component('child', {
        template: '#h1-template'
    });
    var app = new Vue({
        el: '#ap',
    });
</script>
<script type="text/javascript">
    Vue.component('child', {
        template: '<button v-on:click="numberPlus">@{{ count }}</button>',
        data : function () {
            return {count: 0};
        },
        methods: {
            numberPlus: function ()
            {
                this.count += 1;
                this.$emit('counter')
            }
        }
    });
    var app = new Vue({
        el: '#abc',
        data: {
            count: 0
        },
        methods: {
            countPlus: function ()
            {
                this.count += 1;
            }
        }
    });
</script>

<script>
    var childcomp = Vue.extend({
        props: ["myname"],
        template: '<div>Childs myname prop value: @{{myname}}    <button @click="updatename">Click to update child prop and emit back to parent</button></div>',
        methods: {
            updatename: function() {
                this.myname = "new name" ;
                this.$emit("nameupdated", this.myname);
            }
        }
    });

    Vue.component("parent", {
        components: { child: childcomp },
        template: '<div>Parents data name value: @{{name}} <hr><child :myname="name" @nameupdated="name=$event"></child></div>',
        data: function() {
            return { name: "original name" };
        }
    });

    new Vue({
        el: "#app",
        template: "<div><parent></parent></div>"
    });

</script>
<script>
    var vm = new Vue({
        el: '#ex9',

        data: {
            i: "",
			luck: "",
            items: [],
            picked: "",
            checkedNames: [],
			newItem : {'name1':'a','name2':'b','name3':'c'}
        },
        mounted : function(){
            this.getVueItems();
        },
        computed: {

			getVueItems: function(){
				axios.get('/getdata').then(response => {
					this.i = response.data;
			});
			}
        },
        created: function () {
            var input = this.newItem;
            axios.post('/vui',input)
                .then(response => {
                this.picked = {'name1':response.data.name};
            this.i = response.data;

        });

        },

        methods: {
            getVueItems: function(){
                axios.get('/vueitems').then(response => {
                    this.items = response.data;

            });
            },
            created: function () {
               this.luck = "aaaa";
            },
            say: function (message) {
                alert(message + '!');
            },
            changeData: function () {

                var input = this.picked;
                axios.post('http://localhost:8000/vui',input)
                    .then(response => {

                this.i = response.data;

                //  this.getVueItems();
            });
            },
            createItem: function () {
                this.checkedNames = this.newItem;
                var input = this.newItem;
                axios.post('http://localhost:8000/vui',input)
                    .then(response => {
                    this.picked = {'name1':response.data.name};
                    this.i = response.data;

                  //  this.getVueItems();
            });
            }
        }
    });
</script>

	<script type="text/javascript" src="/js/app.js"></script>
</body>
</html>