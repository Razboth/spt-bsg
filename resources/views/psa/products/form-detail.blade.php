<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($detail) ? route(Str::replace('/', '.', $menu_path).'.store-child', $product->id) : route(Str::replace('/', '.', $menu_path).'.update-child', $detail->id) }}">
        @csrf
        @if(!empty($detail))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($detail) ? __('Tambah Produk') : __('Edit Produk') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="d-flex justify-content-center">
                <x-form.image-input name="icon" value="{{ $detail->icon ?? '' }}" />
            </div>
            <x-form.input label="Induk Produk" placeholder="Masukkan Nama Produk" name="name" :value="$product->name ?? ''" readonly class="bg-gray-100"/>
            <x-form.input label="Nama Produk" placeholder="Masukkan Nama Produk" name="name" :value="$detail->name ?? ''" required/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" :value="$detail->description ?? ''"/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Order" placeholder="Masukkan Order" name="order" value="{{ $detail->order ?? $lastOrder }}" numeric class="text-end" />
                </div>
                <div class="col-md-6">
                    <x-form.input label="Bobot" placeholder="Masukkan Bobot" name="weight" value="{{ $detail->weight ?? 0 }}" numeric class="text-end" maxlength="3" />
                </div>
            </div>
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $detail->status ?? '' }}" />
        </div>
        <x-form.modal-footer/>
    </form>
</div>