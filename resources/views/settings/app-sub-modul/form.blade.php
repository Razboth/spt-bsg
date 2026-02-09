<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($submodul) ? route('settings.app-sub-moduls.store') : route('settings.app-sub-moduls.update', $submodul->id) }}">
        @csrf
        @if(!empty($submodul))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($submodul) ? __('Tambah Sub Modul') : __('Edit Sub Modul') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.select label="Modul" name="app_modul_id" required :datas="$moduls" value="{{ $submodul->app_modul_id ?? $filterModul }}" />
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $submodul->name ?? '' }}" required/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $submodul->description ?? '' }}" />
            <x-form.input label="Order" placeholder="Masukkan Order" name="order" value="{{ $submodul->order ?? '' }}"/>
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $submodul->status ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
