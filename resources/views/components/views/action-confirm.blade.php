@props(['url' => '', 'icon' => '', 'text' => ''])
<button
    href="{{ $url }}"
    id="btnConfirm" class="btn btn-icon btn-light-info w-30px h-30px"
    data-text="{{ $text }}"
        title="Kirim Data">
    <i class="fa-solid {{ $icon }}"></i>
</button>
