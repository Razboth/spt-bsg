@props(['name' => '', 'value' => '', 'range' => '5', 'event' => '', 'option' => '', 'all' => '', 'class' => ''])
@php
    $range = $range ?? 5;
    $startYear = date('Y') - $range;
    $endYear = date('Y') + $range;
@endphp
<div {{ $attributes->merge(['class' => 'position-relative d-flex align-items-center '.$class]) }}>
    <select name="{{ $name }}"
            data-control="select2"
            class="form-control w-100px mx-5"
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
