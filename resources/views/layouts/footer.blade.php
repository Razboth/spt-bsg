<div id="kt_app_footer" class="app-footer">
    <!--begin::Footer container-->
    <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
        <!--begin::Copyright-->
        <div class="text-dark order-2 order-md-1">
            <span class="text-muted fw-semibold me-1">{{ $app_info->year ?? '' }}&copy;</span>
            <span class="text-gray-800">{{ $app_info->footer_text ?? '' }}</span>
        </div>
        <!--end::Copyright-->
        <!--begin::Menu-->
        <span class="text-muted fw-semibold order-1">Versi {{ env('APP_VERSION') }}</span>

        <!--end::Menu-->
    </div>
    <!--end::Footer container-->
</div>
