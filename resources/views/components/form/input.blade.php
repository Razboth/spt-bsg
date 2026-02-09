@props(["label" => "", "required" => "", "password" => "", "name" => "", "placeholder" => "", "value" => "", "class" => "", "nospacing" => "", "numeric" => "", "currency" => "", "error" => ""])
@if($label)
    <div class="form-group mb-5">
        <label class="form-label {!! $required ? "required" : "" !!}">{{ $label }}</label>
        <input type="{{ $password ? 'password' : 'text' }}"
               id="{{ $name }}"
               @error($name)
                   {{ $attributes->merge(['class' => 'form-control is-invalid '.$class]) }}
               @else
                   {{ $attributes->merge(['class' => 'form-control '.$class]) }}
               @enderror
               name="{{ $name }}"
               value="{{ old($name, $currency ? setCurrency($value) : $value) }}"
               placeholder="{{ $placeholder }}"
            {{ $nospacing ? 'onkeyup=setNoSpacing(this);' : '' }}
            {{ $numeric ? 'onkeyup=setNumber(this);' : '' }}
            {{ $currency ? 'onkeyup=setCurrency(this);' : '' }}
            {{ $currency ? 'style=text-align:right;' : '' }}
        />
        @if($required)
            @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="invalid-feedback" id="{{ $name }}-error"></div>
        @endif
    </div>
@else
    <input type="text"
           id="{{ $name }}"
           {{ $attributes->merge(['class' => 'form-control '.$class]) }}
           name="{{ $name }}"
           value="{{ empty($value) ? '' : $value }}"
           placeholder="{{ $placeholder }}"
    />
@endif
