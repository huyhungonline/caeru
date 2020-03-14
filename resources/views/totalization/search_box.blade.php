<section class="totalization-search">   
    <section class="select_one bg_light_green bottom_10">
        <section class="select_one_inner">
            <form action="">
                <section @click="preMonth" class="right_30 ico_position">
                    <img class="ico_ico_arrow" src="{{ asset('images/ico_arrow_left1.svg') }}">
                </section>
                <div class="year-change selectbox side_input_block right_10">
                   <select v-model="yearSelected" class="s_size" @change="yearChanged">
                       @for($i=5; $i>=1; $i--)
                       <option value="{{ $currentYear-$i }}">{{ $currentYear-$i }}年</option>
                       @endfor
                       <option value="{{ $currentYear }}">{{ $currentYear }}年</option>
                       @for($i=1;$i<=5; $i++)
                       <option value="{{ $currentYear+$i }}">{{ $currentYear+$i }}年</option>
                       @endfor
                   </select>
                </div>
               
                <div class="month-change selectbox side_input_block right_10">
                    <select v-model="monthSelected" class="s_size" @changed="monthChanged">
                        @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}"{{ $i==$currentMonth?"selected='selected'":""}}>{{ $i }}月度</option>
                        @endfor
                    </select>
                </div>
                <p class="term right_30" v-cloak>
                    <span>@{{ beginYear }}年</span>
                    <span>@{{ beginMonth }}月</span>
                    {{-- <span>@{{ beginDay }}日</span> --}}
                    <span class="right_10 left_10">〜</span>
                    <span>@{{ endYear }}年</span>
                    <span>@{{ endMonth }}月</span>
                    {{-- <span>@{{ endDay }}日</span> --}}
                </p>
                <section @click ="nextMonth" class="ico_position">
                    <img class="ico_ico_arrow" src="{{ asset('images/ico_arrow_right1.svg') }}">
                </section>
            </form>
        </section>
    </section>
    <section class="search_box_wrapper">
        <transition name="slide-down" v-if="display">
            <div class="search_box_innner1 vue" v-cloak v-show="display">
                <form action="" @submit.prevent="">
                    <div class="search_box_innner2">
                        <div>
                            <div class="search_setting">
                                <span class="right_10">従業員ID</span><input class="s_size" v-model ="employeeId" name="employeeId" type="text" value="">
                            </div>
                            <div class="search_setting">
                                <span class="right_10">従業員名</span>
                                <input type="text" v-model="employeeName" class="mm_size">
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="101"  v-model="checkedArr">申請中</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="102" v-model="checkedArr">承認済</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="103" v-model="checkedArr">打刻エラー</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="201" v-model="checkedArr">判定エラー</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="202" v-model="checkedArr">締め未対応</label>
                            </div>
                            <div class="button bottom_10 search_setting right_10">
                                <button class="s_size s_height btn_greeen">検索</button>
                            </div>
                            <div class="button bottom_10 search_setting">
                                <a class="s_size s_height btn_gray">リセット</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </transition>
        <section class="search_result">
            <section class="resule_innner">
                <span class="text_bold right_10">検索結果:</span>
                <span class="red right_10">0</span>
                <span class="red right_10">件</span>
                <span> 
                    <span class="right_10 text_bold">従業員ID:</span>
                    <span class="right_10">10101</span>
                </span>
                <span>
                    <span class="right_10 text_bold">従業員名:</span>
                    <span class="right_10">山内</span>
                </span>
                 <span>
                    <span class="right_10">申請中 </span>
                </span>
                 <span>
                    <span class="right_10">承認済 </span>
                </span>
                 <span>
                    <span class="right_10">打刻エラー </span>
                </span>
                 <span>
                    <span class="right_10">判定エラー </span>
                </span>
                 <span>
                    <span class="right_10">締め未対応 </span>
                </span>
            </section>
            <section>
                <p class="button right"><a class="m_size m_height btn_greeen">検索条件の変更</a></p>
            </section>
        </section>
    </section>
</section>