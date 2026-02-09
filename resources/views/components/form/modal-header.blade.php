@props(['title', 'isDismissable' => true, 'route' => ''])
<div class="card-header border-0 pt-6 justify-content-between d-flex">
    <div class="card-title">
        <h3 class="card-label">
            {{ $title }}
        </h3>
    </div>
    @if($isDismissable)
        <a
            @if(!empty($route))
                href="{{ $route }}" id="btnCloseModal" class="svg-icon svg-icon-1"
            @else
                href="#" id="btnCloseModal" class="svg-icon svg-icon-1" data-bs-dismiss="modal"
            @endif
        >
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>
                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>
            </svg>
        </a>
    @endif
</div>
