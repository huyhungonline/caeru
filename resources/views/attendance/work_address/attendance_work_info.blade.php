@extends('layouts.master')

@section('title', '管理者詳細')

@section('header')
	@include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
	<script defer src="{{ asset('/js/components/attendance_work_infor.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/js/pages/jquery-3.1.1.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/js/pages/script.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/js/pages/fixed_midashi.js') }}" type="text/javascript"></script>
	<script src="{{ asset('/js/multiple-select.js') }}" type="text/javascript"></script>
@endpush

@section('content')
<script>

</script>
<style>
	.classA {background-color: #d3e7f9;}
	.classB {background-color: #e695b9;}
</style>
<section id="header_weapper">
	<header>
		<a href="index.html">
			<div id="logo"></div></a>
		<div id="sign_out">
			<a href="login.html">ログアウト</a>
		</div>
	</header>
	<nav>
		<ul class="gnav" id="dropmenu">
			<li class="home">
				<a href="index.html"></a>
			</li>
			<li class="green">
				<a class="green" href="#">基本情報</a>
				<ul>
					<li><a href="basic_w_company.html">会社情報</a></li>
					<li><a href="basic_admin_list.html">管理者情報</a></li>
					<li><a href="basic_worklocation_list.html">勤務地情報</a></li>
					<li><a href="basic_visit_place_list.html">訪問先情報</a></li>
					<li><a href="basic_employee_list.html">従業員情報</a></li>
					<li><a href="basic_first_calendar.html">カレンダー</a></li>
					<li><a href="basic_first_config.html">設定</a></li>
					<li><a href="basic_first_formofwork.html">項目設定</a></li>
				</ul>
			</li>
			<li class="yellow">
				<a class="chosen_attendance yellow" href="#">勤怠管理</a>
				<ul>
					<li><a href="attendance_management.html">勤怠データ管理</a></li>
					<li><a href="attendance_search.html">勤怠データ検索</a></li>
					<li><a href="attendance_approval.html">勤怠集計</a></li>
					<li><a href="attendance_holiday_list.html">有給休暇管理</a></li>
					<li><a href="attendance_work_list.html">訪問先別勤務情報</a></li>
					<li><a href="attendance_check_list.html">チェックリスト</a></li>
				</ul>
			</li>
		</ul>
	</nav>
</section>
<main id="attendance">
	<section class="title">
		<p class="breadcrumb"><span>勤怠管理</span><span>&emsp;&#62;&emsp;訪問先別勤務情報</span></p>
		<div class="title_wrapper">
			<h1>訪問先別勤務情報</h1>
			<div class="worklocation">
				<div class="worklocation_inner">
					<span class="right_10">広島本社</span>
					<p class="button"><a class="ss_size s_height btn_gray" data-target="con1">変更</a></p>
				</div>
			</div>
		</div>
	</section>
	<section class="search_box_wrapper">
		<div class="search_box_innner2">
			<div>
				<div class="search_setting">
					<span class="right_10">従業員ID</span>
					<autocomplete :suggestions="employee_names" custom-class="s_size" :linked="true" :allow-null="false" v-model="employee_id"
																	  :initial-id="currentAutocompleteId"
																	  :current-id="currentAutocompleteId"
																	  filtered-field-name="presentation_id"
																	  @selected="currentEmployeeSelected">
					</autocomplete>
				</div>
				<div class="search_setting">
					<span class="right_10">従業員名</span>
					<autocomplete :suggestions="employee_names" custom-class="m_size" :linked="true" :allow-null="false" v-model="employee_name"
																	 :initial-id="currentAutocompleteId"
																	 :current-id="currentAutocompleteId"
																	 @selected="currentEmployeeSelected">
					</autocomplete>
				</div>
				<div class="search_setting">
					<span class="right_10 left">訪問先名</span>
					<div class="selectbox left">
						<select class="mm_size" @change="change_address_id($event.target.value)">
							<option value="0" selected></option>
							<option  v-for = "(item,index) in list_address"  :value="item.id" >@{{ item.name }}</option>

						</select>
					</div>
				</div>
				<div class="button bottom_10 search_setting right_10">
					<a class="s_size s_height btn_greeen" @click="search">検索</a>
				</div>
				<div class="button bottom_10 search_setting">
					<a class="s_size s_height btn_gray" @click="reset" >リセット</a>
				</div>
			</div>
		</div>
	</section>
	<section class="select_one">
		<section class="select_one_inner">
			<section class="right_10"><a @click="preWeek()"><img class="ico_ico_arrow"  src="{{ asset('/images/ico_arrow_left2.svg') }}"></a></section>
			<section class="right_30"><a @click="preDay()"><img class="ico_ico_arrow"  src="{{ asset('/images/ico_arrow_left1.svg') }}"></a></section>
			<section class="right_10"><input class="s_size" v-model = "time_input" type="text"></section>
			<section class="right_10"><a @click="toggleDatePicker()"><img class="ico_ico_arrow"  src="{{ asset('/images/ico_calendar.svg') }}"></a></section>
			<section class="right_10 ll_font">から</section>
			<section class="button right_30"><a class="ss_size s_height btn_greeen" href="#">表示</a></section>
			<section class="right_10"><a @click="nextDay()"><img class="ico_ico_arrow"  src="{{ asset('/images/ico_arrow_right1.svg') }}"></a></section>
			<section><a @click="nextWeek()"><img class="ico_ico_arrow"  src="{{ asset('/images/ico_arrow_right2.svg') }}"></a></section>
		</section>
		<section class="left_position">
			<div class="left right_10 description_correction description_text">修正</div>
			<div class="left right_10 description_no_assignment description_text">未配属</div>
		</section>
	</section>
	<!-- 予定リスト -->
	<div class="work_info_table">
		<table _fixedhead='rows:2; cols:1;'>
			<tr>
				<th rowspan="2">
					<div class="mm_size">予定</div>
				</th>
				<th colspan="5" class="bold_line" v-for = "(item,index) in day_infor"><span class="saturday">@{{ item.month }}月@{{ item.day }}日（@{{ item.weekDay }}）</span></th>

			</tr>
			<tr>
				<th><p class="ss_size">出勤</p></th>
				<th><p class="ss_size">退勤</p></th>
				<th><p class="ss_size">必要人数</p></th>
				<th><p class="mm_size">従業員名</p></th>
				<th class="bold_line"><p class="s_size"></p></th>
				<th><p class="ss_size">出勤</p></th>
				<th><p class="ss_size">退勤</p></th>
				<th><p class="ss_size">必要人数</p></th>
				<th><p class="mm_size">従業員名</p></th>
				<th class="bold_line"><p class="s_size"></p></th>
				<th><p class="ss_size">出勤</p></th>
				<th><p class="ss_size">退勤</p></th>
				<th><p class="ss_size">必要人数</p></th>
				<th><p class="mm_size">従業員名</p></th>
				<th class="bold_line"><p class="s_size"></p></th>
				<th><p class="ss_size">出勤</p></th>
				<th><p class="ss_size">退勤</p></th>
				<th><p class="ss_size">必要人数</p></th>
				<th><p class="mm_size">従業員名</p></th>
				<th class="bold_line"><p class="s_size"></p></th>
				<th><p class="ss_size">出勤</p></th>
				<th><p class="ss_size">退勤</p></th>
				<th><p class="ss_size">必要人数</p></th>
				<th><p class="mm_size">従業員名</p></th>
				<th class="bold_line"><p class="s_size"></p></th>
				<th><p class="ss_size">出勤</p></th>
				<th><p class="ss_size">退勤</p></th>
				<th><p class="ss_size">必要人数</p></th>
				<th><p class="mm_size">従業員名</p></th>
				<th class="bold_line"><p class="s_size"></p></th>
			</tr>
			<template v-for = "(item1,index1) in working_address_day_infors">
			   <tr v-for = "(item2,index2) in item1">
				   <td class="bg_gray" v-if = "index2 == 0" v-bind:rowspan="item2.size_of_infor"><a @click="gotoWorkPlace(item2.id)"><p>@{{ item2.pr_id }}</p><p>@{{ item2.address_name  }}</p></a></td>
				   <template v-for = "(item3,index3) in item2.data_working_days_infors">

					   <td>@{{ item3.time_start }}</td>
					   <td>@{{ item3.time_end }}</td>
					   <td v-if ="item3.number != item3.color" width="80px" style="background-color: #e874a4;" >@{{ item3.number}}</td>
					   <td v-else width="80px"  >@{{ item3.number}}</td>
					   <td><p class="right_10 left_10 side_input_block" v-for = "(item4,index4) in item3.data">@{{ item4.name }}<a v-if = "item4.gender == 2">(女)</a><a v-else>(男)</a></p></td>
					   <td class="bold_line"  v-if = "index2 == 0" v-bind:rowspan="item2.size_of_infor">
						   <p class="button"><a class="ss_size s_height btn_gray" @click="gotoAttendanceMember(item2.id)">訪問</a></p>
					   </td>
				   </template>

			   </tr>
			</template>
		</table>
	</div>

	<section class="caeru_date_picker_wrapper" v-show="showDatePicker">
		<calendar class="normal_date_picker" v-bind="datePickerOptions" :editable="false"
				  @change-current-time="datePickerChangeTime"
				  @date-picker-cancel="toggleDatePicker"
				  @day-chose="goToThisDay">
		</calendar>
		<div class="modal-overlay" v-cloak @click="toggleDatePicker"></div>
	</section>
</main>

<script>
    $(function() {
        $('#ms').change(function() {
            console.log($(this).val());
        }).multipleSelect({
            width: '100%'
        });
    });
</script>
@endsection