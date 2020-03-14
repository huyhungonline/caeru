@component('layouts.form.error', ['field' => $field])
    <select class="{{ $class }}" name="{{ $field }}">
        <option value=""></option>
        @foreach($items as $value => $name)
            @if (old($field,$default) == $value)
                <option value="{{ $value }}" selected>{{ $name }}</option>
            @else
                <option value="{{ $value }}" >{{ $name }}</option>
            @endif
        @endforeach
    </select>
@endcomponent