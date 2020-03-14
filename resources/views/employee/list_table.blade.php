@push('scripts')
    <script defer src="{{ asset('/js/components/change_view_order.js') }}"></script>
@endpush

<input type='hidden' name='current_page' value='{{ $employees->currentPage() }}'>
<input type='hidden' name='object_type' value='2'>
@if (is_numeric(session("current_work_location")))
    <input type="hidden" name='current_work_location' value="{{ session('current_work_location') }}">
@endif
<table class="table_with_fixed_header">
    <tr class="fixed_header">
        @if (is_numeric(session("current_work_location")))
            <th class="s_6">並び順</th>
        @endif
        <th class="s_8">従業員ID</th>
        <th class="s_12">従業員名</th>
        @if (!is_numeric(session("current_work_location")))
            <th class="s_20">所属先</th>
        @endif
        <th class="s_10">就労形態</th>
        <th class="s_6">採用形態</th>
        <th class="s_12">部署</th>
        <th class="s_5">性別</th>
        <th class="s_6">雇用状態</th>
        <th class="s_8">承認対象者</th>
        <th class="s_6">承認者</th>
        <th class="s_8"></th>
    </tr>
    @foreach ($employees as $employee)
        <tr class="{{ $employee->work_status === 1 ? '' : 'light_gray' }}">
            @if (is_numeric(session("current_work_location")))
                <td>
                    <div class="input_wrapper">
                        <input class="ss_size view_order" autocomplete="off"　name="to" type="text" value='{{ $employee->masked_view_order }}'>
                        <input name='from' type='hidden' autocomplete="off" value='{{ $employee->masked_view_order }}'>
                        <div class='error_wrapper'>
                            <span class="tool_error"></span>
                        </div>
                    </div>
                </td>
            @endif
            <td>{{ $employee->presentation_id }}</td>
            <td>{{ $employee->last_name . $employee->first_name }}</td>
            @if (!is_numeric(session("current_work_location")))
                <td>{{ $employee->workLocation->name }}</td>
            @endif
            <td>{{ Constants::scheduleTypes()[$employee->schedule_type] }}</td>
            <td>{{ Constants::employmentTypes()[$employee->employment_type] }}</td>
            <td>{{ $employee->departmentName() }}</td>
            <td>{{ Constants::genders()[$employee->gender] }}</td>
            <td>{{ Constants::workStatuses()[$employee->work_status] }}</td>
            <td class="underline"><a href="{{ Caeru::route('employee_approval_list', [$employee->id, $employees->currentPage(), 'list']) }}">{{ $employee->subordinates()->count() }}人</a></td>
            <td>{{ $employee->chiefs()->count() }}人</td>
            <td>
                @can('view_employee_basic_info')
                    <p class="button"><a class="ss_size s_height btn_gray" href="{{ Caeru::route('edit_employee', [$employee->id, $employees->currentPage()]) }}">詳細</a></p>
                @else
                    <p class="button"><a class="ss_size s_height btn_gray" href="{{ Caeru::route('edit_employee_work', [$employee->id, $employees->currentPage()]) }}">詳細</a></p>
                @endif
            </td>
        </tr>
    @endforeach
</table>