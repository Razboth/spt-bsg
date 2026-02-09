<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($dataset) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $dataset->id) }}">
        @csrf
        @if(!empty($dataset))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($dataset) ? __('Tambah Dataset') : __('Edit Dataset') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Judul" placeholder="Masukkan Judul" name="title" value="{{ $dataset->title ?? '' }}" required/>
            <x-form.input label="Kode" placeholder="Masukkan Kode" name="code" value="{{ $dataset->code ?? '' }}" required/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $dataset->description ?? '' }}"/>
        </div>
        <x-form.modal-footer/>
    </form>
</div>
