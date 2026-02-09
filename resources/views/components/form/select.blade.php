@props([
        'label' => '',
    'placeholder' => '',
    'name' => '',
    'required' => '',
    'value' => '',
    'class' => '',
    'event' => '',
    'datas' => [],
    'option' => '',
    'all' => '',
    'select2' => '1'
    ])
@if ($label)
    <div class="form-group mb-5">
        <label class="form-label {!! $required ? 'required' : '' !!}">{{ $label }}</label>
        <select @if ($event) onchange="{{ $event }}" @endif
            @php
                if($select2 == '1'){
                    $classSelect = ' form-select ';
                }else{
                    $classSelect = '';
                }
            @endphp
            @error($name)
                {{ $attributes->merge(['class' => 'form-control is-invalid ' . $class.$classSelect]) }}
            @else
                {{ $attributes->merge(['class' => 'form-control ' . $class.$classSelect]) }}
            @enderror
            @if($select2 == '1')
                data-control="select2"
            @endif
            name="{{ $name }}" id="{{ $name }}">
            @if ($option)
                <option value="">{{ $option }}</option>
            @endif
            @if ($all)
                <option value="All">All</option>
            @endif
            @foreach ($datas as $key => $values)
                <option value="{{ $key }}" {{ $key == $value || $key == old($name) ? 'selected' : '' }}>
                    {{ $values }}</option>
            @endforeach
        </select>
        @if ($required)
            @error($name)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="invalid-feedback" id="{{ $name }}-error"></div>
        @endif
    </div>
@else
    <div class="form-group me-2">
        <select @if ($event) onchange="{{ $event }}" @endif
            {{ $attributes->merge(['class' => 'form-control ' . $class]) }} data-control="select2"
            name="{{ $name }}" id="{{ $name }}">
            @if ($option)
                <option value="">{{ $option }}</option>
            @endif
            @if ($all)
                <option value="All">All</option>
            @endif
            @foreach ($datas as $key => $values)
                <option value="{{ $key }}" {{ $key == $value ? 'selected' : '' }}>{{ $values }}
                </option>
            @endforeach
        </select>
    </div>
@endif
