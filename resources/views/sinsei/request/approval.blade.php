<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>承認 | CAERU</title>
    <link href= "{{ asset('css/approver/all.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
<script>
    window.onload = function(){
        FixedMidashi.create();};
</script>
<section id="header_weapper">
    <header>
        <a href="{{ Caeru::route('personal_detail')}}">
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
            <a href="{{Caeru::route('ss_login')}}">ログアウト</a>
        </div>
        <section class="right right_30 request_btn">
            <p class="button"><a class="m_size l_height btn_red" href="{{ Caeru::route('manager_list')}}">申請中10件</a></p>
        </section>
    </header>
    <nav id="personal">
        <ul>
            <li>
                <a href="{{ Caeru::route('personal_detail')}}" class="nav_select"><img src="../../../images/ico_kintai.svg">勤怠情報</a>
            </li>
            <li>
                <a href="{{ Caeru::route('personal_account')}}"><img src="../../../images/ico_account.svg">アカウント情報</a>
            </li>
        </ul>
    </nav>
</section>
<main id="request_page">
    <section class="select_one2">
        <section class="select_one_inner request_number">
            <section class="right_30 ico_position">
                <a href="#"><img class="ico_ico_arrow" src="../../../images/ico_arrow_left1.svg"></a>
            </section>
            <section>
                <span class="sunday">2016年3月9日(日)</span>
                <span  class="modal-open right_30" data-target="con1"><a href="#"><img class="ico_ico_arrow" src="../../../images/ico_calendar.svg"></a></span></section>
            <section class="ico_position">
                <a href="#"><img class="ico_ico_arrow" src="../../../images/ico_arrow_right1.svg"></a>
            </section>
        </section>
        <section class="right_position">
            <p class="button"><a class="m_size l_height btn_gray" href="{{ Caeru::route('personal_detail')}}">月別表示</a></p>
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
            <p class="ico_box ico_approval bottom_10">承認</p>
            <table>
                <tr class="visit_place">
                    <td class="bg_gray" rowspan="2">
                        打刻時刻
                    </td>
                    <td class="s_40">
                        <p class="l_font">広島本社</p>
                        <p class="place left right_30">出勤</p><p class="place left">08:00</p>
                    </td>
                    <td class="s_40">
                        <p class="l_font">広島本社</p>
                        <p class="place left right_30">退勤</p><p class="place left">19:00</p>
                    </td>
                </tr>
                <tr class="visit_place">
                    <td><span class="right_30">2016/03/01</span><span>08:00</span></td>
                    <td><span class="right_30">2016/03/01</span><span>19:00</span></td>
                </tr>
            </table>
            <table class="top_20">
                <tr>
                    <td class="bg_gray">勤務形態</td>
                    <td colspan="2" class="s_80"></td>
                </tr>
                <tr>
                    <td class="bg_gray">休日形態</td>
                    <td colspan="2"></td>
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
                    <td class="bg_light_gray_line">広島本社</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bg_gray">早出</td>
                    <td class="bg_approval"><span class="right_10">08:00</span><span class="right_10">〜</span>09:00</td>
                    <td><span class="right_10">08:00</span><span class="right_10">〜</span>09:00</td>
                </tr>
                <tr>
                    <td class="bg_gray">所定内</td>
                    <td class="bg_light_gray_line"><span class="right_10">09:00</span><span class="right_10">〜</span>18:00</td>
                    <td><span class="right_10">09:00</span><span class=" right_10">〜</span>18:00</td>
                </tr>
                <tr>
                    <td class="bg_gray">残業</td>
                    <td class="bg_light_gray_line"><span class="left_10 right_10">〜</span></td>
                    <td><span class="left_10 right_10">〜</span></td>
                </tr>
            </table>
            <table class="top_20">
                <tr>
                    <td class="bg_gray s_20">休憩</td>
                    <td class="bg_light_gray_line s_40">60</td>
                    <td>60</td>
                </tr>
                <tr>
                    <td class="bg_gray">(内)深休</td>
                    <td class="bg_light_gray_line"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bg_gray">遅早</td>
                    <td class="bg_light_gray_line"><span class="right_10">遅刻</span><span class="right_10 left_10">早退</span></td>
                    <td><span class="right_10">遅刻</span><span class="right_10 left_10">早退</span></td>
                </tr>
            </table>
            <table class="top_20">
                <tr>
                    <td class="bg_gray s_20">外出</td>
                    <td class="bg_light_gray_line s_40"></td>
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
                        <td class="s_50 contact">残業をしました。</td>
                        <td class="contact"></td>
                    </tr>
                </table>
                <div class="description">
                    <p class="left harf_box s_font">2016年03月03日(月)10:32申請</p>
                    <p class="left harf_box s_font">2016年03月03日(月)10:32承認</p>
                </div>
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
                            <td class="s_20">09:00</td>
                            <td class="s_20">タブレット</td>
                            <td class="s_20">出勤</td>
                        </tr>
                        <tr>
                            <td class="s_20">13:10</td>
                            <td class="s_20">タブレット</td>
                            <td class="s_20">退勤</td>
                        </tr>
                        <tr class="bg_light_blue">
                            <td class="s_20">19:00</td>
                            <td class="s_20">タブレット</td>
                            <td class="s_20">退勤</td>
                        </tr>
                    </table>
                </section>
            </section>
            <section class="btn">
                <p class="button right_30"><a class="m_size l_height btn_greeen" href="request_page1.html">申請手続き</a></p>
            </section>
        </section>
        <section class="right_page right">
            <p class="ico_box ico_before_request bottom_10">申請前</p>
            <table>
                <tr class="visit_place">
                    <td class="bg_gray" rowspan="2">
                        打刻時刻
                    </td>
                    <td class="s_40">
                        <p class="l_font">広島本社</p>
                        <p class="place left right_30">出勤</p><p class="place left">08:00</p>
                    </td>
                    <td class="s_40">
                        <p class="l_font">広島本社</p>
                        <p class="place left right_30">退勤</p><p class="place left">19:00</p>
                    </td>
                </tr>
                <tr class="visit_place">
                    <td><span class="right_30">2016/03/01</span><span>08:00</span></td>
                    <td><span class="right_30">2016/03/01</span><span>19:00</span></td>
                </tr>
            </table>
            <table class="top_20">
                <tr>
                    <td class="bg_gray">勤務形態</td>
                    <td colspan="2" class="s_80"></td>
                </tr>
                <tr>
                    <td class="bg_gray">休日形態</td>
                    <td colspan="2"></td>
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
                    <td class="bg_light_gray_line">広島本社</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bg_gray">早出</td>
                    <td class="bg_light_gray_line"><span class="left_10 right_10">〜</span></td>
                    <td><span class="left_10 right_10">〜</span></td>
                </tr>
                <tr>
                    <td class="bg_gray">所定内</td>
                    <td class="bg_light_gray_line"><span class="right_10">09:00</span><span class="right_10">〜</span>18:00</td>
                    <td><span class="right_10">09:00</span><span class=" right_10">〜</span>18:00</td>
                </tr>
                <tr>
                    <td class="bg_gray">残業</td>
                    <td class="bg_light_gray_line"><span class="left_10 right_10">〜</span></td>
                    <td><span class="left_10 right_10">〜</span></td>
                </tr>
            </table>
            <table class="top_20">
                <tr>
                    <td class="bg_gray s_20">休憩</td>
                    <td class="bg_light_gray_line s_40">60</td>
                    <td>60</td>
                </tr>
                <tr>
                    <td class="bg_gray">(内)深休</td>
                    <td class="bg_light_gray_line"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bg_gray">遅早</td>
                    <td class="bg_light_gray_line"><span class="right_10">遅刻</span><span class="right_10 left_10">早退</span></td>
                    <td><span class="right_10">遅刻</span><span class="right_10 left_10">早退</span></td>
                </tr>
            </table>
            <table class="top_20">
                <tr>
                    <td class="bg_gray s_20">外出</td>
                    <td class="bg_light_gray_line s_40"></td>
                    <td></td>
                </tr>
            </table>
            <section class="result_dakoku top_20">
                <section class="stamp">
                    <table>
                        <tr>
                            <th class="s_20">打刻時刻</th>
                            <th class="s_20">登録ユーザ</th>
                            <th class="s_20">勤怠種類</th>
                        </tr>
                        <tr class="bg_light_blue">
                            <td class="s_20">09:00</td>
                            <td class="s_20">タブレット</td>
                            <td class="s_20">出勤</td>
                        </tr>
                        <tr>
                            <td class="s_20">13:10</td>
                            <td class="s_20">タブレット</td>
                            <td class="s_20">退勤</td>
                        </tr>
                        <tr class="bg_light_blue">
                            <td class="s_20">19:00</td>
                            <td class="s_20">タブレット</td>
                            <td class="s_20">退勤</td>
                        </tr>
                    </table>
                </section>
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
                <a href="#"><img src="../../../images/ico_arrow_left_gray.svg"></a>
            </div>
            <div class="arrow_gray left">
                <a href="#"><img src="../../../images/ico_arrow_right_gray.svg"></a>
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
</body>
</html>