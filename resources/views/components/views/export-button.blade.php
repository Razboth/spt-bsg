@props(['text' => '', 'event' => '', 'id' => '', 'url' => '', 'class' => '', 'type' => 'excel'])
@php
    $color = $type == 'pdf' ? 'danger' : 'primary';
@endphp
<a {{ $attributes->merge(['class' => 'btn btn-light-'.$color.' '.$class]) }} id="{{ $id }}" data-url="{{ $url }}" data-type="{{ $type }}"
   @if($type == 'pdf')
       target="_blank"
   @endif
@if($event)
    onclick="{{ $event }}"
    @endif
    >
    <i class="fa-solid fa-file-{{ $type == 'excel' ? 'excel' : 'pdf' }}"></i> {{ $text }}
</a>
