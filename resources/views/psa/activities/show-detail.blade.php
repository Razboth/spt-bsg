<div class="card">
    <x-form.modal-header title="Detail Tahapan"/>
    <div class="separator mt-2 mb-5 d-flex"></div>
    <div class="card-body pt-0">
        <x-form.input label="Tahapan Aktifitas" name="type_id" :value="$detail->typeActivity->name ?? ''" readonly class="bg-gray-100" />
        <h5 class="text-gray-900 fw-semibold">Data Agenda</h5>
        <div style="width: 100px; height: 2px;" class="bg-primary mb-5"></div>
        <div class="row">
            <div class="col-md-6">
                <x-form.input label="Tanggal" name="plan_date" :value="setDate($detail->plan_date, 'd F Y') ?? ''" readonly class="bg-gray-100" />
            </div>
            <div class="col-md-6">
                <x-form.input label="Jam" name="plan_time" :value="$detail->plan_time ?? ''" readonly class="bg-gray-100" />
            </div>
        </div>
        <x-form.textarea label="Catatan" name="plan_note" :value="$detail->plan_note ?? ''" readonly class="bg-gray-100" />
        <div class="row">
            <div class="col-md-6">
                <x-form.file label="Foto" name="plan_filename" value="{{ $detail->plan_filename ?? '' }}" :is-delete="false"/>
            </div>
            <div class="col-md-6">
                <x-form.file label="Dokumen Pendukung" name="plan_document" value="{{ $detail->plan_document ?? '' }}" :is-delete="false"/>
            </div>
        </div>
        <h5 class="text-gray-900 fw-semibold">Data Realisasi</h5>
        <div style="width: 100px; height: 2px;" class="bg-primary mb-5"></div>
        <div class="row">
            <div class="col-md-6">
                <x-form.input label="Tanggal" name="plan_date" :value="setDate($detail->realization_date, 'd F Y') ?? ''" readonly class="bg-gray-100" />
            </div>
            <div class="col-md-6">
                <x-form.input label="Jam" name="plan_time" :value="$detail->realization_time ?? ''" readonly class="bg-gray-100" />
            </div>
        </div>
        <x-form.textarea label="Catatan" name="realization_note" :value="$detail->realization_note ?? ''" readonly class="bg-gray-100" />
        <div class="row">
            <div class="col-md-6">
                <x-form.file label="Foto" name="realization_filename" value="{{ $detail->realization_filename ?? '' }}" :is-delete="false" />
            </div>
            <div class="col-md-6">
                <x-form.file label="Dokumen Pendukung" name="realization_filename" value="{{ $detail->realization_filename ?? '' }}" :is-delete="false" />
            </div>
        </div>
    </div>
</div>