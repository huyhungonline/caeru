@extends('layouts.master')

@section('title', '勤務地選択')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    <main id="basic">
        <section class="title">
            <p class="breadcrumb"><span>基本情報</span></p>
            <div class="title_wrapper">
                <h1>勤務地一覧</h1>
            </div>
            <p class="red">勤務地を選択してください</p>
        </section>
        <section class="default_table">
            <table class="table_with_fixed_header">
                <tr class="fixed_header">
                    <th class="s_8">勤務地ID</th>
                    <th class="s_20">勤務地</th>
                    <th class="s_8">都道府県</th>
                    <th class="s_5">従業員数</th>
                    <th class="s_5"></th>
                </tr>
                @if (isset($list['first']))
                <tr>
                    <td>{{ $list['first']['presentation_id'] }}</td>
                    <td>{{ $list['first']['name'] }}</td>
                    <td>{{ $list['first']['todofuken'] }}</td>
                    <td>{{ $list['first']['employee_count'] }}</td>
                    <td>
                        <p class="button"><a class="ss_size s_height btn_gray" single-click href="{{ Caeru::route('choose', ['chosen' => 'all']) }}">選択</a></p>
                    </td>
                </tr>
                @endif
                @foreach ($list['work_locations'] as $place)
                    <tr class="{{ $place->enable ? '' : 'light_gray' }}">
                        <td>{{ $place->presentation_id }}</td>
                        <td>{{ $place->name }}</td>
                        <td>{{ $place->todofuken() }}</td>
                        <td>{{ $place->employees->count() }}</td>
                        <td>
                            <p class="button"><a class="ss_size s_height btn_gray" single-click href="{{ Caeru::route('choose', ['chosen' => $place->id]) }}">選択</a></p>
                        </td>
                    </tr>
                @endforeach
            </table>
        </section>
    </main>
@endsection