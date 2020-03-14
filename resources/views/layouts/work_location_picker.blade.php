<transition name="fade">
    <div class="work_location_picker_wrapper" v-cloak v-show="display">
        <div class="work_location_picker">
            <section class="select_one">
                <section class="select_one_innner">
                    <h2>勤務地変更</h2>
                    <section class="right_position">
                        <p class="button left_10"><a class="mm_size s_height btn_gray modal-open" data-target="con1" @click="toggleDisable">無効な勤務地も表示</a></p>
                    </section>
                </section>
            </section>
            @if (isset($list))
                <section class="table">
                    <table>
                        <tr>
                            <th class="s_5">勤務地ID</th>
                            <th class="s_8">勤務地</th>
                            <th class="s_5">都道府県</th>
                            <th class="s_5">従業員数</th>
                            <th class="s_5"></th>
                        </tr>
                        @if (isset($list['company']))
                            <tr>
                                <td>{{ $list['company']['presentation_id'] }}</td>
                                <td>{{ $list['company']['name'] }}</td>
                                <td>{{ $list['company']['todofuken'] }}</td>
                                <td>{{ $list['company']['employees_count'] }}</td>
                                <td>
                                    <p class="button"><a class="ss_size s_height btn_gray" single-click href="{{ Caeru::route('choose', ['chosen' => 'all', 'target' => $target]) }}">選択</a></p>
                                </td>
                            </tr>
                        @elseif (isset($list['multi']) && !$singular)
                            <tr>
                                <td>{{ $list['multi']['presentation_id'] }}</td>
                                <td>{{ $list['multi']['name'] }}</td>
                                <td>{{ $list['multi']['todofuken'] }}</td>
                                <td>{{ $list['multi']['employees_count'] }}</td>
                                <td>
                                    <p class="button"><a class="ss_size s_height btn_gray" single-click href="{{ Caeru::route('choose', ['chosen' => 'all', 'target' => $target]) }}">選択</a></p>
                                </td>
                            </tr>
                        @endif
                        @foreach ($list['work_locations'] as $work_location)
                            @if (!$work_location->enable)
                                <tr class="light_gray" v-show="displayDisabled">
                            @else
                                <tr>
                            @endif
                                    <td>{{ $work_location->presentation_id }}</td>
                                    <td>{{ $work_location->name }}</td>
                                    <td>{{ $work_location->todofuken() }}</td>
                                    <td>{{ $work_location->employees_count }}</td>
                                    <td>
                                        @if (isset($restricted) && ($restricted == true))
                                        @else
                                            <p class="button"><a class="ss_size s_height btn_gray" single-click href="{{ Caeru::route('choose', ['chosen' => $work_location->id, 'target' => $target]) }}">選択</a></p>
                                        @endif
                                    </td>
                                </tr>
                        @endforeach
                    </table>
                </section>
            @endif
            <section class="btn">
                <p class="button"><a class="modal-close m_size l_height btn_gray l_font" @click="close">キャンセル</a></p>
            </section>
        </div>
        <div class="modal-overlay" @click="close"></div>
    </div>
</transition>