@props(['backgroundColor' => '', 'textColor' => '', 'title' => '', 'subtitle' => '', 'photo' => ''])
<div class="d-flex align-items-center">
    @if($photo)
        <div class="symbol symbol-50px symbol-md-50px me-6">
            <img src="{{ asset('storage'.$photo) }}" alt="photo" />
        </div>
    @else
        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
            <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                <div class="symbol-label fs-3 bg-light-{{ $backgroundColor }} text-{{ $textColor }}">{{ getInitial($title) }}</div>
            </div>
        </div>
    @endif
    <div class="d-flex flex-column">
        <span class="text-gray-800 mb-1">{{ $title }}</span>
        <span>{{ $subtitle }}</span>
    </div>
</div>
