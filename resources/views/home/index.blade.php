@extends('layouts.master')

@section('title', 'ホーム')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection
@section('content')
    <main id="home_page">
            <section class="title">
            <p class="breadcrumb"><a href="index.html">ホーム</a><span>&emsp;&#62;&emsp;お知らせ一覧</span></p>
            <div class="title_wrapper">
                <h1>チェックリスト</h1>
            </div>
            </section>
            <section class="list_number">
                <dl>
                    <dt class="left">打刻エラー</dt>
                    <dd class="left"><a href="{{Caeru::route('checklists_list')}}" class="red">{{ $dakoku }}件</a></dd>
                </dl>
                <dl>
                    <dt class="left">要チェックリスト</dt>
                    <dd class="left red"><a href="{{Caeru::route('checklists_list')}}" class="red">{{ $hyou }}件</a></dd>
                </dl>
            </section>
            <section class="notice_wrapper">
                <section class="notice_title">
                    <h1>お知らせ</h1>
                    <p class="button"><a class="m_size s_height btn_gray" href="#">お知らせ一覧</a></p>
                </section>
                <section class="notice_box">
                    <dl>
                        <dt class="left"><span class="notice update">アップデート</span><span class="left">2017年8月17日(木)</span></dt>
                        <dd class="left"><a href="#"><span>××の機能を追加しました</span></a></dd>
                    </dl>
                    <dl>
                        <dt class="left"><span class="notice disability">障害情報</span>2017年7月16日(日)</dt>
                        <dd class="left"><a href="#"><span>××の機能を追加しました</span></a></dd>
                    </dl>
                    <dl>
                        <dt class="left"><span class="notice info">お知らせ</span>2017年7月16日(日)</dt>
                        <dd class="left"><a href="#"><span>××の機能を追加しました</span></a></dd>
                    </dl>
                    <dl>
                        <dt class="left"><span class="notice update">アップデート</span>2017年8月17日(木)</dt>
                        <dd class="left"><a href="#"><span>××の機能を追加しました</span></a></dd>
                    </dl>
                    <dl>
                        <dt class="left"><span class="notice disability">障害情報</span>2017年7月16日(日)</dt>
                        <dd class="left"><a href="#"><span>××の機能を追加しました</span></a></dd>
                    </dl>
                    <dl>
                        <dt class="left"><span class="notice info">お知らせ</span>2017年7月16日(日)</dt>
                        <dd class="left"><a href="#"><span>××の機能を追加しました</span></a></dd>
                    </dl>
                    <dl>
                        <dt class="left"><span class="notice update">アップデート</span>2017年8月17日(木)</dt>
                        <dd class="left"><a href="#"><span>××の機能を追加しました</span></a></dd>
                    </dl>
                    <dl>
                        <dt class="left"><span class="notice disability">障害情報</span>2017年7月16日(日)</dt>
                        <dd class="left"><a href="#"><span>××の機能を追加しました</span></a></dd>
                    </dl>
                    <dl>
                        <dt class="left"><span class="notice info">お知らせ</span>2017年7月16日(日)</dt>
                        <dd class="left"><a href="#"><span>××の機能を追加しました</span></a></dd>
                    </dl>
                    <dl>
                        <dt class="left"><span class="notice update">アップデート</span>2017年8月17日(木)</dt>
                        <dd class="left"><a href="#"><span>××の機能を追加しました</span></a></dd>
                    </dl>
                </section>
            </section>
    </main>