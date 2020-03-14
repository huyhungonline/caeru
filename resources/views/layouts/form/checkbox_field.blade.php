<div class="input_wrapper {{ $errors->has($field) ? 'error':'' }} checkboxes">
    <label class="checkbox_text"><input name="{{ $field }}" type="checkbox" value="1" {{ old($field, isset($object) ? $object->getAttribute($field) : $default) ? 'checked' : '' }}>{{ $label }}</label>
    @if ($errors->has($field))
        <div class='error_wrapper'>
            <span class="tool_error">{{ $errors->first($field) }}</span>
        </div>
    @endif
</div>