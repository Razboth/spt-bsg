<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($period) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $period->id) }}">
        @csrf
        @if(!empty($period))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($period) ? __('Tambah Periode') : __('Edit Periode') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" :value="$period->name ?? ''" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal Mulai" name="start_date" required :value="$period->start_date ?? ''" class="w-100" />
                </div>
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal Selesai" name="end_date" required :value="$period->end_date ?? ''" class="w-100" />
                </div>
            </div>
            <x-form.textarea label="Catatan" placeholder="Masukkan Catatan" name="note" :value="$period->note ?? ''"/>
            <x-form.file label="Dokumen" name="filename" value="{{ $period->filename ?? '' }}" />
        </div>
        <x-form.modal-footer/>
    </form>
</div>