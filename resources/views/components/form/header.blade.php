@props(['title', 'isCanSave' => true, 'buttonText' => 'Simpan',  'backUrl' => null])
<div class="card-header border-0 pt-6 justify-content-between d-flex">
    <div class="card-title">
        <h3 class="card-label">
            {{ $title }}
        </h3>
    </div>
    <div>
        <a href="{{ $backUrl ? : url()->previous() }}" class="btn btn-light-primary me-3">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        @if($isCanSave)
            <button type="button" class="btn btn-primary" id="btnSubmitForm">
                <span class="indicator-label"><i class="fa-regular fa-floppy-disk"></i> {{ $buttonText }}</span>
                <span class="indicator-progress">Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        @endif
    </div>
</div>
