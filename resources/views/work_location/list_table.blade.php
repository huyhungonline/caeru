@push('scripts')
    <script defer src="{{ asset('/js/components/change_view_order.js') }}"></script>
@endpush

<input type='hidden' name='current_page' value='{{ $work_locations->currentPage() }}'>
<input type='hidden' name='object_type' value='1'>
<table class="table_with_fixed_header">
    <tr class="fixed_header">
        @if ($can_change_view_order == true)
            <th class="s_6">並び順</th>
        @endif
        <th class="s_8">勤務地ID</th>
        <th class="s_12">勤務地名</th>
        <th class="s_6">状態</th>
        <th class="s_8">都道府県</th>
        <th class="s_8">従業員数</th>
        <th class="s_12">電話番号</th>
        <th class="s_12">責任者名</th>
        <th>責任者メールアドレス</th>
        <th class="s_6"></th>
    </tr>
    @foreach ($work_locations as $place)
        <tr class="{{ $place->enable ? '' : 'light_gray' }}">
            @if ($can_change_view_order == true)
                <td>
                    <div class="input_wrapper">
                        <input class="ss_size view_order" autocomplete="off" name="to" type="text" value='{{ $place->view_order }}'>
                        <input name='from' type='hidden' autocomplete="off" value='{{ $place->view_order }}'>
                        <div class='error_wrapper'>
                            <span class="tool_error"></span>
                        </div>
                    </div>
                </td>
            @endif
            <td>{{ $place->presentation_id }}</td>
            <td>{{ $place->name }}</td>
            <td>{{ $place->enable ? '有効':'無効' }}</td>
            <td>{{ $place->todofuken() }}</td>
            <td>{{ $place->employees->count() }}</td>
            <td>{{ $place->telephone }}</td>
            <td>{{ $place->chief_last_name . $place->chief_first_name }}</td>
            <td>{{ $place->chief_email }}</td>
            <td>
                <p class="button"><a class="ss_size s_height btn_gray" href="{{ Caeru::route('edit_work_location', [$place->id, $work_locations->currentPage()]) }}">詳細</a></p>
            </td>
        </tr>
    @endforeach
</table>