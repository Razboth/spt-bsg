<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($master) ? route('settings.app-master-datas.store-master', $category->id) : route('settings.app-master-datas.update-master', $master->id) }}">
        @csrf
        @if(!empty($master))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($master) ? __('Tambah Master') : __('Edit Master') }} : {{ $category->name }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            @if($category->parent_id != 0)
                <x-form.select label="Induk" name="parent_id" option="- Pilih Induk -" required :datas="$parents" value="{{ $master->parent_id ?? '' }}" />
            @else
                <input type="hidden" name="parent_id" value="0">
            @endif
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $master->name ?? '' }}" required/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $master->description ?? '' }}" />
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Kode" class="text-uppercase" name="code" value="{{ $master->code ?? $lastCode }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Order" placeholder="Masukkan Order" name="order" required value="{{ $master->order ?? $lastOrder }}" numeric/>
                </div>
            </div>
        </div>
        <x-form.modal-footer />
    </form>
</div>
