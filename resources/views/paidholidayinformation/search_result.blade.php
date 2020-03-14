<section class="pager">
    <section class="left_position">
        <section class="side_input_block right_10"><span class="right_10">チェックしたものを</span><p class="button"><a class="ss_size s_height btn_white" href="#">更新</a></p></section>
        <section class="side_input_block">
            <p class="button"><a class="s_size s_height btn_white" href="#">全員を更新</a>              
        </section>
    </section>

	{{ $employees->links(null, ['sum_line' => true]) }}

</section>
<section class="approval_table">
	<table _fixedhead='rows:1; cols:1;'>
		<tr>
			<th class="s_4">
                <div class="check_onle_wrap">
					<label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
				</div>
            </th>
			<th class="s_6">従業員ID</th>
			<th class="s_8">従業員名</th>
			@if($current_work_location==='会社')
            <th class="s_12">所属先</th>
            @endif
			<th class="s_6">週勤務日数</th>
			<th class="s_8">入社日</th>
			<th class="s_6">勤続年数</th>
            <th class="s_4">更新日</th>
            <th class="s_8">更新前年度出勤率</th>
            <th class="s_5">付与日数</th>
            <th class="s_8"><p>繰越日数</p><p>日:時間:分</p></th>
            <th class="s_8"><p>残日数</p><p>日:時間:分</p></th>
            <th>操作年月日</th>
            <th class="s_5">更新</th>
            <th class="s_5"></th>
		</tr>

		@foreach($employees as $employee)
	        <tr>
				<td>
	                <div class="check_onle_wrap">
						<label class="checkbox_box"><input name="hugahuga" type="checkbox" value="hoge"></label>
					</div>
	            </td>
	            <td>{{ $employee->presentation_id }}</td>
				<td>{{ $employee->first_name . $employee->last_name }}</td>
				@if($current_work_location==='会社')
				<td>{{ $employee->work_location_name }}</td>
				@endif
	            <td>{{ $employee->holiday_bonus_type }}</td>
	            <td>{{ $employee->joined_date }}</td>
	            <td>2年6ヶ月</td>
	            <td>04/01</td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td>2016-04-03</td>
	            <td>
	                <p class="button"><a class="ss_size s_height btn_white" href="#">更新</a></p>
	            </td>
				<td>
					<p class="button">
						<a class="ss_size s_height btn_gray" href="{{Caeru::route('edit_paid_holiday', [$employee->id, $employees->currentPage()]) }}">詳細</a>
					</p>
				</td>
			</tr>
		@endforeach
    </table>
</section>
<section class="pager">
	{{ $employees->links(null, ['sum_line' => false]) }}
</section>