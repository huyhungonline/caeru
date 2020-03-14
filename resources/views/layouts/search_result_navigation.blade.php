<section class="select_one bg_light_green bottom_10">
    <section class="select_one_inner">
        @if (isset($search_navi_previous))
            <section class="right_30 ico_position">
                <a href="{{ $search_navi_previous }}"><img class="ico_ico_arrow" src="{{ asset('images/ico_arrow_left1.svg') }}"></a>
            </section>
        @endif
        <section class="right_30 text_bold">
            <span class="right_30">{{ $search_navi_presentation_id }}</span><span>{{ $search_navi_name }}</span>
        </section>
        @if (isset($search_navi_next))
            <section class="ico_position">
                <a href="{{ $search_navi_next }}"><img class="ico_ico_arrow" src="{{ asset('images/ico_arrow_right1.svg') }}"></a>
            </section>
        @endif
    </section>
</section>