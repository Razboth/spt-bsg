@props(['status', 'english' => false])
<span class="badge badge-{{ $status == 't' || $status == 1 ? 'success' : 'danger' }}">
    @if($english)
        {{ $status == 't' || $status == 1 ? 'Active' : 'Inactive' }}
    @else
        {{ $status == 't' || $status == 1 ? 'Aktif' : 'Tidak Aktif' }}
    @endif
</span>
