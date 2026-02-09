@props(['nameMonth' => '', 'valueMonth' => '', 'nameYear' => '', 'valueYear' => '', 'range' => '5', 'event' => '', 'option' => '', 'all' => '', 'class' => ''])
@php
    $range = $range ?? 5;
    $startYear = date('Y') - $range;
    $endYear = date('Y') + $range;
@endphp
<div {{ $attributes->merge(['class' => 'position-relative d-flex align-items-center '.$class]) }}>
<select
    @if($event)
        onchange="{{ $event }}"
    @endif
    data-control="select2"
    class="form-control w-150px"
    name="{{ $nameMonth }}"
    id="{{ $nameMonth }}">
    @if($option)
        <option value="">{{ $option }}</option>
    @endif
    @if($all)
        <option value="All">All</option>
    @endif
    @for($i = 1; $i<=12; $i++)
        <option value="{{ $i }}" {{ $i == $valueMonth ? 'selected' : '' }} >{{ numToMonth($i) }}</option>
    @endfor
</select>
<select name="{{ $nameYear }}"
        data-control="select2"
        class="ms-5 form-control w-100px "
        id="{{ $nameYear }}"
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
        <option value="{{ $i }}" {{ $i == $valueYear ? 'selected' : '' }} >{{ $i }}</option>
    @endfor
</select>
</div>
