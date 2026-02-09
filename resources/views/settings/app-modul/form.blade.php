<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($modul) ? route('settings.app-moduls.store') : route('settings.app-moduls.update', $modul->id) }}">
        @csrf
        @if(!empty($modul))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($modul) ? __('Tambah Modul') : __('Edit Modul') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $modul->name ?? '' }}" required/>
            <x-form.input label="Target" placeholder="Masukkan Target" name="target" value="{{ $modul->target ?? '' }}" required/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $modul->description ?? '' }}" />
            <x-form.input label="Order" placeholder="Masukkan Order" name="order" value="{{ $modul->order ?? '' }}"/>
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $modul->status ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
