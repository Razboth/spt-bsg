@props(['title' => '', 'group' => 'photos', 'value' => '', 'editRoute', 'deleteRoute'])
<div class="col">
    <a class="d-block overlay" data-fslightbox="lightbox-{{ $group }}" href="{{ asset('storage/'.$value) }}">
        <div class="overlay-wrapper bgi-no-repeat bgi-position-center bgi-size-cover card-rounded min-h-175px" style="background-image:url('{{ asset('storage/'.$value) }}')">
        </div>
        <div class="overlay-layer card-rounded bg-dark bg-opacity-25">
            <i class="bi bi-eye-fill fs-sm-1 text-white"></i>
        </div>
    </a>
    <div class="fs-6 fw-bold mt-5 d-flex flex-stack">
        <!--begin::Label-->
        <a href="#" class="fs-4 text-gray-900 fw-bold text-hover-primary text-gray-900 lh-base">{{ $title }}</a>
        <!--end::Label-->

        <div>
            @if($editRoute)
                <a data-url="{{ $editRoute }}" title="Edit Data" data-bs-toggle="modal" class="btn btn-modal btn-icon btn-light-primary w-25px h-25px me-1">
                    <i class="fa-solid fa-pen fs-7"></i>
                </a>
            @endif
            @if($deleteRoute)
                <button href="{{ $deleteRoute }}" id="delete" class="btn btn-icon btn-light-danger w-25px h-25px" title="Delete Data">
                    <i class="fa-solid fa-trash fs-7"></i>
                </button>
            @endif
        </div>
    </div>
</div>