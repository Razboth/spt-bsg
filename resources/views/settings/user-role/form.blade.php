<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($role) ? route('settings.user-roles.store') : route('settings.user-roles.update', $role->id) }}" enctype="multipart/form-data">
        @csrf
        @if(!empty($role))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($modul) ? __('Tambah Role') : __('Edit Role') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $role->name ?? '' }}" required/>
            <x-form.checkbox label="Modul" name="modul" :arr="$arrModul" :datas="$moduls ?? []" />
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" value="{{ $role->description ?? '' }}" name="description" />
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $role->status ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
