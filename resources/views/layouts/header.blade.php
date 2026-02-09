<div id="kt_app_header" class="app-header">
{{--    @if(!Str::contains(url()->current(), 'dashboard'))--}}
        <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
            <div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show sidebar menu">
                <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <span class="svg-icon svg-icon-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="currentColor" />
                        <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="currentColor" />
                    </svg>
                </span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                <a class="d-lg-none">
                    <img alt="Logo" src="storage/{{ $app_info->app_logo_small ?? '' }}" class="h-40px" />
                </a>
            </div>
{{--        @else--}}
{{--            <div class="app-container container-xxl d-flex align-items-stretch justify-content-between" id="kt_app_header_container">--}}
{{--                <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-0">--}}
{{--                    <div>--}}
{{--                        <img alt="Logo" src="storage/{{ $app_info->app_logo ?? '' }}" class="h-60px app-header-logo-default" />--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--        @endif--}}
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
            <div class="app-header-menu app-header-mobile-drawer align-items-stretch ps-0" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
                <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
                    @php $role = Auth::user()->role_id; @endphp
                    @php
                        $currentRoute = Route::currentRouteName();
                    @endphp
                    @foreach($app_moduls as $key => $modul)
                        @php
                            $firstMenu = DB::table('app_menus as t1')
                            ->select('t1.id', 't3.name')
                            ->join('app_sub_moduls as t2', 't1.app_sub_modul_id', 't2.id')
                            ->join('app_permissions as t3', 't1.id', 't3.type_id')
                            ->join('app_role_has_permissions as t4', 't3.id', 't4.permission_id')
                            ->where('t1.status', 't')
                            ->where('t1.app_modul_id', $modul->id)
                            ->where('t3.method', 'view')
                            ->where('t3.type', 'menu')
                            ->where('t4.role_id', $role)
                            ->orderBy('t2.order')
                            ->orderBy('t1.order')
                            ->first();

                            if($firstMenu){
                                $checkChild = DB::table('app_menus as t1')
                                ->select('t3.name')
                                ->join('app_sub_moduls as t2', 't1.app_sub_modul_id', 't2.id')
                                ->join('app_permissions as t3', 't1.id', 't3.type_id')
                                ->join('app_role_has_permissions as t4', 't3.id', 't4.permission_id')
                                ->where('t1.status', 't')
                                ->where('t1.app_modul_id', $modul->id)
                                ->where('t3.method', 'view')
                                ->where('t3.type', 'menu')
                                ->where('t4.role_id', $role)
                                ->where('t1.parent_id', $firstMenu->id)
                                ->orderBy('t2.order')
                                ->orderBy('t1.order')
                                ->first();

                                if($checkChild){
                                    $route = str_replace("view ", "", str_replace("/", ".", $checkChild->name) . ".index");
                                }else {
                                    $route = str_replace("view ", "", str_replace("/", ".", $firstMenu->name) . ".index");
                                }
                            }
                        @endphp
                        @if($firstMenu)
                            @can($modul->target)
                                @php
                                    $routeFirstMenu = Route::has($route) ? route($route) : '#';
                                @endphp
                                <a
                                    href="{{ $routeFirstMenu }}"
                                    class="menu-item menu-here-bg menu-lg-down-accordion me-0 me-lg-2 {{ $modul->target == $selected_modul->target ? 'show here' : '' }}"

                                >
                                    <span class="menu-link"
                                          @if($modul->target == $selected_modul->target)
                                              style="background-color: {{ $app_info->primary_color }} !important;"
                                    @endif
                                    >
                                        <span class="menu-title"
                                          @if($modul->target == $selected_modul->target)
                                              style="color: #ffffff !important;"
                                         @else
                                                style="color: #000000 !important;"
                                        @endif
                                        >{{ $modul->name }}</span>
                                        <span class="menu-arrow d-lg-none"></span>
                                    </span>
                                </a>
                            @endcan
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="app-navbar flex-shrink-0">
                <div class="app-navbar-item ms-1 ms-lg-3 me-2">
                    <!--begin::Menu- wrapper-->
                    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <span class="svg-icon svg-icon-muted svg-icon-2hx">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="currentColor"/>
                                <path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="currentColor"/>
                            </svg>
                        </span>
                    </div>
                    <!--begin::Menu-->
                    <!--end::Menu-->
                    <!--end::Menu wrapper-->
                </div>
                <div class="app-navbar-item ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <div class="text-end d-flex flex-column justify-content-center">
                        <span class="text-gray-700 fw-semibold fs-6 me-3">{{ Auth::user()->name }}</span>
                        <span class="text-gray-700 fs-8 me-3">{{ Auth::user()->role->name }}</span>
                    </div>
                    <div class="cursor-pointer symbol symbol-35px symbol-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <img src="{{ Auth::user()->photo ? asset('storage'.Auth::user()->photo) : asset('assets/media/blank-image.svg') }}" alt="user" />
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="{{ Auth::user()->photo ? asset('storage'.Auth::user()->photo) : asset('assets/media/blank-image.svg') }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">{{ Auth::user()->name }}
                                    </div>
                                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{ Auth::user()->email }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="menu-item px-5">
                            <a href="#" class="menu-link px-5 btn-modal" data-bs-toggle="modal" data-url="{{ route('app.edit-profile', $currentRoute) }}">Edit Profile</a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="#" class="menu-link px-5 btn-modal" data-bs-toggle="modal" data-url="{{ route('app.edit-password', $currentRoute) }}">Ubah Password</a>
                        </div>
                        @isset($app_apk)
                            <div class="menu-item px-5">
                                <a href="{{ asset('storage'.$app_apk->filename) }}" download class="menu-link px-5">Download APK Presensi</a>
                            </div>
                        @endisset
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="menu-link px-5">Sign Out</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                <div class="app-navbar-item d-lg-none ms-2 me-n3" title="Show header menu">
                    <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_header_menu_toggle">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z" fill="currentColor" />
                                <path opacity="0.3" d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z" fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form/>
        <style>
            .single-line-overflow {
                white-space: nowrap; /* Pastikan konten div tidak memulai baris baru */
                overflow: hidden; /* Sembunyikan konten yang melampaui batas div */
                text-overflow: ellipsis; /* Tambahkan ellipsis (...) jika teks terpotong */
                max-width: 100%; /* Atur lebar maksimum yang diinginkan untuk div */
            }

        </style>