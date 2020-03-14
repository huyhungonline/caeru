@push('scripts')
    <script defer src="{{ asset('/js/multiple-select.js') }}"></script>
    <script defer src="{{ asset('/js/pages/manager_page.js') }}"></script>
@endpush

<main id="basic">
    <section class="title">
        <p class="breadcrumb"><span>基本情報</span>&emsp;&#62;&emsp;<a href="{{ $default_manager ? Caeru::route('managers_list', ['page' => $page]) : Caeru::route('managers_list') }}">管理者一覧</a><span>&emsp;&#62;&emsp;管理者詳細 </span></p>
        <div class="title_wrapper">
            <h1>管理者詳細 </h1>
        </div>
    </section>
    <form id="form" method="POST" form-single-submit action="{{ $route }}">
        {{ csrf_field() }}
        @if (isset($manager))
            {{ method_field('PATCH') }}
        @endif
        <section class="setting_table">
            <table>
                <tr>
                    <td class="input_items"><span class="required">必須</span>管理者ID</td>
                    <td>
                        @component('layouts.form.error', ['field' => 'presentation_id'])
                            <input class="s_size" name="presentation_id" value="{{ old('presentation_id', $default_presentation_id) }}" type="text">
                        @endcomponent
                    </td>
                </tr>
                <tr>
                    <td class="input_items"><span class="required">必須</span>管理者名</td>
                    <td>
                        @include('layouts.form.name_field', ['additional' => '', 'object' => $default_manager, 'kana' => false])
                    </td>
                </tr>
                <tr>
                    <td class="input_items"><span class="required">必須</span>管理者(カナ)</td>
                    <td>
                        @include('layouts.form.name_field', ['additional' => '', 'object' => $default_manager, 'kana' => true])
                    </td>
                </tr>
                <tr>
                    <td class="input_items"><span class="required">必須</span>パスワード</td>
                    <td>
                        @component('layouts.form.error', ['field' => 'password'])
                            <input class="s_size" name="password" type="password" value="{{ $default_password }}">
                        @endcomponent
                    </td>
                </tr>
                <tr>
                    <td class="input_items"><span class="required">必須</span>パスワード再入力</td>
                    <td>
                        @component('layouts.form.error', ['field' => 'password_confirmation'])
                            <input class="s_size" name="password_confirmation" type="password" value="{{ $default_password_confirm }}">
                        @endcomponent
                    </td>
                </tr>
                <tr>
                    <td class="input_items">IPアドレス制限</td>
                    <td>
                    <section class="second">
                        <div class="input_wrapper {{ $errors->has('ip_address') ? 'error':'' }}">
                            <textarea class="m_size ll_height right_10" name="ip_address">{{ old('ip_address', $default_ip_address) }}</textarea>からのアクセスだけを許可する
                            @if ($errors->has('ip_address'))
                                <div class='error_wrapper'>
                                    <span class="textarea_error">{{ $errors->first('ip_address') }}</span>
                                </div>
                            @endif
                        </div>
                    </section>
                    </td>
                </tr>
                <tr>
                    <td class="input_items">電話番号</td>
                    <td>
                        @include('layouts.form.3_cells_field', ['field' => 'telephone', 'object' => $default_manager])
                    </td>
                </tr>
                <tr>
                    <td class="input_items">メールアドレス</td>
                    <td>
                        @component('layouts.form.error', ['field' => 'email'])
                            <input class="l_size" name="email" type="text" value="{{ old('email', $default_email) }}">
                        @endcomponent
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@if ($default_super != true)<span class="required">必須</span>@endif状態</td>
                    <td>
                        <div class="selectbox">
                            @if ($default_super != true)
                                @component('layouts.form.error', ['field' => 'enable'])
                                    <select class="m_size" name="enable">
                                        <option value="1" {{ old('enable', $default_enable) == 1 ? 'selected' : '' }}>有効</option>
                                        <option value="0" {{ old('enable', $default_enable) == 0 ? 'selected' : '' }}>無効</option>
                                    </select>
                                @endcomponent
                            @else
                                <span>有効</span>
                                <input type="hidden" name="enable" value="1">
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </section>
        <section class="setting_table secound_block">
            <h2>管理勤務地</h2>
            <table>
                <tr>
                    @if ($default_super != true)
                        <td class="input_items"><span class="required">必須</span>勤務地選択</td>
                        <td>
                            @include('layouts.form.radio_field', ['field' => 'company_wide_authority', 'object' => $default_manager, 'options' => array(1 => '会社', 0 => '勤務地'), 'default' => $default_company_wide_authority])
                            <div class="search_box right_30 left_10 side_input_block">
                                <section class="form-group m_size">
                                    @if ($errors->has('authorized_work_locations.*') || $errors->has('authorized_work_locations'))
                                        <div class="input_wrapper multiple_select">
                                    @endif
                                    @include('layouts.form.select_field', ['field' => 'authorized_work_locations', 'class' => 'm_size ms', 'default' => $default_authorized_locations, 'items' => $work_locations, 'multiple' => true])
                                    @if ($errors->has('authorized_work_locations.*') || $errors->has('authorized_work_locations'))
                                        <div class='error_wrapper'>
                                            <span class="tool_error">{{ $errors->first('authorized_work_locations.*') }}{{ $errors->first('authorized_work_locations') }}</span>
                                        </div>
                                    @endif
                                    </div>
                                </section>
                           </div>
                        </td>
                    @else
                        <td class="input_items">勤務地</td>
                        <td>会社</td>
                        <input type="hidden" name="company_wide_authority" value="1">
                    @endif
                </tr>
            </table>
        </section>
        <section class="admin_table secound_block">
            <h2>詳細設定</h2>
            <table>
                <tr>
                    <td rowspan="9" class="s_10 bg_gray">基本情報</td>
                    <td class="s_14 bg_gray">会社情報</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'company_information', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">勤務地情報</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'work_location_information', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">訪問先情報</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'work_address_information', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">従業員情報（基本）</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'employee_basic_information', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">従業員情報（勤怠）</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'employee_work_information', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">カレンダー</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'calendar_setting', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">設定</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'setting', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">項目設定（勤務形態）</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'statuses_setting', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">項目設定（部署）</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'department_type_setting', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td rowspan="10" class="s_10 bg_gray">勤怠管理</td>
                    <td class="s_14 bg_gray">勤怠データ管理</td>
                    <td class="s_30">
                        @include('layouts.form.radio_field', ['field' => 'work_data_management', 'object' => $default_authority, 'options' => $two_authority_types, 'default' => 0 ])
                    </td>
                    <td rowspan="5">
                        <section class="check_box_wrap right_30 side_input_block">
                            @include('layouts.form.checkbox_field', ['field' => 'work_data_modify', 'label' => '勤怠修正', 'object' => $default_authority, 'default' => 0])
                        </section>
                        <section class="check_box_wrap right_30 side_input_block">
                            @include('layouts.form.checkbox_field', ['field' => 'work_data_modify_request_confirm', 'label' => '申請確認', 'object' => $default_authority, 'default' => 0])
                        </section>
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">勤怠データ検索</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'work_data_search', 'object' => $default_authority, 'options' => $two_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">勤怠集計</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'work_data_calculation', 'object' => $default_authority, 'options' => $two_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">勤怠データ詳細</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'work_data_detail', 'object' => $default_authority, 'options' => $two_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">個人別詳細</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'work_data_personal_detail', 'object' => $default_authority, 'options' => $two_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">有給休暇管理</td>
                    <td colspan="2">
                        @include('layouts.form.radio_field', ['field' => 'work_data_paid_holiday_management', 'object' => $default_authority, 'options' => [1 => '更新', 2 => '閲覧', 0 => '非表示'], 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">有給休暇詳細</td>
                    <td colspan="2">
                        @include('layouts.form.radio_field', ['field' => 'work_data_paid_holiday_detail', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">訪問先別勤務情報</td>
                    <td colspan="2">
                        @include('layouts.form.radio_field', ['field' => 'work_data_addresses', 'object' => $default_authority, 'options' => $two_authority_types, 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="bg_gray">訪問先別詳細</td>
                    <td colspan="2">
                        @include('layouts.form.radio_field', ['field' => 'work_data_address_detail', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr> 
                <tr>
                    <td class="bg_gray">訪問勤務詳細</td>
                    <td colspan="2">
                        @include('layouts.form.radio_field', ['field' => 'work_data_address_work_detail', 'object' => $default_authority, 'options' => $three_authority_types, 'default' => 0 ])
                    </td>
                </tr>
            </table>
        </section>
        <section class="setting_table secound_block">
            <h2>締め管理者</h2>
            <table>
                <tr>
                    <td class="input_items">管理者1<span class="tooltip left_30"><a href="#"><img src="{{ asset('images/ico_hatena.svg') }}"><span class="tool_description fixed">従業員個人ごとの１日の締め・１ヶ月ごとの締め１日の締め・１ヶ月ごとの締め１日の締め</span></a></span></td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'approval_level_one', 'object' => $default_authority, 'options' => [1 => '有り', 0 => '無し'], 'default' => 0 ])
                    </td>
                </tr>
                <tr>
                    <td class="input_items">管理者2</td>
                    <td>
                        @include('layouts.form.radio_field', ['field' => 'approval_level_two', 'object' => $default_authority, 'options' => [1 => '有り', 0 => '無し'], 'default' => 0 ])
                    </td>
                </tr>
            </table>
        </section>
        <section class="btn">
            <p class="button right_30"><button class="m_size l_height btn_greeen l_font" >保存</button></p>
            <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ $default_manager ? Caeru::route('edit_manager', [$default_manager->id, $page]) : Caeru::route('create_manager') }}">キャンセル</a></p>
            <p class="button"><a class="m_size l_height btn_gray l_font" href="{{ $default_manager ? Caeru::route('managers_list', ['page' => $page]) : Caeru::route('managers_list') }}">一覧に戻る</a></p>
        </section>
    </form>
</main>