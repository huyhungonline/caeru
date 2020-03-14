@component('layouts.form.error', ['field' => $field . '_1'])
    <input class="s_size right_10" name="{{ $field }}_1" value="{{ old( $field . '_1', isset($object) ? $object->getAttribute( $field . '_1' ) : '') }}" type="text"><span class="right_10">&#045;</span>
@endcomponent
@component('layouts.form.error', ['field' => $field . '_2'])
    <input class="s_size right_10" name="{{ $field }}_2" value="{{ old( $field . '_2', isset($object) ? $object->getAttribute( $field . '_2' ) : '') }}" type="text"><span class="right_10">&#045;</span>
@endcomponent
@component('layouts.form.error', ['field' => $field . '_3'])
    <input class="s_size right_10" name="{{ $field }}_3" value="{{ old( $field . '_3', isset($object) ? $object->getAttribute( $field . '_3' ) : '') }}" type="text">
@endcomponent