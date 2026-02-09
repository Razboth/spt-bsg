@props(['name', 'value', 'label' => '', 'isModal' => false, 'required' => false])
@if($label)
    <div class="form-group mb-5">
        <label class="form-label {!! $required ? "required" : "" !!}">{{ $label }}</label>
        <div id="{{ $name }}_toolbar"></div>
        <textarea name="{{ $name }}" id="{{ $name }}" hidden></textarea>
        <div id="{{ $name }}_editor">
            {!! $value ?? '' !!}
        </div>
    </div>
@else
    <div id="{{ $name }}_toolbar"></div>
    <textarea name="{{ $name }}" id="{{ $name }}" hidden></textarea>
    <div id="{{ $name }}_editor">
        {!! $value ?? '' !!}
    </div>
@endif
<style>
    #{{ $name }}_editor {
        border: 1px solid #c4c4c4; /* Ganti dengan gaya border yang Anda inginkan */
        padding: 10px; /* Opsional, untuk memberikan ruang di dalam border */
    }
</style>
<script src="{{asset('assets/plugins/custom/ckeditor/ckeditor-document.bundle.js')}}"></script>
<script>
    DecoupledEditor
        .create(document.querySelector('#{{ $name }}_editor'))
        .then(editor => {
            const toolbarContainer = document.querySelector( '#{{ $name }}_toolbar' );

            toolbarContainer.appendChild( editor.ui.view.toolbar.element );
        })
        .catch(error => {
            console.error(error);
        });
    @if($isModal)
        $('#btnSubmit').on('click', function (e) {
            e.preventDefault();
            $('#{{$name}}').val($('#{{ $name }}_editor').html());
        });
    @else
        $('#form-edit').submit(function (e) {
            e.preventDefault();
            $('#{{$name}}').val($('#{{ $name }}_editor').html());
            $(this).unbind('submit').submit();
        });
    @endif
</script>