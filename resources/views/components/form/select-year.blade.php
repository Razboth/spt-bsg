@props(['label' => '', 'startYear' => '', 'endYear' => '', 'name' => '', 'required' => '', 'value' => '', 'class' => '', 'event' => '', 'option' => '', 'all' => ''])
@php
    $range = $range ?? 3;
    $startYear = date('Y') - $range;
    $endYear = date('Y') + $range;
@endphp
@if($label)
    <div class="form-group mb-5">
        <label class="form-label {!! $required ? "required" : "" !!}">{{ $label }}</label>
        <select name="{{ $name }}"
                `                @error($name)
                {{ $attributes->merge(['class' => 'form-control form-select is-invalid '.$class]) }}
                @else
                    {{ $attributes->merge(['class' => 'form-control form-select '.$class]) }}
                    @enderror
                    id="{{ $name }}"
                @if($event)
                    onchange="{{ $event }}"
                @endif
        >
            @if($option)
                <option value="">{{ $option }}</option>
            @endif
            @if($all)
                <option value="All">All</option>
            @endif
            @for($i = $startYear; $i<=$endYear; $i++)
                <option value="{{ $i }}" {{ $i == $value ? 'selected' : '' }} >{{ $i }}</option>
            @endfor
        </select>
        @if($required)
            <div class="invalid-feedback" id="{{ $name }}-error"></div>
        @endif
    </div>
@else
    <div class="pr-1">
        <select name="{{ $name }}"
                `                @error($name)
                {{ $attributes->merge(['class' => 'form-control form-select is-invalid '.$class]) }}
                @else
                    {{ $attributes->merge(['class' => 'form-control form-select '.$class]) }}
                    @enderror
                    id="{{ $name }}"
                @if($event)
                    onchange="{{ $event }}"
                @endif
        >
            @if($option)
                <option value="">{{ $option }}</option>
            @endif
            @if($all)
                <option value="All">All</option>
            @endif
            @for($i = $startYear; $i<=$endYear; $i++)
                <option value="{{ $i }}" {{ $i == $value ? 'selected' : '' }} >{{ $i }}</option>
            @endfor
        </select>
    </div>
@endif
