<section class="search_box_wrapper">
    @if (isset($show_toggle_button))
        @if (session('employee_search_history')['default'] == true)
            <div class="head">
                <span class="m_size m_height btn_details" @click="toggle">詳細検索</span>
            </div>
        @else
            <div class="head" v-cloak v-if="displayToggleButton">
                <span class="m_size m_height btn_details" @click="toggle">詳細検索</span>
            </div>
        @endif
    @endif
    <transition name="slide-down">
        <div class="search_box_innner vue" v-cloak v-show="display">
            <form action="" @submit.prevent="">
                <div>
                    <div  class="reset_positon">
                    <h2 class="line_green">基本情報</h2>
                    <p class="button"><a class="m_size s_height btn_gray" @click="resetConditions">リセット</a></p>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">従業員ID</span><input class="s_size" v-model="fields[0]" type="text">
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">従業員名</span>
                        <autocomplete :suggestions="employeeNames" custom-class="m_size" :initial-value="fields[1]" :allow-approx="true"
                            @selected="employeeNameSelected"
                            @changed="employeeNameChanged"
                            @enter-pressed="submit"
                        ></autocomplete>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10 left">就労形態</span>
                        <div class="selectbox m_size left">
                            <select class="m_size" v-model="fields[2]">
                                <option selected="selected" value=""></option>
                                @foreach ($schedule_types as $index => $type)
                                    <option value="{{ $index }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10 left">採用形態</span>
                        <div class="selectbox m_size left">
                            <select class="m_size" v-model="fields[3]">
                                <option selected="selected" value=""></option>
                                @foreach ($employment_types as $index => $type)
                                    <option value="{{ $index }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">部署</span>
                        <div class="form-group mm_size">
                            <select class="mm_size ms" v-model="fields[4]" autocomplete="off" multiple="multiple">
                                @foreach ($departments as $index => $type)
                                    <option value="{{ $index }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10 left">雇用状態</span>
                        <div class="selectbox s_size left">
                            <select class="s_size" v-model.number="fields[5]">
                                <option value=""></option>
                                @foreach ($work_statuses as $index => $type)
                                    <option value="{{ $index }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10 left">給与形態</span>
                        <div class="selectbox s_size left">
                            <select class="s_size" v-model="fields[6]">
                                <option selected="selected" value=""></option>
                                @foreach ($salary_types as $index => $type)
                                    <option value="{{ $index }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">住所</span>
                        <input class="l_size" v-model="fields[7]" type="text">
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10 left">性別</span>
                        <div class="selectbox left">
                            <select class="s_size" v-model="fields[8]">
                                <option selected="selected" value=""></option>
                                @foreach ($genders as $index => $type)
                                    <option value="{{ $index }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">生年月日</span>
                        <input class="m_size" v-model="fields[9]" type="text">
                        <span class=" right_10">〜</span>
                        <input class="m_size" v-model="fields[10]" type="text">
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">入社年月日</span>
                        <input class="ss_size" v-model.number="fields[11]" type="text"><span class="select_both_space">年</span>
                        <input class="ss_size" v-model.number="fields[12]" type="text"><span class="select_both_space">月</span>
                        <input class="ss_size right_10" v-model.number="fields[13]" type="text"><span class="right_10">日</span>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">退職日</span>
                        <input class="ss_size" v-model.number="fields[14]" type="text"><span class="select_both_space">年</span>
                        <input class="ss_size" v-model.number="fields[15]" type="text"><span class="select_both_space">月</span>
                        <input class="ss_size right_10" v-model.number="fields[16]" type="text"><span class="right_10">日</span>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10 left">承認対象者</span>
                        <div class="selectbox left">
                            <select class="s_size" v-model="fields[17]">
                                <option selected="selected" value=""></option>
                                <option value="1">有</option>
                                <option value="0">無</option>
                            </select>
                        </div>
                    </div>
                    <h2 class="line_yellow">勤怠管理</h2>
                    <div class="search_box search_setting">
                        <span class="right_10 left">ICカード登録</span>
                        <div class="selectbox left">
                            <select class="s_size" v-model="fields[18]">
                                <option selected="selected" value=""></option>
                                <option value="1">有</option>
                                <option value="0">無</option>
                            </select>
                        </div>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">勤務地</span>
                        <select class="m_size" v-model="fields[19]">
                            <option selected="selected" value=""></option>
                            @foreach ($work_locations as $index => $type)
                                <option value="{{ $index }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="search_box search_setting">
                        <span class="right_10">訪問先</span>
                        <autocomplete :suggestions="workAddresses" custom-class="mm_size" :initial-value="fields[20]" :allow-approx="true"
                            @selected="workAddressSelected"
                            @changed="workAddressChanged"
                            @enter-pressed="submit"
                        ></autocomplete>
                    </div>
                </div>
                <section class="btn">
                    <p class="button right_30"><button class="m_size m_height btn_greeen" @click="submit">検索</button></p>
                </section>
            </form>
        </div>
    </transition>
    <section class="search_result vue" {{ session('employee_search_history')['default'] ? 'v-cloak' : '' }} v-if="displayHistory">
        <section class="resule_innner">
            <span class="red right_30 ">検索結果 {{ session('employee_search_history')['count'] }}件</span>
            @foreach(session('employee_search_history')['result_text'] as $condition => $value)
                @if (is_array($value))
                    <span class="right_10 text_bold">{{ $condition }}</span>
                    @foreach($value as $item)
                        <span class="right_30">{{ $item }}</span>
                    @endforeach
                @else
                    <span class="right_10 text_bold">{{ $condition }}</span><span class="right_30">{{ $value }}</span>
                @endif
            @endforeach
        </section>
        <section>
            <p class="button right"><a class="m_size m_height btn_greeen" @click=changeConditions>検索条件の変更</a></p>
        </section>
    </section>
</section>