<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head><base href="../../../"/>
    <title>{!! $app_info->title ?? '' !!}</title>
    <meta charset="utf-8" />
    <meta name="description" content="Sales Activity & Performance Management" />
    <meta name="keywords" content="sapm, sapm, sales, assistant" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Sales Activity & Performance Management" />
    <meta property="og:url" content="https://sapm-sg.namsatu.id/" />
    <meta property="og:site_name" content="Sales Activity & Performance Management" />
    <link rel="canonical" href="https://sapm-sg.namsatu.id/" />
    <link rel="shortcut icon" href="storage{{ $app_info->app_icon ?? '' }}"/>
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
          type="text/css"/>
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/>
    @include('layouts.style', ['primary' => $app_info->primary_color ?? '', 'lightPrimary' => $app_info->light_primary_color ?? '', 'backgroundLightPrimary' => $app_info->background_light_primary_color ?? ''])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_app_body"
      data-kt-app-header-fixed="true"
      data-kt-app-layout="light-sidebar"
{{--      @if(!Str::contains(url()->current(), 'dashboard'))--}}
          data-kt-app-sidebar-enabled="true"
      data-kt-app-sidebar-fixed="true"
      data-kt-app-sidebar-hoverable="true"
      data-kt-app-sidebar-push-header="true"
      data-kt-app-sidebar-push-toolbar="true"
      data-kt-app-sidebar-push-footer="true"
{{--      @endif--}}
      data-kt-app-toolbar-enabled="true"
      class="app-default">
<!--begin::Theme mode setup on page load-->
<script>var defaultThemeMode = "light";
    var themeMode;
    if (document.documentElement) {
        if (document.documentElement.hasAttribute("data-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-theme-mode");
        } else {
            if (localStorage.getItem("data-theme") !== null) {
                themeMode = localStorage.getItem("data-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-theme", themeMode);
    }</script>
<!--end::Theme mode setup on page load-->
<!--begin::App-->
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <!--begin::Page-->
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
        <!--begin::Header-->
        @include('layouts.header')
        <!--end::Header-->
        <!--begin::Wrapper-->
        <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
{{--            @if(!Str::contains(url()->current(), 'dashboard'))--}}
                <!--begin::Sidebar-->
                @include('layouts.sidebar')
                <!--end::Sidebar-->
{{--            @endif--}}
            <!--begin::Main-->
            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                <!--begin::Content wrapper-->
{{--                @if(!Str::contains(url()->current(), 'dashboard') && !Str::contains(url()->current(), 'ess-dash'))--}}
                    <div class="d-flex flex-column flex-column-fluid">
                        <!--begin::Toolbar-->
                        @include('layouts.toolbar')
                        <!--end::Toolbar-->
                        <!--begin::Content-->
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-auto">
                                @yield('content')
                            </div>
                        </div>
                        <!--end::Content-->
                    </div>
{{--                @else--}}
{{--                    @yield('content')--}}
{{--                @endif--}}
                <!--end::Content wrapper-->
                <!--begin::Footer-->
                @include('layouts.footer')
                <!--end::Footer-->
            </div>
            <!--end:::Main-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->
</div>
<!--end::App-->
<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
    <span class="svg-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)"
                  fill="currentColor"/>
            <path
                d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                fill="currentColor"/>
        </svg>
    </span>
    <!--end::Svg Icon-->
</div>
<style>
    .custom-border-bottom {
        border-bottom: 2px solid #E4E6EF !important;
    }
</style>
<!--end::Scrolltop-->
<!--begin::Javascript-->
<script>var hostUrl = "assets/";</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('assets/js/general.js') }}"></script>
<!--end::Global Javascript Bundle-->
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<!--begin::DataTables global error handler-->
<script>
    (function () {
        if (typeof $ === 'undefined' || !$.fn || !$.fn.dataTable) return;

        // Matikan alert() bawaan DataTables
        $.fn.dataTable.ext.errMode = 'none';

        var dtToast = (typeof Swal !== 'undefined') ? Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 6000,
            timerProgressBar: true,
            customClass: { popup: 'shadow-sm' }
        }) : null;

        var lastXhr = {};

        $(document).on('xhr.dt', function (e, settings, json, xhr) {
            lastXhr[settings.sTableId || 'dt'] = xhr;
        });

        $(document).on('error.dt', function (e, settings, techNote, message) {
            var xhr    = lastXhr[settings.sTableId || 'dt'];
            var status = xhr ? xhr.status : 0;
            var text   = 'Gagal memuat data tabel. Silakan muat ulang halaman.';

            if (status === 401 || status === 419) {
                text = 'Sesi Anda telah berakhir. Halaman akan dimuat ulang...';
                setTimeout(function () { window.location.reload(); }, 3000);
            } else if (status === 403) {
                text = 'Anda tidak memiliki akses ke data ini.';
            } else if (status === 404) {
                text = 'Sumber data tidak ditemukan.';
            } else if (status >= 500) {
                text = 'Terjadi kesalahan pada server (' + status + '). Silakan coba lagi.';
            } else if (status === 0 && xhr) {
                text = 'Koneksi terputus. Periksa jaringan Anda.';
            }

            // Detail teknis tetap ada di console untuk debugging
            console.error('[DataTables] ' + techNote + ': ' + message, xhr ? xhr.responseText : null);

            // Hentikan indikator "Loading..." yang tertinggal
            if (settings.sTableId) {
                $('#' + settings.sTableId + '_processing').hide();
            }

            if (dtToast) {
                dtToast.fire({ icon: 'error', title: text });
            } else {
                console.warn(text);
            }
        });
    })();
</script>
<!--end::DataTables global error handler-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
<script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/fslightbox/fslightbox.bundle.js') }}"></script>
<!--CKEditor Build Bundles:: Only include the relevant bundles accordingly-->
{{--<script src="{{asset('assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js')}}"></script>--}}
{{--<script src="{{asset('assets/plugins/custom/ckeditor/ckeditor-inline.bundle.js')}}"></script>--}}
{{--<script src="{{asset('assets/plugins/custom/ckeditor/ckeditor-balloon.bundle.js')}}"></script>--}}
{{--<script src="{{asset('assets/plugins/custom/ckeditor/ckeditor-balloon-block.bundle.js')}}"></script>--}}
{{--<script src="{{asset('assets/plugins/custom/ckeditor/ckeditor-document.bundle.js')}}"></script>--}}
@yield('scripts')
@include('sweetalert::alert')

{{--@include('sweetalert::alert')--}}
<!--end::Custom Javascript-->
<!--end::Javascript-->
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function sendLocationToServer(latitude, longitude, address) {
        $.ajax({
            url: '/app/current-location',
            type: 'POST',
            data: {
                _token: csrfToken,
                latitude: latitude,
                longitude: longitude,
                address: address
            },
            success: function(response) {
                console.log('Lokasi terkirim:', response);
            },
            error: function(xhr, status, error) {
                console.error('Gagal mengirim lokasi:', error);
            }
        });
    }

    function getAddressFromCoords(lat, lon, callback) {
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`;

        fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const address = data.display_name || 'Alamat tidak ditemukan';
            callback(address);
        })
        .catch(error => {
            console.error('Gagal mengambil alamat:', error);
            callback('Alamat tidak ditemukan');
        });
    }

    function trackLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Ambil alamat lalu kirim ke server
                getAddressFromCoords(latitude, longitude, function(address) {
                    sendLocationToServer(latitude, longitude, address);
                });

            }, function(error) {
                console.error('Gagal mengambil lokasi:', error.message);
            });
        } else {
            alert("Geolocation tidak didukung oleh browser ini.");
        }
    }

    @if($app_info->location_log_status == 't')
        // Jalankan pertama kali
        trackLocation();

        // Jalankan setiap 1 menit
        setInterval(trackLocation, 60000);
    @endif
</script>

</body>
<!--end::Body-->
</html>
