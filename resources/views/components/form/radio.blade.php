@props(["label" => "", "required" => "", "password" => "", "name" => "", "placeholder" => "", "value" => "", "class" => "", "datas" => [], "event" => ""])
<div class="form-group mb-5">
    <label class="form-label {{ $required ? 'required' : '' }}">{{ $label }}</label>
    <div class="d-flex">
        @foreach($datas as $key => $values)
            <div class="form-check form-check-custom form-check-solid me-5"
            >
                @php
                    $value = empty($value) && $value != '0' ? $key : $value;
                @endphp
                <input class="form-check-input" type="radio"
                       @if($event)
                           onclick="{{ $event }}"
                       @endif
                       value="{{ $key }}"
                       name="{{ $name }}"
                       id="{{ $name }}_{{ $key }}"
                    {{ $value == $key ? "checked=checked" : "" }}
                />
                <label class="form-check-label" for="{{ $name }}_{{ $key  }}">{{ $values }}</label>
            </div>
        @endforeach
    </div>
</div>



