<section class="search_box_wrapper">
    <div class="search_box_innner2" {{ (isset($not_display_search_box) || !session('work_address_search_history')['default']) ? 'v-cloak' : '' }} v-show="display">
        <div>
            <form action="" @submit.prevent="">
                <div class="search_setting">
                    <span class="right_10">訪問先ID</span><input class="s_size" v-model="fields[0]" type="text">
                </div>
                <div class="search_setting">
                    <span class="right_10">訪問先名</span>
                    <input type="text" class="vue_place_holder mm_size" v-cloak>
                    <autocomplete :suggestions="placeNames" custom-class="mm_size" :initial-value="fields[1]" :allow-approx="true"
                        @selected="placeNameSelected"
                        @changed="placeNameChanged"
                        @enter-pressed="submit"
                    ></autocomplete>
                </div>
                <div class="search_setting">
                    <span class="right_10">従業員ID</span><input class="s_size" v-model="fields[2]" type="text">
                </div>
                <div class="search_setting">
                    <span class="right_10">従業員名</span>
                    <input type="text" class="vue_place_holder m_size" v-cloak>
                    <autocomplete :suggestions="employeeNames" custom-class="m_size" :initial-value="fields[3]" :allow-approx="true"
                        @selected="employeeNameSelected"
                        @changed="employeeNameChanged"
                        @enter-pressed="submit"
                    ></autocomplete>
                </div>
                <div class="search_box search_setting">
                    <span class="right_10">状態</span><div class="selectbox s_size side_input_block">
                    <select class="s_size" v-model.number="fields[4]">
                        <option value="1">有効</option>
                        <option value="0">無効</option>
                    </select>
                    </div>
                </div>
                <div class="search_setting">
                    <span class="right_10">住所</span>
                    <input type="text" class="vue_place_holder mm_size" v-cloak>
                    <autocomplete :suggestions="placeAddresses" custom-class="mm_size" :initial-value="fields[5]" :allow-approx="true"
                        @selected="placeAddressSelected"
                        @changed="placeAddressChanged"
                        @enter-pressed="submit"
                    ></autocomplete>
                </div>
                <div class="search_setting">
                    <span class="right_10">許容範囲</span><input class="ss_size" v-model="fields[6]" type="text"><span class="select_both_space">m</span><span class=" right_10">〜</span><input class="ss_size right_10" v-model="fields[7]" type="text"><span>m</span>
                </div>
                <div class="button bottom_10 search_setting right_10">
                    <button class="s_size s_height btn_greeen" @click="submit">検索</button>
                </div>
                <div class="button bottom_10 search_setting">
                    <a class="s_size s_height btn_gray" @click="resetConditions">リセット</a>
                </div>
            </form>
        </div>
    </div>
    <section class="search_result" {{ session('work_address_search_history')['default'] ? 'v-cloak' : '' }} v-if="displayHistory">
        <section class="resule_innner">
            <span class="red right_30">検索結 {{ session('work_address_search_history')['count'] }}件</span>
            @foreach(session('work_address_search_history')['result_text'] as $condition => $value)
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