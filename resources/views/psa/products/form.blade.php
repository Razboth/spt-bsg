<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($product) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $product->id) }}">
        @csrf
        @if(!empty($product))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($product) ? __('Tambah Produk') : __('Edit Produk') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="d-flex justify-content-center">
                <x-form.image-input name="icon" value="{{ $product->icon ?? '' }}" max="2" />
            </div>
            <x-form.input label="Nama Produk" placeholder="Masukkan Nama Produk" name="name" :value="$product->name ?? ''" required/>
            <x-form.ckeditor label="Uraian" name="description" :value="$product->description ?? ''" :is-modal="true" />
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Order" placeholder="Masukkan Order" name="order" value="{{ $product->order ?? $lastOrder }}" numeric class="text-end" />
                </div>
                <div class="col-md-6">
                    <x-form.radio label="Tipe" name="type" :datas="$typeOption" value="{{ $product->type ?? '' }}" />
                </div>
            </div>
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $product->status ?? '' }}" />
        </div>
        <x-form.modal-footer/>
    </form>
</div>