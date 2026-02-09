@props(['status', 'url' => ''])
@php
    if($status == 't'){
        $class = 'success';
        $text = 'Disetujui';
    } else if($status == 'f'){
        $class = 'danger';
        $text = 'Ditolak';
    } else {
        $class = 'warning';
        $text = 'Pending';
    }
@endphp
@if($url)
    <a class="badge badge-{{ $class }} btn-modal" data-bs-toggle="modal" data-url="{{ $url }}">
        {{ $text }}
    </a>
@else
<span class="badge badge-{{ $class }}">
    {{ $text }}
</span>
@endif

