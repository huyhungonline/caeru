@extends('layouts.master_employee')

@section('title', '勤務地一覧')

@section('header_employee')
    @include('layouts.header_employee', [ 'active' => 2 ])
@endsection

@push('scripts')
    <script defer src="{{ asset('/js/multiple-select.js') }}"></script>
    <script defer src="{{ asset('/js/components/employee_searcher.js') }}"></script>
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
    <script defer src="{{ asset('/js/components/request_page.js') }}" type="text/javascript"></script>
@endpush
<div id="request_page">
<section id="header_weapper">
    <div class="overlay"></div>
	<header>
		<a href="personal_detail.html">
		<div id="logo" class="left"></div>
        </a>
        <div class="left">
            <p class="conditions text_bold">個人モード
            </p>
        </div>
        <div class="left name_setting left_30">
            <p class="side_input_block right_30"><span class="right_10">勤務形態</span><span>通常</span></p>
            <p class="side_input_block right_30"><span class="right_10">従業員番号</span><span>0000001</span></p>
            <p class="side_input_block"><span class="right_10">名前</span><span>上司花子</span></p>
        </div>
		<div id="sign_out">
			<a href="index.html">ログアウト</a>
		</div>
        <section class="right right_30 request_btn">
            <p class="button"><a class="m_size l_height btn_red" href="manager_list.html">申請中10件</a></p>
        </section>
	</header>
	<nav id="personal">
		<ul>
			<li>
				<a href="personal_detail.html" class="nav_select"><img src="images/ico_kintai.svg">勤怠情報</a>
			</li>
			<li>
                <a href="personal_account.html"><img src="images/ico_account.svg">アカウント情報</a>
			</li>
		</ul>
	</nav>
</section>
	<main id="request_page">
        <section class="select_one2">
            <section class="select_one_inner request_number">
                <section class="right_30 ico_position">
                    <a href="#"><img class="ico_ico_arrow" src="images/ico_arrow_left1.svg"></a>
                </section>
                <section>
                    <span class="sunday">2016年3月9日(日)</span>
                    <span  class="modal-open right_30" data-target="con1"><a href="#"><img class="ico_ico_arrow" src="images/ico_calendar.svg"></a></span></section>
                <section class="ico_position">
                    <a href="#"><img class="ico_ico_arrow" src="images/ico_arrow_right1.svg"></a>
                </section>
            </section>
            <section class="right_position">
                    <p class="button"><a class="m_size l_height btn_gray" href="personal_detail.html">月別表示</a></p>
            </section>
        </section>
        <section class="pager top_10">
             <section class="select_one_inner l_font v_10">
				<span class="right_10">所定</span><span class="right_10">出勤</span><span class="right_10">09:00</span><span class="right_10">〜</span><span class="right_10">退勤</span><span class="right_10">18:00</span><span class="right_10 ">休憩</span><span class="right_10">60</span><span class="right_10">(内)深休</span><span class="right_10 ">30</span><span class="right_10">=</span><span>8:00</span>
            </section>
        </section>
        <section class="select_one2">
			<section class="left_position">
                <div class="left right_10 description_confirm_list">要確認</div>
				<div class="left right_10 description_mistake description_text">誤った記録</div>
				<div class="left right_10 description_request description_text">申請中</div>
				<div class="left right_10 description_approval description_text">承認</div>
                <div class="left right_10 description_reject description_text">否決</div>
				<div class="left description_correction description_text">修正</div>
			</section>
		</section>
        <section class="request_page_wrapper">
            <section class="left_page left">
                <p class="ico_box ico_now bottom_10">現在</p>
                <table>
                    <tr class="visit_place">
                        <td class="bg_gray" rowspan="2">
                            打刻時刻
                        </td>
                        <td class="s_40">
                            <div class="selectbox bottom_10">
                                <select class="mm_size" @change="change_address_id_start($event.target.value)">
                                    <option value="0" selected></option>
                                    <option  v-for = "(item,index) in work_locations"  :value="index" >@{{ item }}</option>

                                </select>
                            </div>
                            <p class="place left right_30">出勤</p><p class="place left">{{$employee_working_information->schedule_start_work_time}}</p>
                        </td>
                        <td class="s_40">
                            <div class="selectbox bottom_10">
                                <select class="mm_size" @change="change_address_id_end($event.target.value)">
                                    <option value="0" selected></option>
                                    <option  v-for = "(item,index) in work_locations"  :value="index" >@{{ item }}</option>

                                </select>
                            </div>
                            <p class="place left right_30">退勤</p><p class="place left">{{$employee_working_information->schedule_end_work_time}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="right_10 left">
                                <div class="selectbox">
                                    <select class="ss_m_size left">
                                        <option value="2016/03/01" selected>{{$employee_working_day->date}}</option>
                                        <option value="2016/03/02">{{$employee_working_day->date}}</option>
                                    </select>
                                </div>
                            </div>
                            <input class="ss_size left" name="" type="text" v-model="start_work_time">
                        </td>
                        <td>
                            <div class="right_10 left">
                                <div class="selectbox left">
                                    <select class="ss_m_size left">
                                        <option value="2016/03/01" selected>2016/03/01</option>
                                        <option value="2016/03/02">2016/03/02</option>
                                    </select>
                                </div>
                            </div>
                            <input class="ss_size left"  type="text" v-model="end_work_time">
                        </td>
                    </tr>
                </table>
                <table class="top_20">
                    <tr>
                        <td class="bg_gray">勤務形態</td>
                        <td colspan="2" class="s_80">
                            <div class="search_setting">
                                <div class="selectbox search_setting">
                                    <select class="s_size left" @change="select_work_form_input($event.target.value)">
                                        <option value="0" selected></option>
                                        <option  v-for = "(item,index) in work_form"  :value="item.id" >@{{ item.name }}</option>

                                    </select>
                                </div>
                                <section class="transfer_wrap">
                                    <p class="button"><button class="ss_size s_height btn_gray transfer_btn">振替</button></p>
                                    <div class="transfer select_calendar">
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
                                                    <a href="#"><img src="images/ico_arrow_left_gray.svg"></a>
                                                </div>
                                                <div class="arrow_gray left">
                                                    <a href="#"><img src="images/ico_arrow_right_gray.svg"></a>
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
                                                    <td class="pink_holiday"><p><a href="#" class="change_possible candidate">1</a></p></td>
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
                                                    <td class="closing_date"><p><a href="#" class="change_possible candidate">11</a></p></td>
                                                    <td class="closing_date">12</td>
                                                    <td class="closing_date">13</td>
                                                    <td class="blue_holiday"><p><a href="#" class="change_possible candidate">14</a></p></td>
                                                </tr>
                                                <tr>
                                                    <td class="pink_holiday"><p><a href="#" class="change_possible candidate">15</a></p></td>
                                                    <td class="closing_date">16</td>
                                                    <td class="closing_date">17</td>
                                                    <td class="closing_date">18</td>
                                                    <td class="closing_date">19</td>
                                                    <td class="closing_date">20</td>
                                                    <td class="blue_holiday"><p class="change_possible selected">21</p></td>
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
                                            <p class="button right_30"><a class="s_size m_height btn_greeen">振替</a></p>
                                            <p class="button"><a class="transfer_cloase s_size m_height btn_gray">キャンセル</a></p>
                                        </section>
                                    </div>
                                </section>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg_gray">休日形態</td>
                        <td colspan="2">
                            <div class="search_setting">
                                <div class="selectbox left">
                                    <select class="s_size left" @change="select_holiday_mode_input($event.target.value)">
                                        <option value="0" selected></option>
                                        <option  v-for = "(item,index) in holiday_mode"  :value="item.id" >@{{ item.name }}</option>

                                    </select>

                                </div>
                            </div>
<!--                            <input class="ss_size left_30 right_10" name="" type="text"><span class=" right_10">〜</span><input class="ss_size" name="" type="text">-->
                        </td>
                    </tr>
                </table>
                <table class="top_20">
                    <tr>
                        <th></th>
                        <th class="s_40">予定</th>
                        <th class="s_40">実績</th>
                    </tr>
                    <tr>
                        <td class="bg_gray">勤務地</td>
                        <td class="bg_light_gray_line">
                            <div class="selectbox">
                                <select class="mm_size">
                                    <option value="広島本社" selected="selected">広島本社</option>
                                    <option value="岡山支社">岡山支社</option>
                                    <option value="東京本社">東京本社</option>
                                    <option value="長崎支社">長崎支社</option>
                                </select>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="bg_gray">早出</td>
                        <td class="bg_light_gray_line">
                            <input class="ss_size right_10" v-model="early_time_start" type="text"><span class=" right_10">〜</span><input class="ss_size" v-model="early_time_end" type="text">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="bg_gray">所定内</td>
                        <td class="bg_light_gray_line">09:00<span class="left_10 right_10">〜</span>18:00</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="bg_gray">残業</td>
                        <td class="bg_light_gray_line">
                            <input class="ss_size right_10" v-model="over_time_start" type="text"><span class=" right_10">〜</span><input class="ss_size" v-model="over_time_end" type="text">
                        </td>
                        <td></td>
                    </tr>
                </table>
                <table class="top_20">
                    <tr>
                        <td class="bg_gray s_20">休憩</td>
                        <td class="bg_light_gray_line s_40"><input class="ss_size" v-model="break_time" type="text"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="bg_gray">(内)深休</td>
                        <td class="bg_light_gray_line"><input class="ss_size" v-model="deep_holiday" type="text"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="bg_gray">遅早</td>
                        <td class="bg_light_gray_line"><span class="right_10">遅刻</span><input class="ss_size right_10" v-model="late_time" type="text"><span class="right_10 left_10">早退</span><input class="ss_size" v-model="leave_early" type="text"></td>
                        <td></td>
                    </tr>
                </table>
                <table class="top_20">
                    <tr>
                        <td class="bg_gray s_20">外出</td>
                        <td class="bg_light_gray_line s_40"><input class="ss_size" v-model="go_out" type="text"></td>
                        <td></td>
                    </tr>
                </table>
                <section class="top_20">
                    <div class="description">
                        <p class="left harf_box">申請者</p>
                        <p class="left harf_box">承認者</p>
                    </div>
                    <table class="description_table">
                        <tr>
                            <td class="s_50"><textarea name="remarks" v-model="message"></textarea></td>
                            <td class="contact"></td>
                        </tr>
                    </table>
                </section>
                <section class="result_dakoku top_20">
					<section class="stamp">
						<table>
							<tr>
								<th class="s_20">打刻時刻</th>
								<th class="s_20">登録ユーザ</th>
								<th class="s_20">勤怠種類</th>
							</tr>
							<tr class="bg_light_blue">
								<td class="s_20">08:00</td>
								<td class="s_20">タブレット</td>
								<td class="s_20">出勤</td>
							</tr>
							<tr>
								<td class="s_20">13:10</td>
								<td class="s_20">タブレット</td>
								<td class="s_20">退勤</td>
							</tr>
							<tr class="bg_light_blue">
								<td class="s_20">19:20</td>
								<td class="s_20">タブレット</td>
								<td class="s_20">退勤</td>
							</tr>
						</table>
					</section>
				</section>
                <section class="btn">
                    <p class="button right_30"><a class="m_size l_height btn_greeen" @click="save_request_page()">申請</a></p>
                    <p class="button right_30"><a class="m_size l_height btn_gray" href="#">キャンセル</a></p>
                </section>
            </section>
        </section>
        <hr class="top_20">
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
                    <a href="#"><img src="images/ico_arrow_left_gray.svg"></a>
                </div>
                <div class="arrow_gray left">
                    <a href="#"><img src="images/ico_arrow_right_gray.svg"></a>
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
</div>