@props(['value', 'english' => false])
<span class="badge badge-{{ $value == 't' || $value == 1 ? 'success' : 'danger' }}">
    @if($english)
        {{ $value == 't' || $value == 1 ? 'Yes' : 'No' }}
    @else
        {{ $value == 't' || $value == 1 ? 'Ya' : 'Tidak' }}
    @endif
</span>
