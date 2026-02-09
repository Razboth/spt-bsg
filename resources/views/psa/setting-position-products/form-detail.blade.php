<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($detail) ? route(Str::replace('/', '.', $menu_path).'.store-product', $setting->id) : route(Str::replace('/', '.', $menu_path).'.update-product', $detail->id) }}">
        @csrf
        @if(!empty($detail))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($detail) ? __('Tambah Produk') : __('Edit Produk') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="setting_position_product_id" value="{{ $setting->id ?? '' }}"/>
            <x-form.select label="Produk" name="product_id" option="- Pilih Produk -" :datas="$products" :value="$detail->product_id ?? ''" required />
            <x-form.input label="Bobot" placeholder="Masukkan Bobot" name="weight" value="{{ $detail->weight ?? 0 }}" class="text-end" maxlength="5" required />
        </div>
        <x-form.modal-footer/>
    </form>
</div>