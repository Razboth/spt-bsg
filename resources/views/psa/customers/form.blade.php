@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($customer) ? route(Str::replace('/', '.', $menu_path) . '.store') : route(Str::replace('/', '.', $menu_path) . '.update', $customer->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($customer))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($customer) ? 'Tambah Customer' : 'Edit Customer' }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="d-flex justify-content-center">
                    <x-form.image-input name="image" value="{{ $customer->image ?? '' }}" max="2" />
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Kode" name="code" :value="$customer->code ?? $lastNumber" required readonly class="bg-gray-100"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.select label="Kategori" name="category_id" option="- Pilih Kategori -" :datas="$categories" :value="$customer->category_id ?? ''" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nama Customer" placeholder="Masukkan Nama Customer" name="name" :value="$customer->name ?? ''" required/>
                        <x-form.input label="PIC" placeholder="Masukkan Nama PIC" name="pic" :value="$customer->pic ?? ''"/>
                        <x-form.textarea label="Alamat" placeholder="Masukkan Alamat" name="address" :value="$customer->address ?? ''"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Nomor KTP" placeholder="Masukkan Nomor KTP" name="identity_number" :value="$customer->identity_number ?? ''" maxlength="16" numeric/>
                        <x-form.input label="Nomor HP" placeholder="Masukkan Nomor HP" name="phone_number" :value="$customer->phone_number ?? ''" maxlength="15" numeric required/>
                        <x-form.input label="Email" placeholder="Masukkan Nama Customer" name="email" :value="$customer->email ?? ''"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" :value="$customer->description ?? ''"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $customer->status ?? '' }}" />
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
