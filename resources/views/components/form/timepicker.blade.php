{{--@php--}}
{{--    $class = empty($class) ? 'col-md-2' : $class;--}}
{{--   date_default_timezone_set('Asia/Jakarta')--}}
{{--@endphp--}}

{{--<div class="form-group">--}}
{{--    <label for="fp-time">{{ $label }} {!! $required ? "<span style='color: red'> *</span>" : "" !!}</label>--}}
{{--    <div class="input-group input-group-merge" >--}}
{{--        <div class="input-group-prepend">--}}
{{--            <span class="input-group-text"><i data-feather='clock'></i></span>--}}
{{--        </div>--}}
{{--    <input type="text"--}}
{{--           id="{{ $name }}"--}}
{{--           name="{{ $name }}"--}}
{{--           {{ $attributes->merge(['class' => 'form-control flatpickr-time text-left flatpickr-input active '.$class]) }}--}}
{{--           value="{{ empty($value) ? empty(old($name)) ? '' : old($name) : $value }}"--}}
{{--           readonly="readonly">--}}
{{--    </div>--}}
{{--    @if($required)--}}
{{--        <div class="invalid-feedback" id="{{ $name }}-error"></div>--}}
{{--    @endif--}}
{{--</div>--}}

@props(["label" => "", "required" => "", "name" => "", "value" => "", "class" => "", "placeholder" => ""])

<div class="form-group mb-5">
    <label class="form-label {!! $required ? "required" : "" !!}">{{ $label }}</label>
    <div class="position-relative d-flex align-items-center">
{{--        <span class="svg-icon svg-icon-2 position-absolute mx-4">--}}
{{--            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
{{--                <path opacity="0.3" d="M20.9 12.9C20.3 12.9 19.9 12.5 19.9 11.9C19.9 11.3 20.3 10.9 20.9 10.9H21.8C21.3 6.2 17.6 2.4 12.9 2V2.9C12.9 3.5 12.5 3.9 11.9 3.9C11.3 3.9 10.9 3.5 10.9 2.9V2C6.19999 2.5 2.4 6.2 2 10.9H2.89999C3.49999 10.9 3.89999 11.3 3.89999 11.9C3.89999 12.5 3.49999 12.9 2.89999 12.9H2C2.5 17.6 6.19999 21.4 10.9 21.8V20.9C10.9 20.3 11.3 19.9 11.9 19.9C12.5 19.9 12.9 20.3 12.9 20.9V21.8C17.6 21.3 21.4 17.6 21.8 12.9H20.9Z" fill="currentColor"/>--}}
{{--                <path d="M16.9 10.9H13.6C13.4 10.6 13.2 10.4 12.9 10.2V5.90002C12.9 5.30002 12.5 4.90002 11.9 4.90002C11.3 4.90002 10.9 5.30002 10.9 5.90002V10.2C10.6 10.4 10.4 10.6 10.2 10.9H9.89999C9.29999 10.9 8.89999 11.3 8.89999 11.9C8.89999 12.5 9.29999 12.9 9.89999 12.9H10.2C10.4 13.2 10.6 13.4 10.9 13.6V13.9C10.9 14.5 11.3 14.9 11.9 14.9C12.5 14.9 12.9 14.5 12.9 13.9V13.6C13.2 13.4 13.4 13.2 13.6 12.9H16.9C17.5 12.9 17.9 12.5 17.9 11.9C17.9 11.3 17.5 10.9 16.9 10.9Z" fill="currentColor"/>--}}
{{--            </svg>--}}
{{--        </span>--}}
        <input
            placeholder="{{ $placeholder }}"
            @error($name)
                {{ $attributes->merge(['class' => 'form-control input-time ps-12 w-50 is-invalid '.$class]) }}
            @else
                {{ $attributes->merge(['class' => 'form-control input-time ps-12 w-50 '.$class]) }}
            @enderror
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, !empty($value) ? $value : '') }}"
        />
    </div>
    @error($name)
    <div class="text-danger"><small>{{ $message }}</small></div>
    @enderror
    @if($required)
        <div class="invalid-feedback" id="{{ $name }}-error"></div>
    @endif
</div>
<style>
    .input-time {
        background-image: url({{ asset('assets/media/icons/duotune/general/gen013.svg') }});
        background-size: 24px 24px;
        background-repeat: no-repeat;
        background-position: 6px !important;
    }
</style>
