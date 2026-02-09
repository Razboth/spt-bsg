<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head><base href="../../../"/>
    @php($app_info = \App\Models\Setting\AppInfo::first())
    <title>{!! $app_info->title ?? '' !!}</title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    @include('layouts.style', ['primary' => $app_info->primary_color ?? '', 'lightPrimary' => $app_info->light_primary_color ?? '', 'backgroundLightPrimary' => $app_info->background_light_primary_color ?? ''])
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="app-blank app-blank">
<!--begin::Theme mode setup on page load-->
<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-theme-mode")) { themeMode = document.documentElement.getAttribute("data-theme-mode"); } else { if ( localStorage.getItem("data-theme") !== null ) { themeMode = localStorage.getItem("data-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-theme", themeMode); }</script>
<!--end::Theme mode setup on page load-->
<!--begin::Root-->
<div class="d-flex flex-column flex-root" id="kt_app_root">
    <!--begin::Authentication - Sign-in -->
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
        <!--begin::Body-->
        <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
            <!--begin::Form-->
            <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                <!--begin::Wrapper-->
                <div class="w-lg-500px p-10">
                    <!--begin::Form-->
                    <form class="form w-100" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="text-center">
                            <a href="#" class="mb-0 mb-lg-6">
                                <img alt="Logo" src="storage/{{ $app_info->login_page_logo ?? '' }}" class="w-150px text-center" />
                            </a>
                        </div>
                        <!--begin::Heading-->
                        <div class="text-center mb-11">
                            <!--begin::Title-->
                            <h1 class="text-dark fw-bolder mb-3">{!! $app_info->login_page_title ?? '' !!}</h1>
                            <!--end::Title-->
                            <!--begin::Subtitle-->
                            <div class="text-gray-500 fw-semibold fs-6">Welcome back! Please enter your detail.</div>
                            <!--end::Subtitle=-->
                        </div>
                        <!--begin::Heading-->
                        <!--begin::Input group=-->
                        <div class="fv-row mb-8">
                            <!--begin::Email-->
                            <input type="text" placeholder="Email or NIP" name="login" autocomplete="off" class="form-control bg-transparent" />
                            <!--end::Email-->
                            @error('username')
                            @if(!str_contains($message, 'salah'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="username" data-validator="notEmpty">{{ $message }}</div>
                                </div>
                            @endif
                            @enderror
                            @error('email')
                            @if(!str_contains($message, 'salah'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="username" data-validator="notEmpty">{{ $message }}</div>
                                </div>
                            @endif
                            @enderror
                        </div>
                        <!--end::Input group=-->
                        <div class="fv-row mb-3">
                            <!--begin::Password-->
                            <input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" />
                            @error('password')
                            @if(!str_contains($message, 'salah'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="password" data-validator="notEmpty">{{ $message }}</div>
                                </div>
                            @endif
                            @enderror
                        </div>

                        <!--begin::Captcha-->
                        @if(!request()->cookie('device_recognized'))
                        <div class="fv-row mb-3">
                            <div class="captcha mb-3">
                                <span>{!! captcha_img() !!}</span>
                                <button type="button" class="btn btn-danger" class="reload" id="reload">
                                    &#x21bb;
                                </button>
                            </div>
                            <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="captcha">
                            @error('captcha')
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="captcha" data-validator="notEmpty">{{ $message }}</div>
                                </div>
                            @enderror
                        </div>
                        @endif
                        <!--end::Captcha-->

                        @if ($errors->has('username') || $errors->has('email'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('username') ?: $errors->first('email') }}</strong>
                            </span>
                        @endif

                        @if($errors->any())
                            @error('username')
                            @if(str_contains($message, 'salah'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="username" data-validator="notEmpty">{{ $message }}</div>
                                </div>
                            @endif
                            @enderror

                            @error('email')
                            @if(str_contains($message, 'salah'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="username" data-validator="notEmpty">{{ $message }}</div>
                                </div>
                            @endif
                            @enderror

                            @error('password')
                            @if(str_contains($message, 'salah'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="password" data-validator="notEmpty">{{ $message }}</div>
                                </div>
                            @endif
                            @enderror
                        @endif
                        <!--end::Input group=-->
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-5">
                            <div></div>
                            <!--begin::Link-->
                            {{--                            <a href="../../demo1/dist/authentication/layouts/corporate/reset-password.html" class="link-primary">Forgot Password ?</a>--}}
                            <!--end::Link-->
                        </div>
                        <!--end::Wrapper-->
                        <!--begin::Submit button-->
                        <div class="d-grid mb-10">
                            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                <!--begin::Indicator label-->
                                <span class="indicator-label">Sign In</span>
                                <!--end::Indicator label-->
                                <!--begin::Indicator progress-->
                                <span class="indicator-progress">Please wait...
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                <!--end::Indicator progress-->
                            </button>
                        </div>
                        <!--end::Submit button-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Form-->
        </div>
        <!--end::Body-->
        <!--begin::Aside-->
        <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-xxl-cover bgi-position-center order-1 order-lg-2" style="background-image: url({{ isset($app_info) ? 'storage'.$app_info->login_page_background_image : '' }})">
            <!--begin::Content-->
            <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                <!--begin::Logo-->
                <!--end::Logo-->
                <!--begin::Image-->
                <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-400px mb-10 mb-lg-20" src="storage{{ $app_info->login_page_image ?? '' }}" alt="" />
                <!--end::Image-->
                <!--begin::Title-->
                <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-2">{!! $app_info->login_page_subtitle ?? '' !!}</h1>
                <h1 class="d-none d-lg-block text-warning fs-2qx fw-bolder text-center mb-5">{!! $app_info->login_page_description ?? '' !!}</h1>
                <!--end::Title-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Aside-->
    </div>
    <!--end::Authentication - Sign-in-->
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var signInForm = document.querySelector('.form'); // Sesuaikan selector ini jika perlu
        signInForm.addEventListener('submit', function() {
            var signInButton = document.getElementById('kt_sign_in_submit');
            signInButton.innerHTML = 'Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>';
            signInButton.disabled = true;
        });

        $('#reload').click(function () {
            $.ajax({
                type: 'GET',
                url: 'reload-captcha',
                success: function (data) {
                    $(".captcha span").html(data.captcha);
                }
            });
        });
    });
</script>


<!--end::Root-->
<!--begin::Javascript-->
<script>var hostUrl = "assets/";</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="{{ asset('assets/js/custom/authentication/sign-in/general.js') }}"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
</body>
<!--end::Body-->
</html>
