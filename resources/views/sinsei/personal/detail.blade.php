<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>個人別詳細 | CAERU</title>
    <link href= "{{ asset('css/approver/all.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
<script>
    window.onload = function(){
        FixedMidashi.create();};
</script>
<section id="header_weapper">
    <header>
        <a href="{{Caeru::route('personal_detail')}}">
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
            <a href="{{Caeru::route('ss_logout')}}">ログアウト</a>
        </div>
        <section class="right right_30 request_btn">
            <p class="button"><a class="m_size l_height btn_red" href="{{ Caeru::route('manager_list')}}">申請中10件</a></p>
        </section>
    </header>
    <nav id="personal">
        <ul>
            <li>
                <a href="{{Caeru::route('personal_detail')}}" class="nav_select"><img src="../../../images/ico_kintai.svg">勤怠情報</a>
            </li>
            <li>
                <a href="{{Caeru::route('personal_detail')}}"><img src="../../../images/ico_account.svg">アカウント情報</a>
            </li>
        </ul>
    </nav>
</section>
<main id="attendance_detail">
    <section class="select_one2">
        <section class="select_one_inner">
            <section class="right_30 ico_position">
                <a href="#"><img class="ico_ico_arrow" src="../../../images/ico_arrow_left1.svg"></a>
            </section>
            <div class="selectbox side_input_block right_10">
                <select class="m_size">
                    <option value="2016年">2016年</option>
                    <option selected="selected" value="2017年">2017年</option>
                    <option value="2018年">2018年</option>
                </select>
            </div>
            <div class="selectbox side_input_block right_30">
                <select class="m_size">
                    <option value="1月度">1月度</option>
                    <option value="2月度">2月度</option>
                    <option value="3月度">3月度</option>
                    <option value="4月度">4月度</option>
                    <option value="5月度">5月度</option>
                    <option value="6月度">6月度</option>
                    <option value="7月度">7月度</option>
                    <option value="8月度">8月度</option>
                    <option value="9月度">9月度</option>
                    <option value="10月度">10月度</option>
                    <option value="11月度">11月度</option>
                    <option value="12月度">12月度</option>
                </select>
            </div>
            <section class="ico_position">
                <a href="#"><img class="ico_ico_arrow" src="../../../images/ico_arrow_right1.svg"></a>
            </section>
        </section>
        <section class="right_position">
            <p class="button"><a class="m_size l_height btn_gray" href="{{Caeru::route('request_index')}}">日別表示</a></p>
        </section>
    </section>
    <section class="person_result_table">
        <table class="left s_60">
            <tr>
                <th class="s_8" rowspan="2"></th>
                <th class="s_10 bg_dark_gray" rowspan="2">勤務日数(21日)</th>
                <th class="s_10 bg_dark_gray" rowspan="2">総勤務時間</th>
                <th class="s_28" colspan="4">所定(168:00)</th>
                <th class="s_10" rowspan="2">時間外</th>
            </tr>
            <tr>
                <th class="s_10">所定内</th>
                <th class="s_8">有給</th>
                <th class="s_8">無給</th>
                <th class="s_8">不就労</th>
            </tr>
            <tr>
                <td>実績<span class="light_gray">(予測)</span></td>
                <td>16<span class="light_gray">(20)</span></td>
                <td>139:00<span class="light_gray">(168:00)</span></td>
                <td>128:00<span class="light_gray">(160:00)</span></td>
                <td>18:00<span class="light_gray">(18:00)</span></td>
                <td>18:00<span class="light_gray">(18:00)</span></td>
                <td>10:00<span class="light_gray">(10:00)</span></td>
                <td>100:00<span class="light_gray">(100:00)</span></td>
            </tr>
        </table>
        <table class="left s_28 secound both_1">
            <tr>
                <th class="s_28">深夜勤務</th>
                <th class="s_28">欠勤日数</th>
                <th><p>年次有給取得</p><p>日:時間:分</p></th>
            </tr>
            <tr>
                <td>0:30<span class="light_gray">(0:30)</span></td>
                <td>1日<span class="light_gray">(2日)</span></td>
                <td>9日04:30<span class="light_gray">(9日04:30)</span></td>
            </tr>
        </table>
        <table class="right s_10 secound">
            <tr>
                <th class="s_20"><p>年次有給残日数</p><p>日:時間:分</p></th>
            </tr>
            <tr>
                <td><span>9日04:30</span><span class="light_gray">(9日04:30)</span></td>
            </tr>
        </table>
    </section>
    <section class="select_one2">
        <sectoin class="left_position">
            <section class="side_input_block right_10"><span class="right_10">チェックした勤怠の</span><p class="button"><a class="s_size m_height btn_greeen" href="#">詳細</a></p></section>
        </sectoin>
        <section class="right_position">
            <div class="left right_10 description_day_off1">法定休日</div>
            <div class="left right_10 description_day_off2">一般休日</div>
            <div class="left right_10 description_confirm_list">要確認</div>
            <div class="left right_10 description_mistake description_text">誤った記録</div>
            <div class="left right_10 description_request description_text">申請中</div>
            <div class="left right_10 description_approval description_text">承認</div>
            <div class="left right_10 description_reject description_text">否決</div>
            <div class="left description_correction description_text">修正</div>
        </section>
    </section>
    <section class="search_table">
        <table _fixedhead='rows:3; cols:1;'>
            <tr class="short_height">
                <th class="s_4" rowspan="3"></th>
                <th class="s_12" colspan="1" rowspan="3">日付</th>
                <th class="s_14" colspan="1" rowspan="3">勤務地</th>
                <th class="s_4" rowspan="3"><p>勤務</p><p>形態</p></th>
                <th class="s_4" rowspan="3"><p>休日</p><p>形態</p></th>
                <th colspan="2">出勤</th>
                <th colspan="2">退勤</th>
                <th class="s_4" rowspan="3">休憩</th>
                <th class="s_4" rowspan="3">(内)深休</th>
                <th class="s_4" rowspan="3">外出</th>
                <th rowspan="3">遅・早</th>
                <th class="s_14" colspan="3" rowspan="3">所定内+時間外＝総労働時間</th>
            </tr>
            <tr class="short_height">
                <th class="s_8">予定</th>
                <th class="s_8" rowspan="2">計算時刻</th>
                <th class="s_8">予定</th>
                <th class="s_8" rowspan="2">計算時刻</th>
            </tr>
            <tr class="short_height">
                <th>打刻時刻</th>
                <th>打刻時刻</th>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="blue_holiday saturday"><a href="{{Caeru::route('request_index')}}">2016/03/01(土)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2">09:00</td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2">18:00</td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td>08:00</td>
                <td>18:00</td>
                <td>60</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none">8:00</td>
                <td class="line_right_none"></td>
                <td>8:00</td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="pink_holiday sunday"><a href="{{Caeru::route('request_applying')}}">2016/03/02(日)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2">09:00</td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2">18:00</td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_apply line_right_none">1:00</td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td>08:00</td>
                <td>18:10</td>
                <td>60</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_applying')}}">2016/03/03(月)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">08:00</td>
                <td rowspan="2">08:00</td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2">18:00</td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_approval line_right_none">1:00</td>
                <td class="bg_light_gray">9:00</td>
            </tr>
            <tr>
                <td>08:00</td>
                <td>18:10</td>
                <td>60</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none">8:00</td>
                <td class="line_right_none">1:00</td>
                <td>9:00</td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_rejection')}}">2016/03/04(火)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">08:00</td>
                <td rowspan="2">08:00</td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2">18:00</td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_reject line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td>08:00</td>
                <td>18:10</td>
                <td>60</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none">8:00</td>
                <td class="line_right_none"></td>
                <td>9:00</td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/05(水)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2">09:00</td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2">18:00</td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td>08:50</td>
                <td>18:10</td>
                <td>60</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none">8:00</td>
                <td class="line_right_none"></td>
                <td>8:00</td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/06(木)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2">09:00</td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2">18:00</td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td>08:50</td>
                <td>18:10</td>
                <td>60</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none">8:00</td>
                <td class="line_right_none"></td>
                <td>8:00</td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/07(金)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="saturday"><a href="{{Caeru::route('request_index')}}">2016/03/08(土)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="pink_holiday sunday"><a href="{{Caeru::route('request_index')}}">2016/03/09(日)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="blue_holiday"><a href="{{Caeru::route('request_index')}}">2016/03/10(月)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/11(火)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/12(水)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/13(木)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/14(金)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="blue_holiday saturday"><a href="{{Caeru::route('request_index')}}">2016/03/15(土)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="pink_holiday sunday"><a href="{{Caeru::route('request_index')}}">2016/03/16(日)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/17(月)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/18(火)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/19(水)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/20(木)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/21(金)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="blue_holiday saturday"><a href="{{Caeru::route('request_index')}}">2016/03/21(土)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="pink_holiday sunday"><a href="{{Caeru::route('request_index')}}">2016/03/22(日)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/23(月)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/24(火)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/25(水)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/26(木)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/27(金)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="blue_holiday saturday"><a href="{{Caeru::route('request_index')}}">2016/03/28(土)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><span class="pink_holiday sunday"><a href="{{Caeru::route('request_index')}}">2016/03/29(日)</a></span></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/30(月)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
            <tr>
                <td rowspan="2">
                    <div class="check_onle_wrap">
                        <label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
                    </div>
                </td>
                <td rowspan="2"><a href="{{Caeru::route('request_index')}}">2016/03/31(火)</a></td>
                <td rowspan="2">広島本社</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">09:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">18:00</td>
                <td rowspan="2"></td>
                <td class="bg_light_gray">60</td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray"></td>
                <td class="bg_light_gray line_right_none">8:00</td>
                <td class="bg_light_gray line_right_none"></td>
                <td class="bg_light_gray">8:00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="line_right_none"></td>
                <td class="line_right_none"></td>
                <td></td>
            </tr>
        </table>
    </section>
</main>
</body>
</html>