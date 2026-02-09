<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($agentBooster) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $agentBooster->id) }}">
        @csrf
        @if(!empty($agentBooster))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($agentBooster) ? __('Tambah Booster') : __('Edit Booster') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            @if(Auth::user()->role->name != 'Sales')
                <x-form.select label="Sales" name="agent_id" option="- Pilih Sales -" :datas="$agents" :value="$agentBooster->agent_id ?? ''" required />
            @else
                <input type="hidden" name="agent_id" value="{{ Auth::user()->id }}">
            @endif
            <x-form.select label="Customer" name="customer_id" option="- Pilih Customer -" :datas="$customers" :value="$agentBooster->customer_id ?? ''" required />
            <x-form.select label="Produk" name="product_id" option="- Pilih Produk -" :datas="$products" :value="$agentBooster->product_id ?? ''" required />
            <div class="row">
                <div class="col-md-6">
                    <x-form.select-month label="Bulan" name="month" :value="$agentBooster->month ?? date('m')" required />
                </div>
                <div class="col-md-6">
                    <x-form.select-year label="Tahun" name="year" :value="$agentBooster->year ?? date('Y')" required />
                </div>
            </div>
            <x-form.input label="Jumlah" placeholder="Masukkan Jumlah" name="value" :value="$agentBooster->value ?? ''" required maxlength="5"/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" :value="$agentBooster->description ?? ''"/>
        </div>
        <x-form.modal-footer/>
    </form>
</div>