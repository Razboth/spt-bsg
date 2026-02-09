<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($category) ? route('settings.app-master-datas.store') : route('settings.app-master-datas.update', $category->id) }}">
        @csrf
        @if(!empty($category))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($category) ? __('Tambah Kategori') : __('Edit Kategori') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.select label="Induk" name="parent_id" :datas="$categories" option="- Pilih Induk -" value="{{ $user->role_id ?? '' }}" />
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $category->name ?? '' }}" required/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $category->description ?? '' }}" />
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Kode" class="text-uppercase" name="code" value="{{ $category->code ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Order" placeholder="Masukkan Order" name="order" required value="{{ $category->order ?? $lastOrder }}" numeric/>
                </div>
            </div>
        </div>
        <x-form.modal-footer />
    </form>
</div>
