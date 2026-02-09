@props(['route' => '', 'text' => '', 'class' => '', 'filter' => '', 'size' => 'mw-650px', 'withBack' => false, 'url_back' => '', 'icon' => 'fa-solid fa-plus'])
@if($withBack)
    <a href="{{ !empty($url_back) ? $url_back : url()->previous() }}" class="btn btn-light-primary mx-3">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endif
<button {{ $attributes->merge(['class' => 'btn btn-primary btn-modal '.$class]) }} data-bs-toggle="modal" data-url="{!! $route !!}" data-filter="{{ $filter }}" data-size="{{ $size }}">
    <i class="{{ $icon }}"></i> {{ $text }}
</button>
