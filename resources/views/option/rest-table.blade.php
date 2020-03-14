<table id="休日形態" v-cloak class="s_70 right">
    <tr>
        @if (Session::get('current_work_location') == "all")
            <th colspan="3">休日形態</th>
            @can('change_option_info')
                <th class="s_10"></th>
            @endcan
        @else
            <th colspan="2">休日形態</th>
            <th class="s_6"></th>
        @endif
    </tr>
    <tr v-for="(type2, key) in list_rest_status_default">
        @if (Session::get('current_work_location') == "all")
            <td class="bg_light_blue data_holiday_available" colspan="3">@{{ type2.name }}</td>
            @can('change_option_info')
                <td class="bg_light_blue">
                    <a><img class="ico_delete" src="{{ asset('images/ico_delete.svg') }}"  v-on:click="removeRows(list_rest_status_default,key)"></a>
                 </td>
            @endcan
        @else
            <td>@{{ type2.name }}</td>
            <td class="s_20"></td>
            <td class="s_20">
                @can('change_option_info')
                    <label class="radio_text right_30"><input type="radio" value="1" v-model="type2.status">有</label>
                    <label class="radio_text"><input type="radio" value="0" v-model="type2.status">無</label>
                @else
                    <span>@{{ (type2.status) ? "有" : "無" }}</span>
                @endcan
            </td>
        @endif
    </tr>
    <tr v-for="(type2, key) in list_rest_status_customize">
        @if (Session::get('current_work_location') == "all")
            <td class="s_20">@{{ type2.name }}</td>
            <td class="s_32">
                @can('change_option_info')
                    <label class="radio_text right_30"><input type="radio" value="1" v-model="type2.unit_type">1日</label>
                    <label class="radio_text"><input type="radio" value="0" v-model="type2.unit_type">時間</label>
                @else
                    <span>@{{ (type2.unit_type) ? "1日" : "時間" }}</span>
                @endcan
            </td>
            <td class="s_32">
                @can('change_option_info')
                    <label class="radio_text right_30"><input type="radio" value="1" v-model="type2.paid_type">有給</label>
                    <label class="radio_text"><input type="radio" value="0" v-model="type2.paid_type">無給</label>
                @else
                    <span>@{{ (type2.paid_type) ? "有給" : "無給" }}</span>
                @endcan
            </td>
            @can('change_option_info')
                <td>
                    <a><img class="ico_delete" src="{{ asset('images/ico_delete.svg') }}"  v-on:click="removeRows(list_rest_status_customize,key)"></a>
                </td>
            @endcan
        @else
            <td class="s_20">@{{ type2.name }}</td>
            <td>
                <span>@{{ (type2.unit_type) ? "1日" : "時間" }}</span>
                <span>@{{ (type2.paid_type) ? "有給" : "無給" }}</span>
            </td>
            <td>
                @can('change_option_info')
                    <label class="radio_text right_30"><input type="radio" value="1" v-model="type2.status">有</label>
                    <label class="radio_text"><input type="radio" value="0" v-model="type2.status">無</label>
                @else
                    <span>@{{ (type2.status) ? "有" : "無" }}</span>
                @endcan
            </td>
        @endif
    </tr>
</table>