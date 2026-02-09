@props(['label' => '', 'required' => '', 'name' => '', 'value' => '', 'isDelete' => true, 'accept' => '', 'max' => ''])
@php
    if($value){
        $classExist = 'd-block';
        $classEmpty = 'd-none';
    }else{
        $classExist = 'd-none';
        $classEmpty = 'd-block';
    }
@endphp
<div class="form-group mb-5">
    <label class="form-label {{ $required ? 'required' : '' }}">{{ $label }} @if($max)<span class="fs-9 text-danger">(Max {{ $max }}MB)</span>@endif </label>
    <div class="mb-3">
        <div id="file-exist-{{ $name }}" class="{{ $classExist }}">
            <a href="storage/{{ $value }}" class="btn btn-icon btn-light-dark w-40px h-40px me-1" download="">
                <i class="fa-solid fa-download"></i>
            </a>
            @if($isDelete)
                <a class="btn btn-icon btn-light-danger w-40px h-40px" onclick="deleteFile('{{ $name }}');">
                    <i class="fa-solid fa-trash"></i>
                </a>
            @endif
        </div>
        @if($isDelete)
            <div id="file-empty-{{ $name }}" class="{{ $classEmpty }}">
                <input class="form-control" type="file" name="{{ $name }}" id="formFile"
                    @if($accept)
                        accept="{{ $accept }}"
                    @endif
                >
            </div>
            <input type="hidden" name="isDelete_{{$name}}" id="isDelete_{{$name}}">
        @endif
    </div>
</div>
@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
<div class="invalid-feedback" id="{{ $name }}-error"></div>
<script>
    function deleteFile(name){
        $('#file-exist-'+name).addClass('d-none');
        $('#file-empty-'+name).removeClass('d-none');
        $('#isDelete_'+name).val('t');
    }
</script>
