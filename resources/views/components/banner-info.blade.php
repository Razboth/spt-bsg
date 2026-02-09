@props(['title', 'text'])
<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed rounded-3 p-6">
    <!--begin::Wrapper-->
    <div class="d-flex flex-stack flex-grow-1 ">
        <!--begin::Content-->
        <div class=" fw-semibold">
            <h4 class="text-gray-900 fw-bold">{{ $title }}</h4>

            <div class="fs-6 text-gray-700 ">{{ $text }}</div>
        </div>
        <!--end::Content-->

    </div>
    <!--end::Wrapper-->
</div>