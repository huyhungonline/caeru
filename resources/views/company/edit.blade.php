@extends('layouts.master')

@section('title', '会社情報')

@section('header')
    @include('layouts.header', [ 'active' => 2 ])
@endsection

@section('content')
    <main id="basic">
        <section class="title">
            <p class="breadcrumb"><span>基本情報</span><span>&emsp;&#62;&emsp;会社情報入力</span></p>
            <div class="title_wrapper">
                <h1>会社情報入力</h1>
            </div>
        </section>
        <form method="POST" form-single-submit action="{{ Caeru::route('update_company') }}">
            {{ csrf_field() }}
            <section class="setting_table">
                <table>
                    <tr>
                        <td class="input_items">@can('change_company_info')<span class="required">必須</span>@endcan 会社名</td>
                        <td>
                            @can('change_company_info')
                                @component('layouts.form.error', ['field' => 'name'])
                                    <input class="l_size" name="name" value="{{ old('name',$company->name) }}" type="text">
                                @endcomponent
                            @else
                                {{ $company->name }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">@can('change_company_info')<span class="required">必須</span>@endcan フリガナ</td>
                        <td>
                            @can('change_company_info')
                                @component('layouts.form.error', ['field' => 'furigana'])
                                    <input class="l_size" name="furigana" value="{{ old('furigana',$company->furigana) }}" type="text">
                                @endcomponent
                            @else
                                {{ $company->furigana }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">郵便番号</td>
                        <td>
                            @can('change_company_info')
                                @include('layouts.form.2_cells_field', ['field' => 'postal_code', 'object' => $company])
                            @else
                                {{ $company->postal_code_1 ? ($company->postal_code_1 . '-' . $company->postal_code_2) : '' }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">住所</td>
                        <td>
                            @can('change_company_info')
                                <section>
                                    <div class="selectbox right_10 left">
                                        @component('layouts.form.error', ['field' => 'todofuken'])
                                            @include('layouts.form.nullable_select_field', ['field' => 'todofuken', 'class' => 's_size', 'default' => $company->todofuken, 'items' => $todofuken_list, 'multiple' => false])
                                        @endcomponent
                                    </div>
                                    <div class="left">
                                        @component('layouts.form.error', ['field' => 'address'])
                                            <input class="l_size left right_30" name="address" value="{{ old('address',$company->address) }}" type="text">
                                        @endcomponent
                                    </div>
                                </section>
                            @else
                                {{ ($company->todofuken ? $todofuken_list[$company->todofuken] : '') . $company->address }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">電話番号</td>
                        <td>
                            @can('change_company_info')
                                @include('layouts.form.3_cells_field', ['field' => 'telephone', 'object' => $company])
                            @else
                                {{ $company->telephone_1 ? ($company->telephone_1 . '-' . $company->telephone_2 . '-' . $company->telephone_3) : '' }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">FAX番号</td>
                        <td>
                            @can('change_company_info')
                                @include('layouts.form.3_cells_field', ['field' => 'fax', 'object' => $company])
                            @else
                                {{ $company->fax_1 ? ($company->fax_1 . '-' . $company->fax_2 . '-' . $company->fax_3) : '' }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">総責任者名</td>
                        <td>
                            @can('change_company_info')
                                @include('layouts.form.name_field', ['additional' => 'ceo_', 'object' => $company, 'kana' => false])
                            @else
                                {{ $company->ceo_last_name . $company->ceo_first_name }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">総責任者名(カナ)</td>
                        <td>
                            @can('change_company_info')
                                @include('layouts.form.name_field', ['additional' => 'ceo_', 'object' => $company, 'kana' => true])
                            @else
                                {{ $company->ceo_last_name_furigana . $company->ceo_first_name_furigana }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">総責任者メールアドレス</td>
                        <td>
                            @can('change_company_info')
                                @component('layouts.form.error', ['field' => 'ceo_email'])
                                    <input class="l_size" name="ceo_email" value="{{ old('ceo_email',$company->ceo_email) }}" type="text">
                                @endcomponent
                            @else
                                {{ $company->ceo_email }}
                            @endcan
                        </td>
                    </tr>
                </table>
            </section>
            <section class="setting_table secound_block">
                <h2>請求先情報</h2>
                <table>
                    <tr>
                        <td class="input_items">担当者名</td>
                        <td>
                            @can('change_company_info')
                                @include('layouts.form.name_field', ['additional' => 'billing_person_', 'object' => $company, 'kana' => false])
                            @else
                                {{ $company->billing_person_last_name . $company->billing_person_first_name }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">担当者名(カナ)</td>
                        <td>
                            @can('change_company_info')
                                @include('layouts.form.name_field', ['additional' => 'billing_person_', 'object' => $company, 'kana' => true])
                            @else
                                {{ $company->billing_person_last_name_furigana . $company->billing_person_first_name_furigana }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">メールアドレス</td>
                        <td>
                            @can('change_company_info')
                                @component('layouts.form.error', ['field' => 'billing_person_email'])
                                    <input class="l_size" name="billing_person_email" value="{{ old('billing_person_email',$company->billing_person_email) }}" type="text">
                                @endcomponent
                            @else
                                {{ $company->billing_person_email }}
                            @endcan
                        </td>
                    </tr>
                </table>
            </section>
            <section class="setting_table secound_block">
                <h2>設定</h2>
                <table>
                    <tr>
                        <td class="input_items">@can('change_company_info')<span class="required">必須</span>@endcan 営業日仕分け</td>
                        <td>
                            @can('change_company_info')
                                <div class="left right_10">
                                    <span class="right_10">営業日分岐時刻</span>
                                    @component('layouts.form.error', ['field' => 'date_separate_time'])
                                        <input class="s_size" name="date_separate_time" value="{{ old('date_separate_time',$company->date_separate_time) }}" type="text">
                                    @endcomponent
                                </div>
                                <div class="selectbox">
                                    @component('layouts.form.error', ['field' => 'date_separate_type'])
                                        @include('layouts.form.select_field', ['field' => 'date_separate_type', 'class' => 'mm_size', 'default' => $company->date_separate_type, 'items' => $date_separate_types, 'multiple' => false])
                                    @endcomponent
                                </div>
                            @else
                                {{ $company->date_separate_time . $date_separate_types[$company->date_separate_type] }}
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td class="input_items">訪問先</td>
                        <td>
                            @can('change_company_info')
                                @include('layouts.form.radio_field', ['field' => 'use_address_system', 'object' => $company, 'options' => array( 1 => '利用する', 0 => '利用しない'), 'default' => 1])
                            @else
                                {{ $company->use_address_system ? '利用する' : '利用しない' }}
                            @endcan
                        </td>
                    </tr>
                </table>
            </section>
            <section class="btn">
                @can('change_company_info')
                    <p class="button right_30"><button class="m_size l_height btn_greeen l_font" type="submit">保存</button></p>
                    <p class="button"><a class="m_size l_height btn_gray l_font" href="{{ Caeru::route('edit_company') }}">キャンセル</a></p>
                @endcan
            </section>
        </form>
    </main>
@endsection