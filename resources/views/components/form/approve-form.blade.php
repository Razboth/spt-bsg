<div class="card">
    <form method="POST" id="form-edit" action="{{ $actionRoute }}">
        @csrf
        @method('PATCH')
        <x-form.modal-header title="{{ $title }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.datepicker name="approved_date" label="Tanggal Approve" value="{{ $data->approved_date ?? date('Y-m-d') }}" required="true" />
            <x-form.textarea name="approved_note" label="Catatan" value="{{ $data->approved_note ?? '' }}" required="true" />
            <x-form.radio label="Approved Status" name="approved_status" :datas="$arrApprove" value="{{ $data->approved_status ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
