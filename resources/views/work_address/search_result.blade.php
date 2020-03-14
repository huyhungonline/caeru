<section class="pager">
    {{ $list_work_address->links(null, ['sum_line' => true, 'force_url' => 'work_address_list']) }}
    <section class="right_position">
        @can('change_work_address_info')
            <p class="button"><a class="m_size s_height btn_blue" href="{{ Caeru::route('create_work_address') }}">新規登録</a></p>
        @endcan
        <p class="button left_10"><a class="m_size s_height btn_gray modal-open" data-target="con1" href="#">ダウンロード</a></p>
    </section>
</section>
<section class="default_table">
    <table class='table_with_fixed_header'>
        <tr class="fixed_header">
            <th class="s_8">勤務地ID</th>
            <th class="s_10">勤務地名</th>
            <th class="s_8">訪問先ID</th>
            <th>訪問先名</th>
            <th class="s_8">状態</th>
            <th class="s_12">電話番号</th>
            <th class="s_6"></th>
        </tr>
        @foreach ($list_work_address->groupBy('work_location_id') as $work_addresses)
            @foreach ($work_addresses as $index => $work_address)
                <tr>
                    @if ($index == 0)
                        <td rowspan="{{ count($work_addresses) }}" class={{ ($work_addresses->where('enable', 1)->count() == 0) ? "light_gray" : ""}} >{{ $work_address->workLocation->presentation_id }}</td>
                        <td rowspan="{{ count($work_addresses) }}" class={{ ($work_addresses->where('enable', 1)->count() == 0) ? "light_gray" : ""}}>{{ $work_address->workLocation->name }}</td>
                    @endif
                    <td class={{ !$work_address->enable ? "light_gray" : ""}}>{{ $work_address->presentation_id }}</td>
                    <td class={{ !$work_address->enable ? "light_gray" : ""}}>{{ $work_address->name }}</td>
                    <td class={{ !$work_address->enable ? "light_gray" : ""}}>{{ $work_address->enable ? "有効" : "無効" }}</td>
                    <td class={{ !$work_address->enable ? "light_gray" : ""}}>{{ $work_address->telephone }}</td>
                    <td class={{ !$work_address->enable ? "light_gray" : ""}}>
                        <p class="button"><a class="ss_size s_height btn_gray" href="{{ Caeru::route('edit_work_address', [$work_address->id, $list_work_address->currentPage()]) }}">詳細</a></p>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </table>
</section>
<section class="pager">
    {{ $list_work_address->links(null, ['sum_line' => false, 'force_url' => 'work_address_list']) }}
</section>