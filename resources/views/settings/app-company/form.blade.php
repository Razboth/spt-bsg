@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path).'.update') }}"
            enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <x-form.header title="Data Perusahaan" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="d-flex justify-content-center">
                    <x-form.image-input name="logo" value="{{ $company->logo ?? '' }}" />
                </div>
                <x-form.input label="Nama Perusahaan" name="name" :value="$company->name ?? ''" required />
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="No. Telepon" name="phone_number" numeric
                                      :value="$company->phone_number ?? ''" required />
                        <x-form.input label="Email" name="email" :value="$company->email ?? ''"  />
                        <x-form.textarea label="Alamat" name="address" :value="$company->address ?? ''" required />

                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Fax" name="fax" :value="$company->fax ?? ''" />
                        <x-form.input label="Website" name="website" :value="$company->website ?? ''" />
                        <x-form.textarea label="Keterangan" name="description" :value="$company->address ?? ''" />
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
