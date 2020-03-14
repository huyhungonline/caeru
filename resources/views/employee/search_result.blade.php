<section class="pager">
    {{ $employees->links(null, ['sum_line' => true, 'force_url' => 'employees_list']) }}
    <section class="right_position">
        @can('change_employee_basic_info')
            <p class="button"><a class="m_size s_height btn_blue" href="{{ Caeru::route('create_employee') }}">新規登録</a></p>
        @endcan
        <p class="button left_10"><a class="m_size s_height btn_gray modal-open" data-target="con1" href="#">ダウンロード</a></p>
    </section>
</section>
<section class="default_table">
    @include('employee.list_table', ['employees' => $employees])
</section>
<section class="pager">
    {{ $employees->links(null, ['sum_line' => false, 'force_url' => 'employees_list']) }}
</section>