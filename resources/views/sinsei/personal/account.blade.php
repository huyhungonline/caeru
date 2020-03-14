<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>アカウント設定 | CAERU</title>
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
            <a href="{{Caeru::route('ss_login')}}">ログアウト</a>
        </div>
        <section class="right right_30 request_btn">
            <p class="button"><a class="m_size l_height btn_red" href="{{Caeru::route('manager_list')}}">申請中10件</a></p>
        </section>
    </header>
    <nav id="personal">
        <ul>
            <li>
                <a href="{{Caeru::route('personal_detail')}}"><img src="../../../images/ico_kintai.svg">勤怠情報</a>
            </li>
            <li>
                <a href="{{Caeru::route('personal_account')}}" class="nav_select"><img src="../../../images/ico_account.svg">アカウント情報</a>
            </li>
        </ul>
    </nav>
</section>
<main id="personal_account">
    <section>
        <h1>パスワード変更</h1>
        <section class="account_inner">
            <div class="new_pass bottom_30">
                <span class="mm_size left right_30">現在のパスワード</span><input class="mm_size left" name="" type="text">
            </div>
            <div class="new_pass bottom_20">
                <span class="mm_size left right_30">新しいパスワード</span><input class="mm_size left" name="" type="text">
            </div>
            <div class="new_pass bottom_40">
                <span class="mm_size left right_30">新しいパスワード（再入力）</span><input class="mm_size left" name="" type="text">
            </div>
        </section>
        <section class="btn">
            <p class="button right_30"><a class="m_size l_height btn_greeen" href="#">保存</a></p>
            <p class="button right_30"><a class="m_size l_height btn_gray" href="#">キャンセル</a></p>
        </section>
    </section>
    <section class="top_60">
        <h1>メールアドレス変更</h1>
        <section class="account_inner">
            <div class="new_pass bottom_30">
                <span class="mm_size left right_30">現在のメールアドレス</span><input class="mm_size left" name="" type="text">
            </div>
            <div class="new_pass bottom_20">
                <span class="mm_size left right_30">新しいメールアドレス</span><input class="mm_size left" name="" type="text">
            </div>
            <div class="new_pass bottom_40">
                <span class="mm_size left right_30">新しいメールアドレス（再入力）</span><input class="mm_size left" name="" type="text">
            </div>
        </section>
        <section class="btn">
            <p class="button right_30"><a class="m_size l_height btn_greeen" href="#">保存</a></p>
            <p class="button right_30"><a class="m_size l_height btn_gray" href="#">キャンセル</a></p>
        </section>
    </section>
</main>
</body>
</html>