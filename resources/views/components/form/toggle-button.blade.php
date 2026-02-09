@props(["label" => "", "required" => "", "name" => "", "class" => "", "value" => "", "data" => "", "event" => ""])
<div class="form-group mb-5">
    <label class="form-label {!! $required ? "required" : "" !!}">{{ $label }}</label>
    <div class="col-lg-8 d-flex align-items-center">
        <span class="fw-semibold pe-5 fs-6">Tidak Aktif</span>
        <div class="form-check form-check-solid form-switch form-check-custom fv-row">
            <input class="form-check-input w-45px h-30px" type="checkbox"
                   @if($value == $data) checked @endif
                   name="{{ $name }}"
                   value="{{ $data }}"
                   id="{{ $name }}" />
            <label class="form-check-label" for="{{ $name }}"></label>
        </div>
        <span class="fw-semibold ps-5 fs-6">Aktif</span>
    </div>
</div>
