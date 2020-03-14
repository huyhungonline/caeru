<div id="form-first">
    @can('change_option_info')
        @if (Session::get('current_work_location') == "all")
            <section class="add_item">
            <div class="search_box right_10 left">
                <span>形態追加</span>
            </div>
            <div class="selectbox left right_10">
                <select class="s_size" v-model="selected">
                    <option value="勤務形態">勤務形態</option>
                    <option value="休日形態">休日形態</option>
                </select>
            </div>
            <div class="search_box right_30 left">
                <div class="input_wrapper" :class="checkError">
                    <input v-model="name_option" ref='search' class="m_size" @keydown.enter.prevent="addRows" type="text">
                    <div class='error_wrapper'>
                        <span class="tool_error">@{{ message_error }}</span>
                    </div>
                </div>
            </div>
            <p class="button left"><button class="s_size s_height btn_gray" v-on:click="addRows">追加</button></p>
            </section>
        @endif
    @endcan
    <section class="default_table separate">
        <table id="勤務形態" v-cloak class="s_29 left">
            <tr>
                <th>勤務形態</th>
                @if (Session::get('current_work_location') == "all")
                    @can('change_option_info')
                        <th class="s_20"></th>
                    @endcan
                @else
                    <th class="s_40">利用</th>
                @endif
            </tr>
            <tr v-for="(type1, key) in list_work_status_default">
                <td class={{ (Session::get('current_work_location') == "all") ? "bg_light_blue" : ""  }} >@{{type1.name}}</td>
                @can('change_option_info')
                    <td class={{ (Session::get('current_work_location') == "all") ? "bg_light_blue" : ""  }}>
                        @if (Session::get('current_work_location') == "all")
                            <img class="ico_delete" src="{{ asset('images/ico_delete.svg') }}" v-on:click="removeRows(list_work_status_default,key)">
                        @else
                            <label class="radio_text right_30"><input type="radio" value="1" v-model="type1.status">有</label>
                            <label class="radio_text"><input type="radio" value="0" v-model="type1.status">無</label>
                        @endif
                    </td>
                @else
                    @if (Session::get('current_work_location') != "all")
                        <td class={{ (Session::get('current_work_location') == "all") ? "bg_light_blue" : ""  }}>
                            <span>@{{ (type1.status) ? "有" : "無"  }}</span>
                        </td>
                    @endif
                @endcan
            </tr>
            <tr v-for="(work_status_customize, key) in list_work_status_customize">
                <td>@{{work_status_customize.name}}</td>
                @can('change_option_info')
                    <td>
                        @if (Session::get('current_work_location') == "all")
                            <img class="ico_delete" src="{{ asset('images/ico_delete.svg') }}" v-on:click="removeRows(list_work_status_customize,key)">
                        @else
                            <label class="radio_text right_30"><input type="radio" value="1" v-model="work_status_customize.status">有</label>
                            <label class="radio_text"><input type="radio" value="0" v-model="work_status_customize.status">無</label>
                        @endif
                    </td>
                @else
                    @if (Session::get('current_work_location') != "all")
                        <td>
                            <span>@{{ (work_status_customize.status) ? "有" : "無"  }}</span>
                        </td>
                    @endif
                @endcan
            </tr>
        </table>
        @include('option.rest-table')
    </section>
    @can('change_option_info')
        <section class="btn">
            <p class="button right_30"><button type='button' v-on:click="onFormSubmit" class="m_size l_height btn_greeen l_font" >{{ (Session::get('current_work_location') == "all") ? "勤務地に反映" : "保存"  }}</button></p>
            <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('edit_option_work_rest') }}">キャンセル</a></p>
        </section>
    @endcan
</div>