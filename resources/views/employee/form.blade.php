@push('scripts')
    <script defer src="{{ asset('/js/multiple-select.js') }}"></script>
    <script defer src="{{ asset('/js/components/employee_searcher.js') }}"></script>
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
    <script defer src="{{ asset('/js/pages/employee_basic_info_form.js') }}"></script>
@endpush

<main id="basic">
    <section class="title">
        <p class="breadcrumb"><span>基本情報</span>&emsp;&#62;&emsp;<a href="{{ $default_employee ? Caeru::route('employees_list', ['page' => $page]) : Caeru::route('employees_list') }}">従業員一覧</a><span>&emsp;&#62;&emsp;従業員詳細</span></p>
        <div class="title_wrapper">
            <h1>従業員詳細</h1>
            <div class="worklocation">
                <div class="worklocation_inner">
                    <span class="right_10">{{ $current_work_location }}</span>
                    @if (isset($picker_list))
                        <p class="button"><a class="modal-open ss_size s_height btn_gray" @click="open">変更</a></p>
                    @endif
                </div>
                @include('layouts.work_location_picker', ['list' => $picker_list, 'target' => $picker_redirect_to, 'singular' => false])
            </div>
        </div>
    </section>
    @if ($use_searcher)
        @include('employee.search_box')
        @include('layouts.search_result_navigation')
    @endif
    @if (isset($default_employee))
        <section class="select_one2">
            <section class="tab">
                <p class="tab_button"><a class="tab_size btn_greeen left right_10" href="">基本</a></p>
                @can('view_employee_work_info')
                    <p class="tab_button"><a class="tab_size tab_btn_gray left" href="{{ Caeru::route('edit_employee_work', [$default_employee->id, $page]) }}">勤怠</a></p>
                @endcan
            </section>
        </section>
    @endif
    <form id="form" method="POST" form-single-submit action="{{ $route }}">
        {{ csrf_field() }}
        @if (isset($default_employee))
            {{ method_field('PATCH') }}
        @endif
        <section class="setting_table">
            <table>
                <tr>
                    <td class="input_items">@can('change_employee_basic_info')<span class="required">必須</span>@endcan 従業員ID</td>
                    <td>
                        @can('change_employee_basic_info')
                            @component('layouts.form.error', ['field' => 'presentation_id'])
                                <input class="s_size" name="presentation_id" value="{{ old('presentation_id', $default_presentation_id) }}" type="text">
                            @endcomponent
                        @else
                            {{ $default_presentation_id }}
                        @endcan
                    </td>
                </tr>
                @can('change_employee_basic_info')
                    <tr>
                        <td class="input_items">パスワード(英数)</td>
                        <td>
                            @component('layouts.form.error', ['field' => 'password'])
                                <input class="s_size" name="password" type="password" value="">
                            @endcomponent
                        </td>
                    </tr>
                @endcan
                <tr>
                    <td class="input_items">@can('change_employee_basic_info')<span class="required">必須</span>@endcan 従業員名</td>
                    <td>
                        @can('change_employee_basic_info')
                            @include('layouts.form.name_field', ['additional' => '', 'object' => $default_employee, 'kana' => false])
                        @else
                            {{ $default_employee->last_name . $default_employee->first_name }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_employee_basic_info')<span class="required">必須</span>@endcan 従業員名(カナ)</td>
                    <td>
                        @can('change_employee_basic_info')
                            @include('layouts.form.name_field', ['additional' => '', 'object' => $default_employee, 'kana' => true])
                        @else
                            {{ $default_employee->last_name_furigana . $default_employee->first_name_furigana }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_employee_basic_info')<span class="required">必須</span>@endcan 生年月日</td>
                    <td>
                        @can('change_employee_basic_info')
                            @include('layouts.form.date_field', ['field' => 'birthday', 'object' => $default_employee])
                        @else
                            {{ $default_employee->birthday_1 . '-' . $default_employee->birthday_2 . '-' . $default_employee->birthday_3 }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_employee_basic_info')<span class="required">必須</span>@endcan 性別</td>
                    <td>
                        @can('change_employee_basic_info')
                            @include('layouts.form.radio_field', ['field' => 'gender', 'object' => $default_employee, 'options' => $genders, 'default' => $default_gender ])
                        @else
                            {{ $genders[$default_employee->gender] }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">郵便番号</td>
                    <td>
                        @can('change_employee_basic_info')
                            @include('layouts.form.2_cells_field', ['field' => 'postal_code', 'object' => $default_employee])
                        @else
                            {{ $default_employee->postal_code_1 ? ($default_employee->postal_code_1 . '-' . $default_employee->postal_code_2) : '' }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">住所</td>
                    <td>
                        <section class="second">
                            @can('change_employee_basic_info')
                                <div class="search_box right_10 left">
                                    <div class="selectbox">
                                        @component('layouts.form.error', ['field' => 'todofuken'])
                                            @include('layouts.form.nullable_select_field', ['field' => 'todofuken', 'class' => 's_size', 'default' => $default_todofuken, 'items' => $todofuken_list, 'multiple' => false])
                                        @endcomponent
                                    </div>
                                </div>
                                @component('layouts.form.error', ['field' => 'address'])
                                    <input class="l_size right_30" name="address" value="{{ old('address', $default_address) }}" type="text">
                                @endcomponent
                            @else
                                {{ ($default_todofuken ? $todofuken_list[$default_todofuken] : '') . $default_address }}
                            @endcan
                        </section>
                    </td>
                </tr>
                <tr>
                    <td class="input_items">電話番号</td>
                    <td>
                        @can('change_employee_basic_info')
                            @include('layouts.form.3_cells_field', ['field' => 'telephone', 'object' => $default_employee])
                        @else
                            {{ $default_employee->telephone_1 ? ($default_employee->telephone_1 . '-' . $default_employee->telephone_2 . '-' . $default_employee->telephone_3) : '' }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">メールアドレス</td>
                    <td>
                        @can('change_employee_basic_info')
                            @component('layouts.form.error', ['field' => 'email'])
                                <input class="l_size" name="email" value="{{ old('email', $default_email) }}" type="text">
                            @endcomponent
                        @else
                            {{ $default_email }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_employee_s_work_location')<span class="required">必須</span>@endcan 所属先名</td>
                    <td>
                        @can('change_employee_s_work_location')
                            <div class="selectbox">
                                @component('layouts.form.error', ['field' => 'work_location_id'])
                                    @include('layouts.form.nullable_select_field', ['field' => 'work_location_id', 'class' => 'l_size', 'default' => $default_work_location, 'items' => $work_locations, 'multiple' => false])
                                @endcomponent
                            </div>
                        @else
                            {{ $work_locations[$default_work_location] }}
                            <input type="hidden" name="work_location_id" value="{{ $default_work_location }}">
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_employee_basic_info')<span class="required">必須</span>@endcan 入社日</td>
                    <td>
                        @can('change_employee_basic_info')
                            @include('layouts.form.date_field', ['field' => 'joined_date', 'object' => $default_employee])
                        @else
                            {{ $default_employee->joined_date_1 . '-' . $default_employee->joined_date_2 . '-' . $default_employee->joined_date_3 }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">部署</td>
                    <td>
                        @can('change_employee_basic_info')
                            <div class="selectbox">
                                @component('layouts.form.error', ['field' => 'department_id'])
                                    <select class='m_size' name='department_id'></select>
                                @endcomponent
                            </div>
                        @else
                            {{ $default_department }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_employee_basic_info')<span class="required">必須</span>@endcan 就労形態</td>
                    <td>
                        @can('change_employee_basic_info')
                            <div class="selectbox">
                                @component('layouts.form.error', ['field' => 'schedule_type'])
                                    @include('layouts.form.select_field', ['field' => 'schedule_type', 'class' => 'm_size', 'default' => $default_schedule_type, 'items' => $schedule_types, 'multiple' => false])
                                @endcomponent
                            </div>
                        @else
                            {{ $schedule_types[$default_schedule_type] }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_employee_basic_info')<span class="required">必須</span>@endcan 採用形態</td>
                    <td>
                        @can('change_employee_basic_info')
                            <div class="selectbox">
                                @component('layouts.form.error', ['field' => 'employment_type'])
                                    @include('layouts.form.select_field', ['field' => 'employment_type', 'class' => 'm_size', 'default' => $default_employment_type, 'items' => $employment_types, 'multiple' => false])
                                @endcomponent
                            </div>
                        @else
                            {{ $employment_types[$default_employment_type] }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_employee_basic_info')<span class="required">必須</span>@endcan 給与形態</td>
                    <td>
                        @can('change_employee_basic_info')
                            <div class="selectbox">
                                @component('layouts.form.error', ['field' => 'salary_type'])
                                    @include('layouts.form.select_field', ['field' => 'salary_type', 'class' => 'm_size', 'default' => $default_salary_type, 'items' => $salary_types, 'multiple' => false])
                                @endcomponent
                            </div>
                        @else
                            {{ $salary_types[$default_salary_type] }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_employee_basic_info')<span class="required">必須</span>@endcan 雇用状態</td>
                    <td>
                        @can('change_employee_basic_info')
                            <div class="selectbox">
                                @component('layouts.form.error', ['field' => 'work_status'])
                                    @include('layouts.form.select_field', ['field' => 'work_status', 'class' => 'm_size', 'default' => $default_work_status, 'items' => $work_statuses, 'multiple' => false])
                                @endcomponent
                            </div>
                        @else
                            {{ $work_statuses[$default_work_status] }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">退職日</td>
                    <td>
                        @can('change_employee_basic_info')
                            @include('layouts.form.date_field', ['field' => 'resigned_date', 'object' => $default_employee])
                        @else
                            {{ isset($default_employee->resigned_date) ? $default_employee->resigned_date_1 . '-' . $default_employee->resigned_date_2 . '-' . $default_employee->resigned_date_3 : '' }}
                        @endcan
                    </td>
                </tr>
                @if (isset($default_employee))
                    <tr>
                        <td class="input_items">承認対象者</td>
                        <td class="underline"><a href ="{{ Caeru::route('employee_approval_list', [$default_employee->id, $page, 'detail']) }}">{{ $default_employee->subordinates()->count() }}人</a></td>
                    </tr>
                @endif
                <tr class="chiefs_panel">
                    <td class="input_items">承認者</td>
                    <td>
                        <div class="search_box">
                            <div class="input_wrapper approval {{ $errors->has('chiefs.*') ? 'error':'' }}">
                                <span v-for="(chief, key) in chiefs" class="right_30">@{{ getChiefName(chief) }}
                                    <i @click="chiefRemove(key)" class="fa fa-times-circle" aria-hidden="true"></i>
                                    <input type="hidden" name="chiefs[]" :value="chief">
                                </span>
                                @if ($errors->has('chiefs.*'))
                                    <div class='error_wrapper'>
                                        <span class="tool_error">{{ $errors->first('chiefs.*') }}</span>
                                    </div>
                                @endif
                            <!-- No newline here because of the damn 'speck'-->
                            </div><autocomplete :suggestions="employees" custom-class="m_size" :current-id="resetId" :linked="true" @selected="chiefAdd" @enter-pressed="submit"></autocomplete>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
        <section class="btn">
            @can('change_employee_basic_info')
                <p class="button right_30"><button class="m_size l_height btn_greeen l_font" >保存</button></p>
                <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ $default_employee ? Caeru::route('edit_employee', [$default_employee->id, $page]) : Caeru::route('create_employee') }}">キャンセル</a></p>
            @endcan
            <p class="button"><a class="m_size l_height btn_gray l_font" href="{{ $default_employee ? Caeru::route('employees_list', ['page' => $page]) : Caeru::route('employees_list') }}">一覧に戻る</a></p>
        </section>
    </form>
</main>