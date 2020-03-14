@extends('layouts.master')

@section('title', '管理者詳細')

@section('header')
	@include('layouts.header', [ 'active' => 2 ])
@endsection

@push('scripts')
	<script defer src="{{ asset('/js/components/attendance_shift.js') }}" type="text/javascript"></script>
@endpush
<body >

<section id="header_weapper">
	<header>
		<section class="alert_wrapper">
			<section class="alert_innner">
				<section class="alert_box save_yellow"><span class="alert_ico right_10"><img src="../../public/images/ico_alert_dakoku.svg"></span>打刻エラーを修正してください</section>
			</section>
		</section>	
		<a href="index.html">
			<div id="logo"></div>
		</a>
			<div id="sign_out">
				<a href="login.html">ログアウト</a>
			</div>
	</header>
	<nav>
		<ul class="gnav" id="dropmenu">
			<li class="home">
				<a href="index.html"></a>
			</li>
			<li class="green"><a class="green" href="#">基本情報</a>
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

	<main id="attendance_detail">
		<section class="title">
			<p class="breadcrumb"><span>勤怠管理</span>&emsp;&#62;&emsp;<a href="attendance_work_info.html">訪問先別勤務情報</a><span>&emsp;&#62;&emsp;訪問勤務詳細</span></p>
			<div class="title_wrapper">
				<h1>訪問勤務詳細</h1>
				<div class="worklocation">
					<div class="worklocation_inner">
						<span class="right_30">広島本社</span><span class="right_10">00002</span><span>広島中央ビルエントランス</span>
					</div>
				</div>
			</div>
		</section>
		<div id="ex6">
		<section class="select_one bg_light_green bottom_10">
			<section class="select_one_inner">
				<section class="right_30 ico_position">
					<a href="{{ Caeru::route('attendance_working_member', [$pre_day_id]) }}"><img class="ico_ico_arrow" src="{{ asset('/images/ico_arrow_left1.svg') }}"></a>
				</section>
				<section class="right_10 ll_font">{{ $year }}年{{ $month }}月{{ $day }}日({{ $weekDay }})
					<section class="right_10 ico_position"><a class="modal-open" data-target="con1"><img class="ico_ico_arrow" src="{{ asset('/images/ico_calendar.svg') }}"></a></section>
				</section>
				<section class=" ico_position"> 
					<a href="{{ Caeru::route('attendance_working_member', [$next_day_id]) }}"><img class="ico_ico_arrow" src="{{ asset('/images/ico_arrow_right1.svg') }}"></a>
				</section>
			</section>
		</section>
         <section class="select_one2">
            <section class="right_position">
				<p class="button"><a id="abcd" class="m_size s_height btn_blue" href="#">勤務追加</a></p>
			</section>
		</section>

			<template v-for="(item,index) in lists">
				<attendance :data="item" :employee_id= "employee_id" :employee_names= "employee_names" :index="index" :work_address_id="work_address_id" :working_day_id="working_day_id" :work_infor_id="work_infor_id"></attendance>
			</template>
		</div>
	</main>

    <div class="modal-content select_calendar" id="con1">
		<section class="year_month_select">
			<div class="selectbox left right_10 left_10">
				<select class="s_size">
					<option value="2016年">2016年</option>
					<option value="2017年">2017年</option>
					<option value="2018年">2018年</option>
				</select>
			</div>
			<div class="selectbox left">
				<select class="s_size">
					<option value="1月">1月</option>
					<option value="2月">2月</option>
					<option value="3月">3月</option>
				</select>
			</div>
			<section class="right right_10">
				<div class="arrow_gray left right_10">
					<a href="#"><img src="../../public/images/ico_arrow_left_gray.svg"></a>
				</div>
				<div class="arrow_gray left">
					<a href="#"><img src="../../public/images/ico_arrow_right_gray.svg"></a>
				</div>
			</section>
		</section>
		<section>
			<table>
				<tr>
					<td class="sunday text_bold">日</td>
					<td class="text_bold">月</td>
					<td class="text_bold">火</td>
					<td class="text_bold">水</td>
					<td class="text_bold">木</td>
					<td class="text_bold">金</td>
					<td class="saturday text_bold">土</td>
				</tr>
				<tr>
					<td class="pink_holiday">1</td>
					<td class="pink_holiday">2</td>
					<td class="pink_holiday">3</td>
					<td class="closing_date">4</td>
					<td class="closing_date">5</td>
					<td class="closing_date">6</td>
					<td class="blue_holiday">7</td>
				</tr>
				<tr>
					<td class="pink_holiday">8</td>
					<td class="closing_date">9</td>
					<td class="closing_date">10</td>
					<td class="closing_date">11</td>
					<td class="closing_date">12</td>
					<td class="closing_date">13</td>
					<td class="blue_holiday">14</td>
				</tr>
				<tr>
					<td class="pink_holiday">15</td>
					<td class="closing_date">16</td>
					<td class="closing_date">17</td>
					<td class="closing_date">18</td>
					<td class="closing_date">19</td>
					<td class="closing_date">20</td>
					<td class="blue_holiday">21</td>
				</tr>
				<tr>
					<td class="pink_holiday">22</td>
					<td class="closing_date2">23</td>
					<td class="closing_date2">24</td>
					<td class="closing_date2">25</td>
					<td class="closing_date2">26</td>
					<td class="closing_date2">27</td>
					<td class="blue_holiday">28</td>
				</tr>
				<tr>
					<td class="pink_holiday">29</td>
					<td class="closing_date2">30</td>
					<td class="closing_date2">31</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</section>
		<section class="btn">
			<p class="button"><a class="modal-close m_size m_height btn_gray">キャンセル</a></p>
		</section>
	</div>
    <script src="{{ asset('/js/components/attendance_shift.js') }}" type="text/javascript"></script>
	<script>
		export default {

		}
	   $(function() {
	       $('#ms').change(function() {
	           console.log($(this).val());
	       }).multipleSelect({
	           width: '100%'
	       });
	   });
	</script>

</body>


</html>