<section class="checklist-search">   
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
                        @for($i=1; $i<=5; $i++)
                        <option value="{{ $currentYear+$i }}">{{ $currentYear+$i }}年</option>
                        @endfor
                    </select>
                </div>
               
                <div class="month-change selectbox side_input_block right_10">
                    <select v-model="monthSelected" class="s_size" @change="monthChanged">
                        @for($i= 1; $i<=12; $i++)
                        <option value="{{ $i }}"{{ $i==$currentMonth?" selected='selected'":""}}>{{ $i }}月度</option>
                        @endfor
                    </select>
                </div>
                <p class="term right_30" v-cloak>
                    <span>@{{ beginYear }}年</span>
                    <span>@{{ beginMonth }}月</span>
                    <span>@{{ beginDay }}日</span>
                   {{--  <span>@{{ showBeginDayOfWeek }}</span> --}}
                    <span class="right_10 left_10">〜</span>
                    <span>@{{ endYear }}年</span>
                    <span>@{{ endMonth }}月</span>
                    <span>@{{ endDay }}日</span>
                   {{--  <span>@{{ showEndDayOfWeek }}</span> --}}

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
                                {{-- <autocamplete :suggestions = "employeeName" custom-class="m_size" :initial-value="fields[1]" :allow-approx ="true" @selected="employeeNameSelected" @changed="employeeNameChange"  @enter-pressed="submit">
                                </autocomplete> --}}
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="101"  v-model="checkedEr">出勤エラー</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="102" v-model="checkedEr">退勤エラー</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="103" v-model="checkedEr">外出・戻りエラー</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="201" v-model="checkedEr">遅刻・早退</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="202" v-model="checkedEr">時間外</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="203" v-model="checkedEr">形態</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="204" v-model="checkedEr">休出</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="205" v-model="checkedEr">欠勤</label>
                            </div>
                            <div class="check_box_wrap search_setting">
                                <label class="checkbox_text"><input class="left" type="checkbox" value="206" v-model="checkedEr">休憩・外出</label>
                            </div>
                            <div class="button bottom_10 search_setting right_10">
                                <button class="s_size s_height btn_greeen" @click="submit">検索</button>
                            </div>
                            <div class="button bottom_10 search_setting">{{-- 
                                <a class="s_size s_height btn_gray" @click="resetConditions">リセット</a> --}}
                                <a class="s_size s_height btn_gray" @click="resetConditions">リセット</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </transition>
        <section class="search_result vue" v-else v-cloak>
            <section class="resule_innner">
                <span class="text_bold right_10">打刻エラー:</span>
                <span class="red right_10">@{{ totaldakoku }}</span>
                <span class="red right_10">件</span>
                <span class="text_bold right_10">要チェックリスト:</span>
                <span class="red right_10">@{{ totalhyou }}</span>
                <span class="red right_10">件</span>
                <span v-show="employeeId != ''"> 
                    <span class="right_10 text_bold">従業員ID:</span>
                    <span class="right_10">@{{ employeeId }}</span>
                </span>
                <span v-show="employeeName != ''">
                    <span class="right_10 text_bold">従業員名:</span>
                    <span class="right_10">@{{ employeeName }}</span>
                </span>
                <span v-show="checkedEr.indexOf('101')>=0">
                    <span class="right_10">出勤エラー </span>
                </span>
                <span v-show="checkedEr.indexOf('102')>=0">
                    <span class="right_10">退勤エラー </span>
                </span>
                <span v-show="checkedEr.indexOf('103')>=0">
                    <span class="right_10">外出・戻りエラー</span>
                </span>
                <span v-show="checkedEr.indexOf('201')>=0">
                    <span class="right_10">遅刻・早退 </span>
                </span>
                <span v-show="checkedEr.indexOf('202')>=0">
                    <span class="right_10">時間外 </span>
                </span>
                <span v-show="checkedEr.indexOf('203')>=0">
                    <span class="right_10">形態 </span>
                </span>
                <span v-show="checkedEr.indexOf('204')>=0">
                    <span class="right_10">休出</span>
                </span>
                <span v-show="checkedEr.indexOf('205')>=0">
                    <span class="right_10">欠勤</span>
                </span>
                <span v-show="checkedEr.indexOf('206')>=0">
                    <span class="right_10">休憩・外出</span>
                </span>
            </section>
            <section>
                <p class="button right"><a class="m_size m_height btn_greeen" @click=changConditions>検索条件の変更</a></p>
            </section>
        </section>
    </section>
</section>