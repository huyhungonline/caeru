<section class="pager">
    {{ $employees->links(null, ['sum_line' => true, 'force_url' => 'totalization_list']) }}
</section>
<section class="approval_table">
    <table _fixedhead='rows:1; cols:2;'>
        <tr>
            <th class="s_10">従業員ID</th>
            <th>名前</th>
            <th class="s_10">申請数</th>                   
            <th class="s_10">申請中</th>
            <th class="s_10">承認済</th>
            <th class="s_10">打刻エラー</th>
            <th class="s_10">判定エラー</th>
            <th class="s_10">管理者1</th>
            <th class="s_10">管理者2</th>
        </tr>
        @foreach($employees as $employee)
        <tr>
            <td>{{ $employee->id ."----". $employee->presentation_id }}</td>
            <td>{{ $employee->first_name . $employee->last_name }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <img class="check_blue ico_ico_arrow3" src="{{ asset('images/ico_check_blue.svg') }}">
            </td>
            <td>
                <p class="button">
                    <a class="ss_size s_height btn_red" href="#">
                        <img class="ico_ico_arrow2" src="{{ asset('images/ico_check_red.svg') }}">
                    </a>
                </p>
                
            </td>
        </tr>
        @endforeach
    </table>
</section>
<section class="pager">
    {{ $employees->links(null, ['sum_line' => false, 'force_url' => 'totalization_list']) }}
</section>
<script defer type="text/javascript">
    {{-- window.checklists = {!! $checklistsJson !!}; --}}
    {{-- window.checklistsHistory = {!! $checklistsHistory !!}; --}}
    {{-- window.checklistsDisplayHistory = {!! $displayHistory !!} --}}
</script>
