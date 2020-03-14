@push('scripts')
    <script defer src="{{ asset('/js/components/work_address_searcher.js') }}"></script>
    <script defer src="{{ asset('/js/components/work_location_picker.js') }}"></script>
    <script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCTcCQSwQzudWCAhaAL2HAqtIp7V71_psA&v=3&libraries=places"></script>
    <script defer src="{{ asset('/js/components/place_picker.js') }}"></script>
    <script defer src="{{ asset('/js/components/place_picker_container.js') }}"></script>
@endpush

<main id="basic">
    <section class="title">
        <p class="breadcrumb"><span>基本情報</span><span>&emsp;&#62;&emsp;<a href="{{ $default_work_address ? Caeru::route('work_address_list', ['page' => $page]) : Caeru::route('work_address_list') }}">訪問先一覧</a></span><span>&emsp;&#62;&emsp;訪問先詳細</span></p>
        <div class="title_wrapper">
            <h1>訪問先詳細</h1>
            <div class="worklocation">
                <div class="worklocation_inner">
                    <span class="right_10">{{ $current_work_location }}</span>
                    @if (isset($picker_list))
                        <p class="button"><a class="modal-open ss_size s_height btn_gray" @click="open">変更</a></p>
                    @endif
                </div>
                @include('layouts.work_location_picker', ['list' => $picker_list, 'target' => $picker_redirect_to, 'singular' => false])
            </div>
    </section>
    @if ($use_searcher)
        @include('work_address.search_box', ['not_display_search_box' => true])
        @include('layouts.search_result_navigation')
    @endif
    @if (isset($default_work_address))
        <section class="select_one2">
            <section class="tab">
                <p class="tab_button"><a class="tab_size btn_greeen left right_10" href="">設定</a></p>
                <p class="tab_button"><a class="tab_size tab_btn_gray left" href="{{ Caeru::route('edit_work_address_detail', [$default_work_address->id, $page]) }}">訪問予定</a></p>
            </section>
        </section>
    @endif
    <form id="form" method="POST" form-single-submit action="{{ $route }}">
        {{ csrf_field() }}
        @if (isset($default_work_address))
            {{ method_field('PATCH') }}
        @endif
        <section class="setting_table">
            <table>
                <tr>
                    <td class="input_items">@can('change_work_address_info')<span class="required">必須</span>@endcan 訪問先ID</td>
                    <td>
                        @can('change_work_address_info')
                            @component('layouts.form.error', ['field' => 'presentation_id'])
                                <input class="s_size" name="presentation_id" value="{{ old('presentation_id', $default_presentation_id) }}" type="text">
                            @endcomponent
                        @else
                            {{ $default_presentation_id }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_work_address_info')<span class="required">必須</span>@endcan 訪問先名</td>
                    <td>
                        @can('change_work_address_info')
                            @component('layouts.form.error', ['field' => 'name'])
                                <input class="l_size" name="name" value="{{ old('name', $default_name) }}" type="text">
                            @endcomponent
                        @else
                            {{ $default_name }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_work_address_info')<span class="required">必須</span>@endcan フリガナ</td>
                    <td>
                        @can('change_work_address_info')
                            @component('layouts.form.error', ['field' => 'furigana'])
                                <input class="l_size" name="furigana" value="{{ old('furigana', $default_furigana) }}" type="text">
                            @endcomponent
                        @else
                            {{ $default_furigana }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">@can('change_work_address_s_work_location')<span class="required">必須</span>@endcan 勤務地名</td>
                    <td>
                        @can('change_work_address_s_work_location')
                            <div class="selectbox">
                                @component('layouts.form.error', ['field' => 'work_location_id'])
                                    @include('layouts.form.select_field', ['field' => 'work_location_id', 'class' => 'l_size', 'default' => $default_work_location_id, 'items' => $work_locations, 'multiple' => false])
                                @endcomponent
                            </div>
                        @else
                            {{ $work_locations[$default_work_location_id] }}
                            <input type="hidden" name="work_location_id" value="{{ $default_work_location_id }}">
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">状態</td>
                    <td>
                        @can('change_work_address_info')
                            <div class="selectbox">
                                @component('layouts.form.error', ['field' => 'enable'])
                                    <select class="m_size" name="enable">
                                        <option value="1" {{ old('enable', $default_enable) == 1 ? 'selected' : '' }}>有効</option>
                                        <option value="0" {{ old('enable', $default_enable) == 0 ? 'selected' : '' }}>無効</option>
                                    </select>
                                @endcomponent
                            </div>
                        @else
                            {{ ($default_enable == 1) ? '有効' : '無効' }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">郵便番号</td>
                    <td>
                        @can('change_work_address_info')
                            @include('layouts.form.2_cells_field', ['field' => 'postal_code', 'object' => $default_work_address])
                        @else
                            {{ $default_work_address->postal_code_1 ? ($default_work_address->postal_code_1 . '-' . $default_work_address->postal_code_2) : '' }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">住所</td>
                    <td>
                        <section class="second">
                            @can('change_work_address_info')
                                <div class="search_box right_10 left">
                                    <div class="selectbox">
                                        @component('layouts.form.error', ['field' => 'todofuken'])
                                            @include('layouts.form.nullable_select_field', ['field' => 'todofuken', 'class' => 'm_size', 'default' => $default_todofuken, 'items' => $todofuken_list, 'multiple' => false])
                                        @endcomponent
                                    </div>
                                </div>
                                <div class="left">
                                    @component('layouts.form.error', ['field' => 'address'])
                                        <input class="l_size right_30" name="address" value="{{ old('address', $default_address) }}" v-model="address" type="text">
                                    @endcomponent
                                    <span class="right_10">打刻許容範囲</span>
                                    @component('layouts.form.error', ['field' => 'login_range'])
                                        <input class="s_size right_10" name="login_range" value="{{ old('login_range', $default_login_range) }}" type="text">
                                    @endcomponent
                                    <span>m</span>
                                </div>
                            @else
                                {{ ($default_todofuken ? $todofuken_list[$default_todofuken] : '') . $default_address }}
                            @endcan
                        </section>
                        <section class="second">
                            <div class="left">
                                @can('change_work_address_info')
                                    <span class="right_10">緯度</span>
                                    @component('layouts.form.error', ['field' => 'latitude'])
                                        <input class="s_size right_30" name="latitude" v-model.number="workLatitude" value="{{ old('latitude', $default_latitude) }}" type="text">
                                    @endcomponent
                                    <span class="right_10">経度</span>
                                    @component('layouts.form.error', ['field' => 'longitude'])
                                        <input class="s_size right_10" name="longitude" v-model.number="workLongitude" value="{{ old('longitude', $default_longitude) }}" type="text">
                                    @endcomponent
                                    <p class="button"><a class="mm_size s_height btn_gray left_30" @click="toggle">GoogleMapから取得</a></p>
                                @else
                                    <span class="right_30">打刻許容範囲: {{ $default_login_range ? $default_login_range . 'm':'' }}</span><span class="right_30">緯度: {{ $default_latitude }}</span><span class="right_30">経度: {{ $default_longitude }}</span>@if($default_latitude)<img class="ico_balloon" src="{{ asset('images/map_balloon.svg') }}" @click="toggle">@endif
                                @endcan
                            </div>
                        </section>
                    </td>
                </tr>
                <tr>
                    <td class="input_items">電話番号</td>
                    <td>
                        @can('change_work_address_info')
                            @include('layouts.form.3_cells_field', ['field' => 'telephone', 'object' => $default_work_address])
                        @else
                            {{ $default_work_address->telephone_1 ? ($default_work_address->telephone_1 . '-' . $default_work_address->telephone_2 . '-' . $default_work_address->telephone_3) : '' }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">責任者名</td>
                    <td>
                        @can('change_work_address_info')
                            @include('layouts.form.name_field', ['additional' => 'chief_', 'object' => $default_work_address, 'kana' => false])
                        @else
                            {{ $default_work_address->chief_last_name . $default_work_address->chief_first_name }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">責任者名(カナ)</td>
                    <td>
                        @can('change_work_address_info')
                            @include('layouts.form.name_field', ['additional' => 'chief_', 'object' => $default_work_address, 'kana' => true])
                        @else
                            {{ $default_work_address->chief_last_name_furigana . $default_work_address->chief_first_name_furigana }}
                        @endcan
                    </td>
                </tr>
                <tr>
                    <td class="input_items">責任者メールアドレス</td>
                    <td>
                        @can('change_work_address_info')
                            @component('layouts.form.error', ['field' => 'chief_email'])
                                <input class="l_size" name="chief_email" value="{{ old('chief_email', $default_chief_email) }}" type="text">
                            @endcomponent
                        @else
                            {{ $default_chief_email }}
                        @endcan
                    </td>
                </tr>
            </table>
        </section>
        <section class="btn">
            @can('change_work_address_info')
                <p class="button right_30"><button class="m_size l_height btn_greeen l_font" >保存</button></p>
                <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ $default_work_address ? Caeru::route('edit_work_address', [$default_work_address->id, $page]) : Caeru::route('create_work_address') }}">キャンセル</a></p>
            @endcan
            <p class="button"><a class="m_size l_height btn_gray l_font" href="{{ $default_work_address ? Caeru::route('work_address_list', ['page' => $page]) : Caeru::route('work_address_list') }}">一覧に戻る</a></p>
        </section>
        @can('change_work_address_info')
            <place-picker :display="placePickerDisplay" :lat="workLatitude" :lng="workLongitude" :read_only="false" :addr="address"
                @close="toggle"
                @change-position="changeGeocode"
                @change-address="changeAddress"
            ></place-picker>
        @else
            <place-picker :display="placePickerDisplay" :lat="{{ $default_latitude ? $default_latitude : '0' }}" :lng="{{ $default_longitude ? $default_longitude : '0' }}" :read_only="true" @close="toggle"></place-picker>
        @endcan
    </form>
</main>
