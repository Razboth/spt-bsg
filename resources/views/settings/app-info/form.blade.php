@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path).'.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <x-form.header title="Setting Aplikasi" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Title" name="title" :value="$info->title ?? ''" required />
                        <x-form.input-color label="Light Primary Color" name="light_primary_color" value="{{ $info->light_primary_color ?? '' }}" />
                        <x-form.input label="Versi Aplikasi" name="app_version" class="w-25" value="{{ $info->app_version ?? '' }}" required />
                        <x-form.input label="Text Footer" name="footer_text" :value="$info->footer_text ?? ''" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input-color label="Primary Color" name="primary_color" value="{{ $info->primary_color ?? '' }}" />
                        <x-form.input-color label="Background Light Primary Color" name="background_light_primary_color" value="{{ $info->background_light_primary_color ?? '' }}" />
                        <x-form.input label="Tahun Aplikasi" name="year" class="w-25" value="{{ $info->year ?? '' }}" required />
                        <x-form.radio label="Status Log Lokasi" name="location_log_status" :datas="$statusOption" value="{{ $info->location_log_status ?? '' }}" />
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Login Page Title" name="login_page_title" :value="$info->login_page_title ?? ''" />
                        <x-form.textarea label="Login Page Description" name="login_page_description" :value="$info->login_page_description ?? ''" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Login Page Subtitle" name="login_page_subtitle" :value="$info->login_page_subtitle ?? ''" />
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="row">
                    <div class="col-md-2">
                        <x-form.image-input label="Login Image" name="login_page_image" value="{{ $info->login_page_image ?? '' }}"/>
                    </div>
                    <div class="col-md-2">
                        <x-form.image-input label="Login Logo" name="login_page_logo" value="{{ $info->login_page_logo ?? '' }}"/>
                    </div>
                    <div class="col-md-2">
                        <x-form.image-input label="Login Background Image" name="login_page_background_image" value="{{ $info->login_page_background_image ?? '' }}"/>
                    </div>
                    <div class="col-md-2">
                        <x-form.image-input label="Sidebar Logo" name="app_logo" value="{{ $info->app_logo ?? '' }}"/>
                    </div>
                    <div class="col-md-2">
                        <x-form.image-input label="Sidebar Logo Small" name="app_logo_small" value="{{ $info->app_logo_small ?? '' }}"/>
                    </div>
                    <div class="col-md-2">
                        <x-form.image-input label="Icon Aplikasi" name="app_icon" value="{{ $info->app_icon ?? '' }}"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
