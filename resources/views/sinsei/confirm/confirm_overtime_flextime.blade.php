<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>申請中 | CAERU</title>
    <link href= "{{ asset('css/approver/all.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
<script>
    window.onload = function(){
        FixedMidashi.create();};
</script>
<section id="header_weapper">
    <header>
        <a href="{{Caeru::route('manager_list')}}">
            <div id="logo" class="left"></div>
        </a>
        <div class="left">
            <p class="conditions text_bold">管理モード
            </p>
        </div>
        <div id="sign_out">
            <a href="{{Caeru::route('ss_login')}}">ログアウト</a>
        </div>
        <section class="right right_30 request_btn">
            <p class="button"><a class="m_size l_height btn_red" href="{{Caeru::route('personal_detail_flextime')}}">個人ページへ</a></p>
        </section>
    </header>
    <nav id ="manager">
        <ul>
            <li>
                <a href="{{Caeru::route('manager_list')}}" class="nav_select"><img src="../../../images/ico_sinsei.svg">勤怠申請管理</a>
            </li>
        </ul>
    </nav>
</section>
<main id="request_page">
    <section class="select_one2">
        <section class="select_one_inner request_number">
            <section>
                <span>2017年1月度</span>
            </section>
        </section>
        <section class="right_position">
            <p class="button"><a class="m_size l_height btn_gray" href="{{Caeru::route('personal_detail_flextime')}}">月別表示</a></p>
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
    <section class="request_overtime_wrapper">
        <section class="left_page left">
            <p class="ico_box ico_request bottom_10">申請中</p>
            <table>
                <tr>
                    <th></th>
                    <th class="s_22">所定<p>168:00</p></th>
                    <th class="s_22">有給<p>8:00</p></th>
                    <th class="s_22 bg_apply">時間外<p>4:00</p></th>
                    <th class="s_22">実労働時間<p>4:00</p></th>
                </tr>
                <tr>
                    <td>予測</td>
                    <td class="light_gray">160:00</td>
                    <td class="light_gray">8:00</td>
                    <td class="light_gray">4:00</td>
                    <td class="red">164:00</td>
                </tr>
                <tr>
                    <td>実績</td>
                    <td>122:00</td>
                    <td>8:00</td>
                    <td>0:00</td>
                    <td>122:00</td>
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
                        <td class="s_50"><textarea name="remarks"></textarea></td>
                    </tr>
                </table>
                <div class="description">
                    <p class="left harf_box">2016年3月5日(木)09:20</p>
                    <p class="left harf_box"></p>
                </div>
            </section>
            <section class="btn">
                <p class="button right_30"><a class="m_size l_height btn_light_blue" href="#">承認</a></p>
                <p class="button right_30"><a class="m_size l_height btn_black" href="#">否決</a></p>
            </section>
        </section>
        <section class="right_page right">
            <p class="ico_box ico_before_request bottom_10">申請前</p>
            <table>
                <tr>
                    <th></th>
                    <th class="s_22">所定<p>168:00</p></th>
                    <th class="s_22">有給<p>8:00</p></th>
                    <th class="s_22">時間外<p>0:00</p></th>
                    <th class="s_22">実労働時間<p>160:00</p></th>
                </tr>
                <tr>
                    <td>予測</td>
                    <td class="light_gray">160:00</td>
                    <td class="light_gray">8:00</td>
                    <td class="light_gray">4:00</td>
                    <td class="red">164:00</td>
                </tr>
                <tr>
                    <td>実績</td>
                    <td>122:00</td>
                    <td>8:00</td>
                    <td>0:00</td>
                    <td>122:00</td>
                </tr>
            </table>
        </section>
    </section>
    <hr class="top_20">
</main>
</body>
</html>