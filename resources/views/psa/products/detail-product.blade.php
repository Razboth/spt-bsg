<div class="card">
    <x-form.modal-header title="Detail Product" />
    <div class="separator mt-2 mb-5 d-flex"></div>
    <div class="card-body pt-0">
        <div class="d-flex justify-content-center">
            <div class="image-input image-input-outline mb-5 image-input-placeholder" data-kt-image-input="true" >
                <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $product->icon == '' ? asset('assets/media/blank-image.svg') : asset('storage/'.$product->icon) }});"></div>
            </div>
        </div>
        <x-form.input label="Nama Produk" placeholder="Masukkan Nama Produk" name="name" :value="$product->name ?? ''" readonly class="bg-gray-100" />
        <div class="form-group mb-5">
            <label class="form-label">Keterangan</label>
            <div class="">
                {!! $product->description ?? '' !!}
            </div>
        </div>
        <x-form.input label="Status" placeholder="Masukkan Nama Produk" name="name" :value="$product->status == 't' ? 'Aktif' : 'Tidak Aktif'" readonly class="bg-gray-100" />
    </div>
</div>
