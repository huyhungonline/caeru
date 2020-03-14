<div id="form-second">
    @can('change_department_info')
        @if (Session::get('current_work_location') == "all")
            <section class="add_item">
                    <div class="search_box right_30 left">
                        <span class="right_10">部署追加</span>
                        <input v-model="name_option" class="m_size" name="" @keydown.enter.prevent="addRows" type="text">
                    </div>
                    <p class="button left"><button class="s_size s_height btn_gray" v-on:click="addRows">追加</button></p>
            </section>
        @endif
    @endcan
    <section class="default_table">
        <table v-cloak>
            <tr>
                <th>部署</th>
                @if (Session::get('current_work_location') == "all")
                    @can('change_department_info')
                        <th class="s_8"></th>
                    @endcan
                @else
                    <th class="s_20"></th>
                @endif
            </tr>
            <tr v-for="(type3, key) in list_department_status">
                <td>@{{ type3.name }}</td>
                @can('change_department_info')
                    <td>
                        @if (Session::get('current_work_location') == "all")
                            <a><img class="ico_delete" src="{{ asset('images/ico_delete.svg') }}" v-on:click="removeRows(list_department_status,key)"></a>
                        @else
                            <label class="radio_text right_30"><input type="radio" value="1" v-model="type3.status">有</label>
                            <label class="radio_text"><input type="radio" value="0" v-model="type3.status">無</label>
                        @endif
                    </td>
                @else
                    @if (Session::get('current_work_location') != "all")
                        <td><span>@{{ (type3.status) ? "有" : "無" }}</span></td>
                    @endif
                @endcan
            </tr>
        </table>
    </section>
    @can('change_department_info')
        <section class="btn">
            <p class="button right_30"><button type='button' v-on:click="onFormSubmit" class="m_size l_height btn_greeen l_font">{{ (Session::get('current_work_location') == "all") ? "勤務地に反映" : "保存"  }}</button></p>
            <p class="button right_30"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('edit_option_department') }}">キャンセル</a></p>
        </section>
    @endcan
</div>