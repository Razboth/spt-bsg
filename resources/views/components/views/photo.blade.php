@props(['photo'])
@if($photo)
    <a class="d-block card-rounded overlay h-50px w-50px" data-fslightbox="lightbox-{{ $photo }}" href="{{ asset('storage/'.$photo) }}">
        <!--begin::Image-->
        <div class="overlay-wrapper bgi-no-repeat bgi-position-center bgi-size-cover card-rounded h-50px w-50px" style="background-image:url('{{ asset('storage/'.$photo) }}')"></div>
        <!--end::Image-->
        <!--begin::Action-->
        <div class="overlay-layer card-rounded bg-dark bg-opacity-25">
            <i class="bi bi-eye-fill fs-sm-1 text-white"></i>
        </div>
        <!--end::Action-->
    </a>
@else
    <div class="overlay-wrapper bgi-no-repeat bgi-position-center bgi-size-cover card-rounded h-50px w-50px" style="background-image:url({{ asset('assets/media/blank-image.svg') }});"></div>
@endif
