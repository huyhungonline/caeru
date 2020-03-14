@extends('layouts.master')

@section('title', 'ログイン')

@push('scripts')
    <script defer src="{{ asset('/js/mics.js') }}" defer></script>
    <script defer src="{{ asset('/js/components/alert_module.js') }}" defer></script>
@endpush

@section('content')
    <section id="header_wrapper">
        <header>
            <section class="alert_wrapper">
                <section class="alert_innner">
                    <section class="alert_box save_green"><span class="alert_ico right_10"><img src="{{ asset('images/ico_alert_save.svg') }}"></span>
                        @if (session()->has('success'))
                            <span class="alert_content">{{ session('success') }}</span>
                        @endif
                    </section>
                    <section class="alert_box save_yellow"><span class="alert_ico right_10"><img src="{{ asset('images/ico_alert_dakoku.svg') }}"></span>
                        @if (session()->has('warning'))
                            <span class="alert_content">{{ session('warning') }}</span>
                        @endif
                    </section>
                    <section class="alert_box save_red"><span class="alert_ico right_10"><img src="{{ asset('images/ico_alert_error.svg') }}"></span>
                        @if (count($errors) || session()->has('error'))
                            <span class="alert_content">{{ session()->has('error') ? session('error') : '入力に誤りがあります' }}</span>
                        @endif
                    </section>
                </section>
            </section>
        </header>
    </section>
    <section id ="login_wrapper">
        <form method="POST" form-single-submit action="{{ Caeru::route('login') }}">
            {{ csrf_field() }}
            <section class="login_logo bottom_20"><img src="{{ asset('images/logo.svg') }}"></section>
            @if ($errors->has('auth_failed'))
                <div>
                    <span class="auth_failed_message">{{ $errors->first('auth_failed') }}</span>
                </div>
            @endif
            <div>
                <section class="input_panel_row">
                    <span class="s_size left">管理者ID</span>
                    @component('layouts.form.error', ['field' => 'presentation_id'])
                        <input type="text" name="presentation_id" class="m_size left" value="{{ old('presentation_id') }}">
                    @endcomponent
                </section>
                <section class="input_panel_row">
                    <span class="s_size left">パスワード</span>
                    @component('layouts.form.error', ['field' => 'password'])
                        <input type="password" name="password" class="m_size">
                    @endcomponent
                </section>
            </div>
            <section class ="btn">
                <p class="button">
                    <button type="submit" class ="mm_size l_height btn_greeen l_font" >ログイン</button>
                </p>
            </section>
        </form>
    </section>
    <script type="text/javascript" >window.Laravel = { csrfToken: '{{ csrf_token() }}' };</script>
@endsection