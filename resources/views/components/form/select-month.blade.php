@props(['label' => '', 'placeholder' => '', 'name' => '', 'required' => '', 'value' => '', 'class' => '', 'event' => '', 'option' => '', 'all' => ''])
@if($label)
    <div class="form-group mb-5">
        <label class="form-label {!! $required ? "required" : "" !!}">{{ $label }}</label>
        <select name="{{ $name }}"
                @error($name)
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
            @for($i = 1; $i<=12; $i++)
                <option value="{{ $i }}" {{ $i == $value ? 'selected' : '' }} >{{ numToMonth($i) }}</option>
            @endfor
        </select>
        @if($required)
            <div class="invalid-feedback" id="{{ $name }}-error"></div>
        @endif
    </div>
@else
    <select
            @if($event)
                onchange="{{ $event }}"
            @endif
            @error($name)
            {{ $attributes->merge(['class' => 'form-control form-select is-invalid '.$class]) }}
            @else
                {{ $attributes->merge(['class' => 'form-control form-select '.$class]) }}
                @enderror
                data-control="select2" name="{{ $name }}" id="{{ $name }}">
        @if($option)
            <option value="">{{ $option }}</option>
        @endif
        @if($all)
            <option value="All">All</option>
        @endif
        @for($i = 1; $i<=12; $i++)
            <option value="{{ $i }}" {{ $i == $value ? 'selected' : '' }} >{{ numToMonth($i) }}</option>
        @endfor
    </select>
@endif
