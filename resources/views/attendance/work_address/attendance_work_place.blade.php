@extends('layouts.master')

@section('title', '管理者詳細')

@section('header')
	@include('layouts.header', [ 'active' => 2 ])
@endsection
@push('scripts')
	<script defer src="{{ asset('/js/components/attendance_place.js') }}" type="text/javascript"></script>
@endpush
<body>

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
			<p class="breadcrumb"><span>勤怠管理</span>&emsp;&#62;&emsp;<a href="attendance_work_info.html">訪問先別勤務情報</a><span>&emsp;&#62;&emsp;訪問先別詳細</span></p>
			<div class="title_wrapper">
				<h1>訪問先別詳細</h1>
				<div class="worklocation">
					<div class="worklocation_inner">
						<span class="right_10">広島本社</span>
						<p class="button"><a class="modal-open ss_size s_height btn_gray" data-target="con1">変更</a></p>
					</div>
				</div>
			</div>
		</section>
        <section class="select_one">
			<section class="select_one_inner">
                <section class="right_10">
                    <span class="right_10">00001</span>
                    <span class="right_10">広島中央ビルエントランス</span>
                    <p class="button"><a class="modal-open ss_size s_height btn_gray" data-target="con1">変更</a></p>
                </section>
			</section>
		</section>
		<section class="select_one bg_light_green">
			<section class="select_one_inner">
				<section class="right_10"><a @click = "preMonth()" ><img class="ico_ico_arrow" src="{{ asset('/images/ico_arrow_left1.svg') }}"></a></section>
				<section class="right_30 ll_font">前月</section>
				<div class="selectbox side_input_block right_10">
					<select class="s_size" v-model = "yearSelected" @change="emit()">

							@for($i=5; $i>=1; $i--)
								<option value="{{ $year-$i }}" {{ $i==$year?" selected='selected'":""}} >{{ $year-$i }}年</option>
							@endfor
							<option value="{{ $year }}" {{ $i==$year?" selected='selected'":""}} >{{ $year }}年</option>
							@for($i=1; $i<=5; $i++)
								<option value="{{ $year+$i }}" {{ $i==$year?" selected='selected'":""}} >{{ $year+$i }}年</option>
							@endfor

					</select>
				</div>
				<div class="selectbox side_input_block right_10">
					<select class="s_size" v-model = "monthSelected" @change="emit($event.target.value)">
						@for($i= 1; $i<=12; $i++)
							<option value="{{ $i }}"{{ $i==$month?" selected='selected'":""}}>{{ $i }}月</option>
						@endfor

                    </select>
                </div>
				<section class="right_10 ll_font left_30">次月</section>
				<section><a @click = "nextMonth()"><img class="ico_ico_arrow" src="{{ asset('/images/ico_arrow_right1.svg') }}"></a></section>
			</section>
		</section>
		<section class="select_one2">
			<section class="left_position">
				<div class="left right_10 description_correction description_text">修正</div>
				<div class="left right_10 description_no_assignment description_text">未配属</div>

			</section>
		</section>
		<section class="approval_table">
			<table>
			<tr>
				<th class="s_14">日付</th>
				<th class="s_8">出勤</th>
				<th class="s_8">退勤</th>
				<th class="s_6">必要人数</th>
				<th>従業員名</th>
				<th class="s_10"></th>
			</tr>
			</table>
			<template v-for="(item,index) in working_day_infors">

			<attendance_place :data="item" :day_infor = "day_infor[index]"></attendance_place>

			</template>

		</section>
			<section class="btn">
			<p class="button right_30"><a class="m_size l_height btn_greeen l_font" @click="click()" >保存</a></p>
			<p class="button right_30"><a class="m_size l_height btn_gray l_font" @click="cancel()">キャンセル</a></p>
			<p class="button"><a class="m_size l_height btn_gray l_font" href="attendance_work_info.html">戻る</a></p>
		    </section>
		</main>
	<div class="modal-content select_location" id="con1">
        <section class="select_one">
            <section class="select_one_innner">
                <h2>訪問先変更</h2>
                <section class="right_position">
				    <p class="button left_10"><a class="mm_size s_height btn_gray modal-open" data-target="con1" href="#">無効な訪問先も表示</a></p>
                </section>
            </section>
        </section>
		<section class="default_table">
			<table>
				<tr>
					<th class="s_5">訪問先ID</th>
					<th class="s_12">訪問先</th>
					<th class="s_5"></th>
				</tr>
				<tr>
					<td>000001</td>
					<td>広島中央ビルエントランス</td>
					<td>
						<p class="button"><a class="ss_size s_height btn_gray" href="#">選択</a></p>
					</td>
				</tr>
				<tr>
					<td>000018</td>
					<td>海田駅前ビル4F</td>
					<td>
						<p class="button"><a class="ss_size s_height btn_gray" href="#">選択</a></p>
					</td>
				</tr>
				<tr>
					<td>306</td>
					<td>広島駅タワーマンション1F</td>
					<td>
						<p class="button"><a class="ss_size s_height btn_gray" href="#">選択</a></p>
					</td>
				</tr>
				<tr>
					<td>1193</td>
					<td>広島そごう8F</td>
					<td>
						<p class="button"><a class="ss_size s_height btn_gray" href="#">選択</a></p>
					</td>
				</tr>
				<tr>
					<td>00001</td>
					<td>海田駅前ビル4F</td>
					<td>
						<p class="button"><a class="ss_size s_height btn_gray" href="#">選択</a></p>
					</td>
				</tr>
			</table>
		</section>
		<section class="btn">
			<p class="button"><a class="modal-close m_size l_height btn_gray l_font">キャンセル</a></p>
		</section>
	</div>
</body>
</html>