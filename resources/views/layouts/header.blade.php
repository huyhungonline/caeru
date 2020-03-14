
@push('scripts')
    <script defer src="{{ asset('/js/mics.js') }}" defer></script>
    <script defer src="{{ asset('/js/components/alert_module.js') }}" defer></script>
@endpush

<section id="header_wrapper">
    <header>
        <section class="alert_wrapper">
            <section class="alert_innner">
                <section class="alert_box save_green"><span class="alert_ico right_10"><img src="{{ asset('images/ico_alert_save.svg') }}"></span>
                    @if (session()->has('success'))
                        <span class="alert_content">{{ session('success') }}</span>
                    @endif
                </section>
                <section class="alert_box save_yellow"><span class="alert_ico right_10"><img src="{{ asset('images/ico_alert_dakoku.svg') }}"></span>
                    @if (session()->has('warning'))
                        <span class="alert_content">{{ session('warning') }}</span>
                    @endif
                </section>
                <section class="alert_box save_red"><span class="alert_ico right_10"><img src="{{ asset('images/ico_alert_error.svg') }}"></span>
                    @if (count($errors) || session()->has('error'))
                        <span class="alert_content">{{ session()->has('error') ? session('error') : '入力に誤りがあります' }}</span>
                    @endif
                </section>
            </section>
        </section>
        <a href ="{{ Caeru::route('dashboard') }}"><div id ="logo"></div></a>
        <div id ="sign_out"><a href ="{{ Caeru::route('logout') }}">ログアウト</a></div>
    </header>
    <nav>
        <ul id="dropmenu" class="gnav">
            <li class="home"><a class="{{ $active == 1 ? 'nav_home':'' }}" href ="{{ Caeru::route('dashboard') }}"></a></li>
            <li class ="green">
                <a href ="#" class ="green {{ $active == 2 ? 'nav_basic':'' }}">基本情報</a>
                <ul>
                    @can('view_company_info')
                        <li><a href ="{{ Caeru::route('edit_company') }}">会社情報</a></li>
                    @endcan
                    @can('change_manager_info')
                        <li><a href ="{{ Caeru::route('managers_list') }}">管理者情報</a></li>
                    @endcan
                    @can('view_work_location_info')
                        <li><a href ="{{ Caeru::route('work_locations_list') }}">勤務地情報</a></li>
                    @endcan
                    @if ( Auth::user()->company->use_address_system == true )
                        @can('view_work_address_info')
                            <li><a href ="{{ Caeru::route('work_address_list') }}">訪問先情報</a></li>
                        @endcan
                    @endif
                    @can('see_employee_tab')
                        <li><a href ="{{ Caeru::route('employees_list') }}">従業員情報</a></li>
                    @endcan
                    @can('view_calendar')
                        <li><a href ="{{ Caeru::route('edit_calendar') }}">カレンダー</a></li>
                    @endcan
                    @can('view_setting_info')
                        <li><a href ="{{ Caeru::route('edit_setting') }}">設定</a></li>
                    @endcan
                    @can('view_option_item_info')
                        @can('view_option_info')
                            <li><a href ="{{ Caeru::route('edit_option_work_rest') }}">項目設定</a></li>
                        @else
                            <li><a href ="{{ Caeru::route('edit_option_department') }}">項目設定</a></li>
                        @endcan
                    @endcan
                </ul>
            </li>
            <li class ="yellow">
                <a href ="#" class ="yellow {{ $active == 3 ? 'nav_attendance':'' }}">勤怠管理</a>
                <ul>
                    <li><a href="attendance_management.html">勤怠データ管理</a></li>
                    <li><a href="attendance_search.html">勤怠データ検索</a></li>
                    <li><a href="{{Caeru::route('totalization_list')}}">勤怠集計</a></li>
                    <li><a href="{{Caeru::route('paid_holiday_list')}}">有給休暇管理</a></li>
                    <li><a href="{{Caeru::route('attendance_address_infor')}}">訪問先別勤務情報</a></li>
                    <li><a href ="{{Caeru::route('checklists_list')}}">チェックリスト</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</section>