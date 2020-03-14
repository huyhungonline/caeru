<div class="input_wrapper {{ $errors->has($field) ? 'error':'' }}">
    {{ $slot }}
    @if ($errors->has($field))
        <div class='error_wrapper'>
            <span class="tool_error">{{ $errors->first($field) }}</span>
        </div>
    @endif
</div>