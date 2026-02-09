<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($detail) ? route(Str::replace('/', '.', $menu_path).'.store-detail', $dataset->id) : route(Str::replace('/', '.', $menu_path).'.update-detail', $detail->id) }}">
        @csrf
        @if(!empty($detail))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($detail) ? __('Tambah Detail') : __('Edit Detail') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="dataset_id" value="{{ $dataset->id }}">
            <x-form.input label="Judul" placeholder="Masukkan Judul" name="title" value="{{ $detail->title ?? '' }}" required/>
            <x-form.input label="Penggunaan" placeholder="Masukkan Penggunaan" name="code" value="{{ $detail->code ?? '' }}" required/>
            <x-form.input label="Contoh Hasil" placeholder="Masukkan Contoh Hasil" name="example" value="{{ $detail->example ?? '' }}"/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $detail->description ?? '' }}"/>
        </div>
        <x-form.modal-footer/>
    </form>
</div>
