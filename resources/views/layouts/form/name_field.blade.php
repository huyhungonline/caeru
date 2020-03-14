<span class="right_10">{{ $kana ? 'セイ':'姓' }}</span>
@component('layouts.form.error', ['field' => $additional . 'last_name' . ($kana ? '_furigana':'')])
    <input class="m_size right_30" name="{{ $additional }}last_name{{ $kana ? '_furigana':'' }}" value="{{ old( $additional . 'last_name' . ($kana ? '_furigana':''), isset($object) ? $object->getAttribute($additional . 'last_name' . ($kana ? '_furigana':'')) : '') }}" type="text">
@endcomponent
<span class="right_10">{{ $kana ? 'メイ':'名' }}</span>
@component('layouts.form.error', ['field' => $additional . 'first_name' . ($kana ? '_furigana':'')])
    <input class="m_size right_10" name="{{ $additional }}first_name{{ $kana ? '_furigana':'' }}" value="{{ old( $additional . 'first_name' . ($kana ? '_furigana':''), isset($object) ? $object->getAttribute($additional . 'first_name' . ($kana ? '_furigana':'')) : '') }}" type="text">
@endcomponent