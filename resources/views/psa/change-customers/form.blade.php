<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($changeCustomer) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $changeCustomer->id) }}">
        @csrf
        @if(!empty($changeCustomer))
            @method('PATCH')
        @endif
        <x-form.modal-header title="Form Pengajuan Perpindahan Customer"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Nomor" name="number" :value="$changeCustomer->number ?? $lastNumber" required readonly class="bg-gray-100"/>
                </div>
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal" name="date" :value="$changeCustomer->date ?? date('Y-m-d')" required class="w-100" />
                </div>
            </div>
            <div class="mb-5">
                <h5 class="text-gray-900 fw-semibold">Cabang Awal</h5>
                <div style="width: 100px; height: 2px;" class="bg-primary"></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <x-form.select label="Cabang" name="from_unit_id" option="- Pilih Cabang -" :datas="$units" :value="$changeCustomer->from_unit_id ?? ''" required event="getSub(this.value, 'from_agent_id', 'sales');" />
                </div>
                <div class="col-md-6">
                    <x-form.select label="Sales" name="from_agent_id" option="- Pilih Sales -" :datas="$agents" :value="$changeCustomer->from_agent_id ?? ''" required event="getSub(this.value, 'customer_id', 'customers');"/>
                </div>
            </div>
            <div class="mb-5">
                <h5 class="text-gray-900 fw-semibold">Cabang Tujuan</h5>
                <div style="width: 100px; height: 2px;" class="bg-primary"></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <x-form.select label="Cabang" name="to_unit_id" option="- Pilih Cabang -" :datas="$units" :value="$changeCustomer->to_unit_id ?? ''" required event="getSub(this.value, 'to_agent_id', 'sales');" />
                </div>
                <div class="col-md-6">
                    <x-form.select label="Sales" name="to_agent_id" option="- Pilih Sales -" :datas="$agents" :value="$changeCustomer->to_agent_id ?? ''" required />
                </div>
            </div>
            <div class="separator mt-2 mb-5 d-flex"></div>
            <x-form.select label="Customer" name="customer_id" option="- Pilih Customer -" :datas="$customers" :value="$changeCustomer->customer_id ?? ''" required />
            <x-form.textarea label="Catatan" placeholder="Masukkan Catatan" name="description" :value="$changeCustomer->description ?? ''"/>
        </div>
        <x-form.modal-footer/>
    </form>
</div>