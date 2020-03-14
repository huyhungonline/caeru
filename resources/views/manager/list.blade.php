@extends('layouts.master')

@section('title', '管理者一覧')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    <main id="basic">
        <section class="title">
            <p class="breadcrumb"><span>基本情報</span><span>&emsp;&#62;&emsp;管理者一覧</span></p>
            <div class="title_wrapper">
                <h1>管理者一覧</h1>
            </div>
        </section>
        <section class="pager">
            {{ $managers->links(null, ['sum_line' => true]) }}
            <p class="button right_position"><a class="m_size m_height btn_blue" href="{{ Caeru::route('create_manager') }}">新規登録</a></p>
        </section>
        <section class="default_table">
            <table class='table_with_fixed_header'>
                <tr class="fixed_header">
                    <th class="s_10">No</th>
                    <th class="s_10">管理者ID</th>
                    <th class="s_14">管理者名</th>
                    <th class="s_8">管理勤務地</th>
                    <th class="s_8">勤務地数</th>
                    <th>メールアドレス</th>
                    <th class="s_8">状態</th>
                    <th class="s_6"></th>
                </tr>
                @foreach ($managers as $manager)
                    <tr class={{ !$manager->enable ? "light_gray" : ""}}>
                        <td>{{ $manager->id }}</td>
                        <td>{{ $manager->presentation_id }}</td>
                        <td>{{ $manager->last_name . $manager->first_name }}</td>
                        @if ($manager->company_wide_authority)
                            <td>会社</td>
                            <td>{{ $manager->company->workLocations->count() }}</td>
                        @elseif ($manager->workLocations->count() > 0)
                            <td>{{ $manager->workLocations->count() > 1 ? "複数" : $manager->workLocations()->first()->name }}</td>
                            <td>{{ $manager->workLocations->count() }}</td>
                        @else
                            <td></td>
                            <td>0</td>
                        @endif
                        <td>{{ $manager->email }}</td>
                        <td>{{ $manager->enable ? "有効" : "無効" }}</td>
                        <td>
                            <p class="button"><a class="ss_size s_height btn_gray" href="{{ Caeru::route('edit_manager', [$manager->id, $managers->currentPage()]) }}">詳細</a></p>
                        </td>
                    </tr>
                @endforeach
            </table>
        <section class="pager">
            {{ $managers->links(null, ['sum_line' => false]) }}
        </section>
    </main>
@endsection