<select class="{{ $class }}" autocomplete="off" name="{{ $multiple ? $field . '[]': $field }}" {{ $multiple ? "multiple='multiple'" : '' }}>
    @foreach($items as $value => $name)
        @if (!$multiple)
            @if (old($field,$default) == $value)
                <option value="{{ $value }}" selected>{{ $name }}</option>
            @else
                <option value="{{ $value }}" >{{ $name }}</option>
            @endif
        @else
            @if (old($field, $default) && in_array($value, old($field, $default)))
                <option value="{{ $value }}" selected>{{ $name }}</option>
            @else
                <option value="{{ $value }}" >{{ $name }}</option>
            @endif
        @endif
    @endforeach
</select>