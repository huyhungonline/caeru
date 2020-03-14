<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>勤怠申請管理 | CAERU</title>
    <link href= "{{ asset('css/approver/all.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
<script>
    window.onload = function(){
        FixedMidashi.create();};
</script>
<section id="header_weapper">
    <header>
        <a href="{{ Caeru::route('manager_list')}}">
            <div id="logo" class="left"></div>
        </a>
        <div class="left">
            <p class="conditions text_bold">管理モード
            </p>
        </div>
        <div id="sign_out">
            <a href="{{Caeru::route('ss_show_login')}}">ログアウト</a>
        </div>
        <section class="right right_30 request_btn">
            <p class="button"><a class="m_size l_height btn_red" href="{{ Caeru::route('personal_detail')}}">個人ページへ</a></p>
        </section>
    </header>
    <nav id ="manager">
        <ul>
            <li>
                <a href="{{ Caeru::route('manager_list')}}" class="nav_select"><img src="../../../images/ico_sinsei.svg">勤怠申請管理</a>
            </li>
        </ul>
    </nav>
</section>
<main id="manager_list">
    <h1>勤怠申請管理</h1>
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
    </section>
    <section class="pager top_20">
        <p>120件中1〜20件表示</p>
        <ul>
            <li class="left"><a href="#">&lt;&lt;</a></li>
            <li class="left"><a href="#">&lt;</a></li>
            <li class="left"><a class="active" href="#">1</a></li>
            <li class="left"><a href="#">2</a></li>
            <li class="left"><a href="#">3</a></li>
            <li class="left"><a href="#">4</a></li>
            <li class="left"><a href="#">5</a></li>
            <li class="left"><a href="#">6</a></li>
            <li class="left"><a href="#">7</a></li>
            <li class="left"><a href="#">8</a></li>
            <li class="left"><a href="#">9</a></li>
            <li class="left"><a href="#">10</a></li>
            <li class="left"><a href="#">&gt;</a></li>
            <li class="left"><a href="#">&gt;&gt;</a></li>
        </ul>
    </section>
    <section class="manager_table">
        <table _fixedhead='rows:1; cols:2;'>
            <tr>
                <th class="s_10">従業員番号</th>
                <th>就労形態</th>
                <th class="s_30">従業員名</th>
                <th class="s_20">申請中</th>
                <th class="s_20">フレックス残業申請</th>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">通常太郎</a></td>
                <td><a href="{{Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000</td>
                <td>フレックス</td>
                <td><a href="{{Caeru::route('manager_detail_flextime')}}">フレックス花子</a></td>
                <td><a href="{{Caeru::route('confirm_flextime')}}">3件</a></td>
                <td><a href="{{Caeru::route('confirm_overtime_flextime')}}">3件</a></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
            <tr>
                <td>1000023</td>
                <td>一般</td>
                <td><a href="{{ Caeru::route('managers_detail')}}">山田太郎</a></td>
                <td><a href="{{ Caeru::route('confirm_multiple')}}">3件</a></td>
                <td></td>
            </tr>
        </table>
    </section>
    <section class="pager">
        <ul>
            <li class="left"><a href="#">&lt;&lt;</a></li>
            <li class="left"><a href="#">&lt;</a></li>
            <li class="left"><a class="active" href="#">1</a></li>
            <li class="left"><a href="#">2</a></li>
            <li class="left"><a href="#">3</a></li>
            <li class="left"><a href="#">4</a></li>
            <li class="left"><a href="#">5</a></li>
            <li class="left"><a href="#">6</a></li>
            <li class="left"><a href="#">7</a></li>
            <li class="left"><a href="#">8</a></li>
            <li class="left"><a href="#">9</a></li>
            <li class="left"><a href="#">10</a></li>
            <li class="left"><a href="#">&gt;</a></li>
            <li class="left"><a href="#">&gt;&gt;</a></li>
        </ul>
    </section>
</main>
</body>
</html>