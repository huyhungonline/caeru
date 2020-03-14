<section class="check_table">
    <section class="s_49 left">
        <p class="ll_font titile_wrap">打刻エラー</p>
        <table _fixedhead='rows:1; cols:2;'>
            <tr>
                <th class="s_18"></th>
                <th>日付</th>
                <th class="s_18">従業員ID</th>
                <th class="s_22">名前</th>
                <th class="s_14"></th>
            </tr>
            <tr v-for="checklist in checklists.checklists_timestamp_error" v-cloak>
                <td>
                    <span v-if="checklist.error_type==101">出勤</span>
                    <span v-if="checklist.error_type==102">退勤</span>
                    <span v-if="checklist.error_type==103">外出.戻り</span>
                    <span v-if="checklist.error_type==201">遅刻.早退</span>
                    <span v-if="checklist.error_type==202">時間外</span>
                    <span v-if="checklist.error_type==203">形態</span>
                    <span v-if="checklist.error_type==204">休憩・外出</span>
                    <span v-if="checklist.error_type==205">休出</span>
                    <span v-else="checklist.error_type==">欠勤</span>
                </td>
                <td>
                    {{-- <span>@{{ checklist.date | formatDate }}</span> --}}
                    <span>@{{ checklist.date }}</span>
                </td>
                <td>@{{ checklist.presentation_id }}</td>
                <td>@{{ checklist.first_name + checklist.last_name }}</td>
                <td><p class="button"><a class="ss_size s_height btn_gray" href="#">変更</a></p></td>
            </tr>
        </table>
    </section>
    <section class="s_49 right">
        <p class="ll_font titile_wrap">要チェックリスト</p>
        <table _fixedhead='rows:1; cols:2;'>
            <tr>
                <th class="s_22"></th>
                <th>日付</th>
                <th class="s_18">従業員ID</th>
                <th class="s_22">名前</th>
                <th class="s_14"></th>
            </tr>
            <tr v-for="checklist in checklists.checklists_confirm_needed" v-cloak>
                <td>
                    <span v-if="checklist.error_type==101">出勤</span>
                    <span v-if="checklist.error_type==102">退勤</span>
                    <span v-if="checklist.error_type==103">外出.戻り</span>
                    <span v-if="checklist.error_type==201">遅刻.早退</span>
                    <span v-if="checklist.error_type==202">時間外</span>
                    <span v-if="checklist.error_type==203">形態</span>
                    <span v-if="checklist.error_type==204">休憩・外出</span>
                    <span v-if="checklist.error_type==205">休出</span>
                    <span v-else="checklist.error_type==">欠勤</span>
                </td>
                <td>@{{ checklist.date | formatDate }}</td>
                <td>@{{ checklist.presentation_id }}</td>
                <td>@{{ checklist.first_name + checklist.last_name }}</td>
                 <td><p class="button"><a class="ss_size s_height btn_gray" href="#">変更</a></p></td>
            </tr>
        </table>
    </section>
</section>
<script defer type="text/javascript">
    window.checklists = {!! $checklistsJson !!};
    window.checklistsHistory = {!! $checklistsHistory !!};
    window.checklistsDisplayHistory = {!! $displayHistory !!}
</script>
