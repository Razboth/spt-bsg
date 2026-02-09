<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($parameter) ? route('settings.app-parameters.store') : route('settings.app-parameters.update', $parameter->id) }}">
        @csrf
        @if(!empty($parameter))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($parameter) ? __('Tambah Parameter') : __('Edit Parameter') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $parameter->name ?? '' }}" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Kode" placeholder="Masukkan Kode" name="code" value="{{ $parameter->code ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Value" placeholder="Masukkan Value" name="value" value="{{ $parameter->value ?? '' }}" required/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $parameter->description ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
