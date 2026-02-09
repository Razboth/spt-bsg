@props(['name' => '', 'value' => '', 'label' => '', 'max' => ''])
<div class="image-input image-input-outline mb-5 {{ $value ? '' : 'image-input-empty' }} image-input-placeholder {{ $errors->has($name) ? 'is-invalid' : '' }}" data-kt-image-input="true" >
    @if($label)
    <label class="form-label">{{ $label }} @if($max)<span class="fs-9 text-danger">(Max {{ $max }}MB)</span>@endif</label>
    @endif
    <!--begin::Image preview wrapper-->
    <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $value == '' ? asset('assets/media/blank-image.svg') : asset('storage/'.$value) }});"></div>
    <!--end::Image preview wrapper-->

    <!--begin::Edit button-->
    <label class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
           data-kt-image-input-action="change"
           data-bs-toggle="tooltip"
           data-bs-dismiss="click"
           title="Change File">
        <i class="bi bi-pencil-fill fs-7"></i>

        <!--begin::Inputs-->
        <input type="file" name="{{ $name }}" accept=".png, .jpg, .jpeg, .ico" />
        <input type="hidden" name="{{ $name }}_remove" />
        <!--end::Inputs-->
    </label>
    <!--end::Edit button-->

    <!--begin::Cancel button-->
    <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
          data-kt-image-input-action="cancel"
          data-bs-toggle="tooltip"
          data-bs-dismiss="click"
          title="Cancel File">
         <i class="bi bi-x fs-2"></i>
     </span>
    <!--end::Cancel button-->

    <!--begin::Remove button-->
    <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
          data-kt-image-input-action="remove"
          data-bs-toggle="tooltip"
          data-bs-dismiss="click"
          title="Remove File">
         <i class="bi bi-x fs-2"></i>
     </span>
    <!--end::Remove button-->
</div>
@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
<div class="invalid-feedback" id="{{ $name }}-error"></div>
<script>
    $(document).ready(function () {
        KTImageInput.createInstances();
    });
</script>
